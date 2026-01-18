<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/remember_me.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit;
}

if (!isset($_POST["action"]) || $_POST["action"] !== "login") {
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
    exit;
}

$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? (string)$_POST["password"] : "";
$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "unknown";
$remember_me = isset($_POST["remember_me"]) ? (int)$_POST["remember_me"] : 0;

$email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

if (!preg_match($email_regex, $email)) {
    http_response_code(422);
    echo json_encode(["message" => "Invalid email format"]);
    exit;
}

if ($password === "") {
    http_response_code(422);
    echo json_encode(["message" => "Password can not be empty"]);
    exit;
}

/**
 * CHECK LOCK: 7 failed attempts in last 30 minutes
 */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS cnt
    FROM login_attempts
    WHERE email = ?
      AND success = 0
      AND attempt_time >= (NOW() - INTERVAL 30 MINUTE)
");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["message" => "DB error", "error" => $conn->error]);
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$row_lock = $res ? $res->fetch_assoc() : null;
$failed_count = $row_lock ? (int)$row_lock["cnt"] : 0;

if ($failed_count >= 7) {
    http_response_code(429);
    echo json_encode(["message" => "Too many failed attempts. Try again after 30 minutes."]);
    exit;
}

/**
 * Find user by email
 */
$stmt = $conn->prepare("
    SELECT id, name, surname, email, hashed_password, is_verified, role_id
    FROM users
    WHERE email = ?
    LIMIT 1
");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["message" => "DB error", "error" => $conn->error]);
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;

if (!$user) {
    // log failed attempt (user not found)
    $stmt2 = $conn->prepare("
        INSERT INTO login_attempts (user_id, email, ip_address, attempt_time, success)
        VALUES (NULL, ?, ?, NOW(), 0)
    ");
    if ($stmt2) {
        $stmt2->bind_param("ss", $email, $ip);
        $stmt2->execute();
    }

    http_response_code(401);
    echo json_encode(["message" => "Invalid credentials"]);
    exit;
}

if ((int)$user["is_verified"] !== 1) {
    http_response_code(403);
    echo json_encode(["message" => "Please verify your email first"]);
    exit;
}

$ok = password_verify($password, (string)$user["hashed_password"]);
if (!$ok) {
    // log failed attempt
    $stmt2 = $conn->prepare("
        INSERT INTO login_attempts (user_id, email, ip_address, attempt_time, success)
        VALUES (?, ?, ?, NOW(), 0)
    ");
    if ($stmt2) {
        $uid = (int)$user["id"];
        $stmt2->bind_param("iss", $uid, $email, $ip);
        $stmt2->execute();
    }

    http_response_code(401);
    echo json_encode(["message" => "Invalid credentials"]);
    exit;
}

/**
 * SUCCESS: log success
 */
$stmt2 = $conn->prepare("
    INSERT INTO login_attempts (user_id, email, ip_address, attempt_time, success)
    VALUES (?, ?, ?, NOW(), 1)
");
if ($stmt2) {
    $uid = (int)$user["id"];
    $stmt2->bind_param("iss", $uid, $email, $ip);
    $stmt2->execute();
}

// reset failed attempts in last 30 min (optional)
$stmt3 = $conn->prepare("
    DELETE FROM login_attempts
    WHERE email = ?
      AND success = 0
      AND attempt_time >= (NOW() - INTERVAL 30 MINUTE)
");
if ($stmt3) {
    $stmt3->bind_param("s", $email);
    $stmt3->execute();
}

/**
 * SESSION
 */
$_SESSION["user_id"] = (int)$user["id"];
$_SESSION["user_email"] = (string)$user["email"];
$_SESSION["role_id"] = (int)$user["role_id"];
$_SESSION["last_activity"] = time();

/**
 * REMEMBER ME (token në DB + cookie)
 * Rotation: në çdo login show/refresh token
 */
if ($remember_me === 1) {
    $token = generateRememberToken();
    $token_hash = hash("sha256", $token);
    $expires_at = date("Y-m-d H:i:s", time() + 60 * 60 * 24 * 30);

    // fshi token-at e vjetër
    $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
    if ($stmt) {
        $uid = (int)$user["id"];
        $stmt->bind_param("i", $uid);
        $stmt->execute();
    }

    // ruaj token-in e ri
    $stmt = $conn->prepare("
        INSERT INTO remember_tokens (user_id, token_hash, expires_at, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["message" => "DB error", "error" => $conn->error]);
        exit;
    }
    $uid = (int)$user["id"];
    $stmt->bind_param("iss", $uid, $token_hash, $expires_at);
    $stmt->execute();

    // cookie flags
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie("remember_me", $token, [
        "expires"  => time() + 60 * 60 * 24 * 30,
        "path"     => "/",
        "secure"   => $secure,
        "httponly" => true,
        "samesite" => "Lax",
    ]);
} else {
    // fshi token-at nga DB
    $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
    if ($stmt) {
        $uid = (int)$user["id"];
        $stmt->bind_param("i", $uid);
        $stmt->execute();
    }

    // fshi cookie
    setcookie("remember_me", "", [
        "expires"  => time() - 3600,
        "path"     => "/",
        "samesite" => "Lax",
    ]);
}

/**
 * role-based redirect (1=user, 2=admin)
 */
$location = "/e-pharma/public/user/profile.php";
if ((int)$user["role_id"] === 2) {
    $location = "/e-pharma/public/admin/dashboard.php";
}

http_response_code(200);
echo json_encode(["message" => "Login success", "location" => $location]);
exit;
