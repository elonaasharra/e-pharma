<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/remember_me.php';

/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_POST["action"]) || $_POST["action"] != "login") {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid action"));
    exit;
}

$email = isset($_POST["email"]) ? mysqli_real_escape_string($conn, $_POST["email"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "unknown";
//marrim rememeber me  post
$remember_me = isset($_POST["remember_me"]) ? (int)$_POST["remember_me"] : 0;

$email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

// backend validation

if (!preg_match($email_regex, $email)) {
    http_response_code(201);
    echo json_encode(array("message" => "Invalid email format"));
    exit;
}

if (empty($password)) {
    http_response_code(201);
    echo json_encode(array("message" => "Password can not be empty"));
    exit;
}

/* CHECK LOCK: 7 failed attempts in last 30 minutes*/
$check_lock_q = "SELECT COUNT(*) AS cnt
                 FROM login_attempts
                 WHERE email = '".$email."'
                   AND success = 0
                   AND attempt_time >= (NOW() - INTERVAL 30 MINUTE)";
$check_lock_r = mysqli_query($conn, $check_lock_q);

if (!$check_lock_r) {
    http_response_code(202);
    echo json_encode(array("message" => "DB error", "error" => mysqli_error($conn)));
    exit;
}

$row_lock = mysqli_fetch_assoc($check_lock_r);
$failed_count = (int)$row_lock["cnt"];

if ($failed_count >= 7) {
    http_response_code(201);
    echo json_encode(array("message" => "Too many failed attempts. Try again after 30 minutes."));
    exit;
}

/*Find user by email*/

$user_q = "SELECT id, name, surname, email, hashed_password, is_verified, role_id
           FROM users
           WHERE email = '".$email."'
           LIMIT 1";
$user_r = mysqli_query($conn, $user_q);

if (!$user_r) {
    http_response_code(202);
    echo json_encode(array("message" => "DB error", "error" => mysqli_error($conn)));
    exit;
}

$user = mysqli_fetch_assoc($user_r);

// if user not found -> log failed attempt

if (!$user) {
    mysqli_query($conn, "INSERT INTO login_attempts SET
        user_id = NULL,
        email = '".$email."',
        ip_address = '".$ip."',
        attempt_time = NOW(),
        success = 0
    ");
    http_response_code(201);
    echo json_encode(array("message" => "Invalid credentials"));
    exit;
}

// if not verified
if ((int)$user["is_verified"] !== 1) {
    http_response_code(201);
    echo json_encode(array("message" => "Please verify your email first"));
    exit;
}

// verify password
$ok = password_verify($password, $user["hashed_password"]);

if (!$ok) {
    mysqli_query($conn, "INSERT INTO login_attempts SET
        user_id = ".$user["id"].",
        email = '".$email."',
        ip_address = '".$ip."',
        attempt_time = NOW(),
        success = 0
    ");
    http_response_code(201);
    echo json_encode(array("message" => "Invalid credentials"));
    exit;
}

// SUCCESS: log success + reset failed attempts (optional clean)

mysqli_query($conn, "INSERT INTO login_attempts SET
    user_id = ".$user["id"].",
    email = '".$email."',
    ip_address = '".$ip."',
    attempt_time = NOW(),
    success = 1
");

// reset count: fshij failed-at e 30 min të fundit (që të mos bllokohet më kot)
mysqli_query($conn, "DELETE FROM login_attempts
    WHERE email = '".$email."'
      AND success = 0
      AND attempt_time >= (NOW() - INTERVAL 30 MINUTE)
");

//SESSION

$_SESSION["user_id"] = (int)$user["id"];
$_SESSION["user_email"] = $user["email"];
$_SESSION["role_id"] = (int)$user["role_id"];
$_SESSION["last_activity"] = time();

//REMEMBER ME (cookie + db)

if ($remember_me === 1) {
    $token = generateRememberToken();
    $token_hash = hash("sha256", $token);
    $expires_at = date("Y-m-d H:i:s", time() + 60*60*24*30); // 30 ditë

    // opsionale: fshi token-at e vjetër të këtij useri
    mysqli_query($conn, "DELETE FROM remember_tokens WHERE user_id = ".$user["id"]);

    mysqli_query($conn, "INSERT INTO remember_tokens SET
        user_id = ".$user["id"].",
        token_hash = '".$token_hash."',
        expires_at = '".$expires_at."',
        created_at = '".date("Y-m-d H:i:s")."'
    ");

    setcookie("remember_me", $token, time() + 60*60*24*30, "/");
}

// role-based redirect (1=user, 2=admin sipas roles table)
$location = "/e-pharma/public/user/profile.php";
if ((int)$user["role_id"] === 2) {
    $location = "/e-pharma/public/admin/users.php";
}


http_response_code(200);
echo json_encode(array("message" => "Login success", "location" => $location));
exit;
