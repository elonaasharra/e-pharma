<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/mail.php';

/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST["action"]) || $_POST["action"] != "register") {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid action"));
    exit;
}

// 1) get data
$name = isset($_POST["name"]) ? mysqli_real_escape_string($conn, $_POST["name"]) : "";
$surname = isset($_POST["surname"]) ? mysqli_real_escape_string($conn, $_POST["surname"]) : "";
$email = isset($_POST["email"]) ? mysqli_real_escape_string($conn, $_POST["email"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";

$email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
$alpha_regex = "/^[a-zA-Z]{3,40}$/";

// 2) Data Validation
if (!preg_match($alpha_regex, $name)) {
    http_response_code(201);
    echo json_encode(array("message" => "Name must be alphabumeric at least 3 letters."));
    exit;
}
if (!preg_match($alpha_regex, $surname)) {
    http_response_code(201);
    echo json_encode(array("message" => "Surname must be alphabumeric at least 3 letters."));
    exit;
}
if (!preg_match($email_regex, $email)) {
    http_response_code(201);
    echo json_encode(array("message" => "E-Mail format is not allowed"));
    exit;
}
if (empty($password) || strlen($password) < 8) {
    http_response_code(201);
    echo json_encode(array("message" => "Password must be at least 8 characters"));
    exit;
}
if ($password != $confirm_password) {
    http_response_code(201);
    echo json_encode(array("message" => "Confirm password must be equal to password"));
    exit;
}

// 3) Check if email exists
$query_check = "SELECT id FROM users WHERE email = '".$email."' LIMIT 1";
$result_check = mysqli_query($conn, $query_check);

if (!$result_check) {
    http_response_code(202);
    echo json_encode(array(
        "message" => "There is an error on Database",
        "error" => mysqli_error($conn),
        "error_number" => mysqli_errno($conn)
    ));
    exit;
}

if (mysqli_num_rows($result_check) > 0) {
    http_response_code(201);
    echo json_encode(array("message" => "There is a user with that E-Mail"));
    exit;
}

// 4) Insert user (kolonat e tua)
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$verify_token = md5(uniqid(mt_rand(), true));
$role_id = "1";
$is_verified = 0;

$query_insert = "INSERT INTO users SET
    name = '".$name."',
    surname = '".$surname."',
    email = '".$email."',
    hashed_password = '".$hashed_password."',
    role_id = '".$role_id."',
    verify_token = '".$verify_token."',
    is_verified = '".$is_verified."',
    created_at = '".date("Y-m-d H:i:s")."'";

$result_insert = mysqli_query($conn, $query_insert);

if (!$result_insert) {
    http_response_code(202);
    echo json_encode(array(
        "message" => "There is an error on Database",
        "error" => mysqli_error($conn),
        "error_number" => mysqli_errno($conn)
    ));
    exit;
}

$data = array(
    "user_email" => $email,
    "token" => $verify_token
);

sendEmail($data);

//  success (pa email akoma)
http_response_code(200);
echo json_encode(array(
    "message" => "User registered successfully (DB insert OK). Next: email verification.",
    "location" => "login.php"
));
exit;
