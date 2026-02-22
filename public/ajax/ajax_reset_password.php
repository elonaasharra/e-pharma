<?php
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST["action"]) || $_POST["action"] != "reset_password") {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid action"));
    exit;
}

$token = isset($_POST["token"]) ? $_POST["token"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if (empty($token)) {
    http_response_code(201);
    echo json_encode(array("message" => "Invalid token"));
    exit;
}

if (strlen($password) < 6) {
    http_response_code(201);
    echo json_encode(array("message" => "Password must be at least 6 characters"));
    exit;
}

$token_hash = hash("sha256", $token);

// gjej reset request valid
$q = "SELECT id, user_id
      FROM password_resets
      WHERE token_hash = '".$token_hash."'
        AND used_at IS NULL
        AND expires_at > NOW()
      LIMIT 1";
$r = mysqli_query($conn, $q);

if (!$r) {
    http_response_code(202);
    echo json_encode(array("message" => "DB error", "error" => mysqli_error($conn)));
    exit;
}

$row = mysqli_fetch_assoc($r);
if (!$row) {
    http_response_code(201);
    echo json_encode(array("message" => "Reset link is invalid or expired"));
    exit;
}

$reset_id = (int)$row["id"];
$user_id = (int)$row["user_id"];

// update password te users
$hashed = password_hash($password, PASSWORD_BCRYPT);

$u = "UPDATE users SET
        hashed_password = '".$hashed."',
        password = '".$hashed."'
      WHERE id = ".$user_id;
$ru = mysqli_query($conn, $u);

if (!$ru) {
    http_response_code(202);
    echo json_encode(array("message" => "DB error", "error" => mysqli_error($conn)));
    exit;
}

// sheno reset si i perdorur
mysqli_query($conn, "UPDATE password_resets SET used_at = NOW() WHERE id = ".$reset_id);

// opsionale: fshi remember_tokens qe user te logoj prap
mysqli_query($conn, "DELETE FROM remember_tokens WHERE user_id = ".$user_id);

http_response_code(200);
echo json_encode(array(
    "message" => "Password updated successfully. Please login.",
    "location" => "/e-pharma/public/login.php"
));
exit;
