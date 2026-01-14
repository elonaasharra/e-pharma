<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

set_error_handler(function ($severity, $message, $file, $line) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "PHP Error: $message",
        "file" => $file,
        "line" => $line
    ]);
    exit;
});

set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Exception: " . $e->getMessage()
    ]);
    exit;
});

require_once __DIR__ . '/../../includes/mail.php';

require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

function out($status, $message, $code = 200, $extra = []) {
    http_response_code($code);
    echo json_encode(array_merge([
        "status" => $status,
        "message" => $message
    ], $extra));
    exit;
}

$action = $_POST["action"] ?? "";
if ($action === "") out("error", "Missing action", 400);

$alpha = "/^[a-zA-Z]{2,40}$/";

// -------------------- DELETE USER --------------------
if ($action === "delete_user") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id <= 0) out("error", "Invalid user id", 422);

    $me = (int)($_SESSION["user_id"] ?? 0);
    if ($id === $me) out("error", "You cannot delete yourself", 422);

    // fshi foton nga disk (opsionale)
    $stmtP = mysqli_prepare($conn, "SELECT profile_photo FROM users WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($stmtP, "i", $id);
    mysqli_stmt_execute($stmtP);
    $resP = mysqli_stmt_get_result($stmtP);
    $rowP = $resP ? mysqli_fetch_assoc($resP) : null;
    mysqli_stmt_close($stmtP);

    if (!empty($rowP["profile_photo"])) {
        $path = __DIR__ . "/../uploads/" . $rowP["profile_photo"];
        if (is_file($path)) @unlink($path);
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
    if (!$stmt) out("error", "Prepare failed", 500);

    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    $err = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) out("error", "DB error", 500, ["error" => $err]);

    out("success", "User deleted");
}

// -------------------- ADD USER --------------------
if ($action === "add_user") {
    $name = trim($_POST["name"] ?? "");
    $surname = trim($_POST["surname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role_id = (int)($_POST["role_id"] ?? 1);

    if (!preg_match($alpha, $name)) out("error", "Invalid name", 422);
    if (!preg_match($alpha, $surname)) out("error", "Invalid surname", 422);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) out("error", "Invalid email", 422);
    if (strlen($password) < 6) out("error", "Password must be at least 6 characters", 422);
    if (!in_array($role_id, [1,2], true)) out("error", "Invalid role", 422);

    // email unique
    $stmtC = mysqli_prepare($conn, "SELECT id FROM users WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($stmtC, "s", $email);
    mysqli_stmt_execute($stmtC);
    $resC = mysqli_stmt_get_result($stmtC);
    $exists = $resC ? mysqli_fetch_assoc($resC) : null;
    mysqli_stmt_close($stmtC);

    if ($exists) out("error", "Email already exists", 422);

    // ✅ hash për login (si te ajax_login.php)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // admin e krijon direkt verified (mund ta bësh 0 nëse do)
    $is_verified = 1;
    $verify_token = null;

    // ⚠️ ke edhe kolonat password dhe hashed_password.
    // Ne do plotësojmë hashed_password; password e lëmë NULL/bosh.
    $stmt = mysqli_prepare($conn, "
        INSERT INTO users
            (name, surname, email, password, hashed_password, role_id, verify_token, is_verified, created_at, updated_at)
        VALUES
            (?, ?, ?, NULL, ?, ?, ?, ?, NOW(), NOW())
    ");
    if (!$stmt) out("error", "Prepare failed", 500);

    mysqli_stmt_bind_param($stmt, "sssisii",
        $name, $surname, $email, $hashed_password, $role_id, $verify_token, $is_verified
    );

    $ok = mysqli_stmt_execute($stmt);
    $err = mysqli_stmt_error($stmt);
    $newId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if (!$ok) out("error", "DB error", 500, ["error" => $err]);

    out("success", "User created", 200, ["id" => $newId]);
}

// -------------------- UPDATE USER --------------------
// -------------------- UPDATE USER --------------------
if ($action === "update_user") {
    require_once __DIR__ . '/../../includes/mail.php';

    $id = (int)($_POST["id"] ?? 0);
    $name = trim($_POST["name"] ?? "");
    $surname = trim($_POST["surname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role_id = (int)($_POST["role_id"] ?? 1);

    // NEW PASSWORD (optional)
    $new_password = trim($_POST["new_password"] ?? "");
    $change_pass = ($new_password !== "");

    if ($id <= 0) out("error", "Invalid user id", 422);
    if (!preg_match($alpha, $name)) out("error", "Invalid name", 422);
    if (!preg_match($alpha, $surname)) out("error", "Invalid surname", 422);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) out("error", "Invalid email", 422);
    if (!in_array($role_id, [1,2], true)) out("error", "Invalid role", 422);

    if ($change_pass && strlen($new_password) < 8) {
        out("error", "New password must be at least 8 characters", 422);
    }

    $new_hash = null;
    if ($change_pass) {
        $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
    }

    // 1) Merr email-in aktual nga DB (që ta krahasojmë)
    $stmtOld = mysqli_prepare($conn, "SELECT email FROM users WHERE id=? LIMIT 1");
    if (!$stmtOld) out("error", "Prepare failed", 500);

    mysqli_stmt_bind_param($stmtOld, "i", $id);
    mysqli_stmt_execute($stmtOld);
    $resOld = mysqli_stmt_get_result($stmtOld);
    $oldRow = $resOld ? mysqli_fetch_assoc($resOld) : null;
    mysqli_stmt_close($stmtOld);

    if (!$oldRow) out("error", "User not found", 404);

    $old_email = $oldRow["email"];
    $email_changed = (strtolower($old_email) !== strtolower($email));

    // 2) Email unique (përveç vetes)
    $stmtC = mysqli_prepare($conn, "SELECT id FROM users WHERE email=? AND id<>? LIMIT 1");
    if (!$stmtC) out("error", "Prepare failed", 500);

    mysqli_stmt_bind_param($stmtC, "si", $email, $id);
    mysqli_stmt_execute($stmtC);
    $resC = mysqli_stmt_get_result($stmtC);
    $exists = $resC ? mysqli_fetch_assoc($resC) : null;
    mysqli_stmt_close($stmtC);

    if ($exists) out("error", "Email already in use", 422);

    // 3) Nëse email u ndryshua → bëje unverified + token i ri + dërgo email
    if ($email_changed) {
        $new_token = md5(uniqid(mt_rand(), true));
        $is_verified = 0;

        if ($change_pass) {
            $stmt = mysqli_prepare($conn, "
                UPDATE users
                SET name=?, surname=?, email=?, role_id=?, is_verified=?, verify_token=?, hashed_password=?, updated_at=NOW()
                WHERE id=?
            ");
            if (!$stmt) out("error", "Prepare failed", 500);

            mysqli_stmt_bind_param(
                $stmt,
                "sssiissi",
                $name, $surname, $email, $role_id, $is_verified, $new_token, $new_hash, $id
            );
        } else {
            $stmt = mysqli_prepare($conn, "
                UPDATE users
                SET name=?, surname=?, email=?, role_id=?, is_verified=?, verify_token=?, updated_at=NOW()
                WHERE id=?
            ");
            if (!$stmt) out("error", "Prepare failed", 500);

            mysqli_stmt_bind_param(
                $stmt,
                "sssiisi",
                $name, $surname, $email, $role_id, $is_verified, $new_token, $id
            );
        }

        $ok = mysqli_stmt_execute($stmt);
        $err = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);

        if (!$ok) out("error", "DB error", 500, ["error" => $err]);

        // fshi remember_tokens (që të mos mbetet i loguar me cookie të vjetër)
        $stmtT = mysqli_prepare($conn, "DELETE FROM remember_tokens WHERE user_id=?");
        if ($stmtT) {
            mysqli_stmt_bind_param($stmtT, "i", $id);
            mysqli_stmt_execute($stmtT);
            mysqli_stmt_close($stmtT);
        }

        // dërgo email verifikimi në emailin e ri (mos e rrëzo AJAX nëse dështon)
        try {
            $data = ["user_email" => $email, "token" => $new_token];
            sendEmail($data);
            out("success", "User updated. Verification email sent to the new address.");
        } catch (Throwable $e) {
            out("success", "User updated, but verification email could not be sent (check mail settings).");
        }
    }

    // 4) Nëse email s’u ndryshua → update normal
    if ($change_pass) {
        $stmt = mysqli_prepare($conn, "
            UPDATE users
            SET name=?, surname=?, email=?, role_id=?, hashed_password=?, updated_at=NOW()
            WHERE id=?
        ");
        if (!$stmt) out("error", "Prepare failed", 500);

        mysqli_stmt_bind_param(
            $stmt,
            "sssisi",
            $name, $surname, $email, $role_id, $new_hash, $id
        );
    } else {
        $stmt = mysqli_prepare($conn, "
            UPDATE users
            SET name=?, surname=?, email=?, role_id=?, updated_at=NOW()
            WHERE id=?
        ");
        if (!$stmt) out("error", "Prepare failed", 500);

        mysqli_stmt_bind_param(
            $stmt,
            "sssii",
            $name, $surname, $email, $role_id, $id
        );
    }

    $ok = mysqli_stmt_execute($stmt);
    $err = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) out("error", "DB error", 500, ["error" => $err]);

    // nëse u ndryshua password, pastro remember_tokens
    if ($change_pass) {
        $stmtT = mysqli_prepare($conn, "DELETE FROM remember_tokens WHERE user_id=?");
        if ($stmtT) {
            mysqli_stmt_bind_param($stmtT, "i", $id);
            mysqli_stmt_execute($stmtT);
            mysqli_stmt_close($stmtT);
        }
    }

    out("success", "User updated");
}
