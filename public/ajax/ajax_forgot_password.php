<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/mail.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST["action"]) || $_POST["action"] != "forgot_password") {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid action"));
    exit;
}

$email = isset($_POST["email"]) ? mysqli_real_escape_string($conn, $_POST["email"]) : "";
$email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

if (!preg_match($email_regex, $email)) {
    http_response_code(201);
    echo json_encode(array("message" => "Invalid email format"));
    exit;
}

// gjej user
$q = "SELECT id, email, is_verified FROM users WHERE email = '".$email."' LIMIT 1";
$r = mysqli_query($conn, $q);

if (!$r) {
    http_response_code(202);
    echo json_encode(array("message" => "DB error", "error" => mysqli_error($conn)));
    exit;
}

$user = mysqli_fetch_assoc($r);

// për siguri: mos trego nëse ekziston apo jo emaili
if (!$user) {
    http_response_code(200);
    echo json_encode(array("message" => "If the email exists, a reset link has been sent."));
    exit;
}

// opsionale: vetëm për user të verifikuar
if ((int)$user["is_verified"] !== 1) {
    http_response_code(201);
    echo json_encode(array("message" => "Please verify your email first."));
    exit;
}

// krijo token (kompatibël me PHP të vjetër)
$token = md5(uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true));
$token_hash = hash("sha256", $token);
$expires_at = date("Y-m-d H:i:s", time() + 60*30); // 30 minuta

// fshi reset-at e vjetër (opsionale)
mysqli_query($conn, "DELETE FROM password_resets WHERE user_id = ".$user["id"]." AND used_at IS NULL");

// ruaje në DB
$ins = "INSERT INTO password_resets SET
    user_id = ".$user["id"].",
    token_hash = '".$token_hash."',
    expires_at = '".$expires_at."',
    created_at = '".date("Y-m-d H:i:s")."',
    used_at = NULL";

$ri = mysqli_query($conn, $ins);
if (!$ri) {
    http_response_code(202);
    echo json_encode(array("message" => "DB error", "error" => mysqli_error($conn)));
    exit;
}

// dërgo email
$link = "http://localhost/e-pharma/public/reset_password.php?token=" . urlencode($token);

$data = array(
    "user_email" => $user["email"],
    "token" => $token,
    "link" => $link,
    "type" => "reset_password"
);

// përdor mail.php – nëse s’e ke të përshtatur, e dërgojmë me Body të thjeshtë
$sent = sendEmail(array(
    "user_email" => $user["email"],
    "token" => $token,
    "link" => $link
));

http_response_code(200);
echo json_encode(array("message" => "Reset link sent (check your email)."));
exit;
