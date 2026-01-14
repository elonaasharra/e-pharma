<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

if (!isset($_POST["action"]) || $_POST["action"] !== "update_profile") {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action"]);
    exit;
}

// ✅ Auth check
$user_id = (int)($_SESSION["user_id"] ?? 0);
if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Not authenticated"]);
    exit;
}

$name = trim($_POST["name"] ?? "");
$surname = trim($_POST["surname"] ?? "");

$alpha_regex = "/^[a-zA-Z]{2,40}$/";

if (!preg_match($alpha_regex, $name)) {
    http_response_code(422);
    echo json_encode(["status" => "error", "message" => "Invalid name"]);
    exit;
}
if (!preg_match($alpha_regex, $surname)) {
    http_response_code(422);
    echo json_encode(["status" => "error", "message" => "Invalid surname"]);
    exit;
}

// upload photo (optional)
$photo_name_db = null;
$upload_dir = __DIR__ . "/../uploads/";

if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Upload error"]);
        exit;
    }

    $tmp = $_FILES["photo"]["tmp_name"];
    $orig = $_FILES["photo"]["name"];

    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed = ["jpg","jpeg","png","gif","webp"];

    if (!in_array($ext, $allowed, true)) {
        http_response_code(422);
        echo json_encode(["status" => "error", "message" => "Only jpg, jpeg, png, gif, webp allowed"]);
        exit;
    }

    if (($_FILES["photo"]["size"] ?? 0) > 2 * 1024 * 1024) {
        http_response_code(422);
        echo json_encode(["status" => "error", "message" => "Max photo size is 2MB"]);
        exit;
    }

    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0777, true);
    }

    $newname = "u".$user_id."_".time().".".$ext;

    if (!move_uploaded_file($tmp, $upload_dir.$newname)) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Upload failed"]);
        exit;
    }

    $photo_name_db = $newname;

    // (opsionale) fshi foton e vjetër
    $stmtOld = mysqli_prepare($conn, "SELECT profile_photo FROM users WHERE id = ? LIMIT 1");
    if ($stmtOld) {
        mysqli_stmt_bind_param($stmtOld, "i", $user_id);
        mysqli_stmt_execute($stmtOld);
        $resOld = mysqli_stmt_get_result($stmtOld);
        $old = $resOld ? mysqli_fetch_assoc($resOld) : null;
        mysqli_stmt_close($stmtOld);

        if (!empty($old["profile_photo"])) {
            $oldPath = $upload_dir . $old["profile_photo"];
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }
    }
}

// ✅ update DB me prepared statements
if ($photo_name_db !== null) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE users SET name=?, surname=?, profile_photo=?, updated_at=NOW() WHERE id=?"
    );
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Prepare failed"]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "sssi", $name, $surname, $photo_name_db, $user_id);
} else {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE users SET name=?, surname=?, updated_at=NOW() WHERE id=?"
    );
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Prepare failed"]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "ssi", $name, $surname, $user_id);
}

$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    $err = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "DB error", "error" => $err]);
    exit;
}

mysqli_stmt_close($stmt);

http_response_code(200);
echo json_encode(["status" => "success", "message" => "Profile updated"]);
exit;
