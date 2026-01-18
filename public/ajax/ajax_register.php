<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/mail.php';

/** @var mysqli $conn */
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST["action"]) || $_POST["action"] !== "register") {
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
    exit;
}

$name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
$surname = isset($_POST["surname"]) ? trim($_POST["surname"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? (string)$_POST["password"] : "";
$confirm_password = isset($_POST["confirm_password"]) ? (string)$_POST["confirm_password"] : "";

$email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
$alpha_regex = "/^[a-zA-Z]{3,40}$/";

// VALIDIME
if (!preg_match($alpha_regex, $name)) {
    http_response_code(422);
    echo json_encode(["message" => "Name must contain only letters (min 3)."]);
    exit;
}
if (!preg_match($alpha_regex, $surname)) {
    http_response_code(422);
    echo json_encode(["message" => "Surname must contain only letters (min 3)."]);
    exit;
}
if (!preg_match($email_regex, $email)) {
    http_response_code(422);
    echo json_encode(["message" => "Invalid email format."]);
    exit;
}
if ($password === "" || strlen($password) < 8) {
    http_response_code(422);
    echo json_encode(["message" => "Password must be at least 8 characters."]);
    exit;
}
if ($password !== $confirm_password) {
    http_response_code(422);
    echo json_encode(["message" => "Passwords do not match."]);
    exit;
}

// CHECK EMAIL (prepared)
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["message" => "DB error", "error" => $conn->error]);
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["message" => "There is a user with that E-Mail."]);
    exit;
}

// INSERT
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$verify_token = md5(uniqid((string)mt_rand(), true));
$role_id = 1;
$is_verified = 0;

$stmt = $conn->prepare("
    INSERT INTO users (name, surname, email, hashed_password, role_id, verify_token, is_verified, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["message" => "DB error", "error" => $conn->error]);
    exit;
}
$stmt->bind_param("ssssisi", $name, $surname, $email, $hashed_password, $role_id, $verify_token, $is_verified);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["message" => "DB insert failed", "error" => $stmt->error]);
    exit;
}

// EMAIL
$data = [
    "user_email" => $email,
    "token" => $verify_token
];
sendEmail($data);

// SUCCESS
http_response_code(200);
echo json_encode([
    "message" => "Registered successfully. Check your email to verify your account.",
    "location" => "/e-pharma/public/login.php"
]);
exit;
