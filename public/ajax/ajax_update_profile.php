<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST["action"]) || $_POST["action"] !== "update_profile") {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid action"));
    exit;
}

$user_id = (int)$_SESSION["user_id"];
$name = isset($_POST["name"]) ? mysqli_real_escape_string($conn, $_POST["name"]) : "";
$surname = isset($_POST["surname"]) ? mysqli_real_escape_string($conn, $_POST["surname"]) : "";

$alpha_regex = "/^[a-zA-Z]{2,40}$/";

if (!preg_match($alpha_regex, $name)) {
    http_response_code(201);
    echo json_encode(array("message" => "Invalid name"));
    exit;
}
if (!preg_match($alpha_regex, $surname)) {
    http_response_code(201);
    echo json_encode(array("message" => "Invalid surname"));
    exit;
}

// upload photo (optional)
$photo_name_db = null;

if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
    $tmp = $_FILES["photo"]["tmp_name"];
    $orig = $_FILES["photo"]["name"];

    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed = array("jpg","jpeg","png","gif","webp");

    if (!in_array($ext, $allowed)) {
        http_response_code(201);
        echo json_encode(array("message" => "Only jpg, jpeg, png, gif, webp allowed"));
        exit;
    }

    if ($_FILES["photo"]["size"] > 2 * 1024 * 1024) {
        http_response_code(201);
        echo json_encode(array("message" => "Max photo size is 2MB"));
        exit;
    }

    $newname = "u".$user_id."_".time().".".$ext;
    $upload_dir = __DIR__ . "/../uploads/";
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0777, true);
    }

    if (!move_uploaded_file($tmp, $upload_dir.$newname)) {
        http_response_code(202);
        echo json_encode(array("message" => "Upload failed"));
        exit;
    }

    $photo_name_db = $newname;
}

// update DB
if ($photo_name_db !== null) {
    $q = "UPDATE users SET
            name='".$name."',
            surname='".$surname."',
            profile_photo='".$photo_name_db."',
            updated_at=NOW()
          WHERE id=".$user_id;
} else {
    $q = "UPDATE users SET
            name='".$name."',
            surname='".$surname."',
            updated_at=NOW()
          WHERE id=".$user_id;
}

$u = mysqli_query($conn, $q);
if (!$u) {
    http_response_code(202);
    echo json_encode(array("message" => "DB error", "error" => mysqli_error($conn)));
    exit;
}

http_response_code(200);
echo json_encode(array("message" => "Profile updated"));
exit;
