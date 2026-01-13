<?php
//require_once __DIR__ . '/../../includes/admin_auth.php';
//require_once __DIR__ . '/../../includes/db.php';
///** @var mysqli $conn */
//
//$id = (int)($_GET["id"] ?? 0);
//if ($id <= 0) { header("Location: /e-pharma/public/admin/users.php"); exit; }
//
//$error = "";
//
//if ($_SERVER["REQUEST_METHOD"] === "POST") {
//    $name = trim($_POST["name"] ?? "");
//    $surname = trim($_POST["surname"] ?? "");
//    $email = trim($_POST["email"] ?? "");
//    $role_id = (int)($_POST["role_id"] ?? 1);
//    $is_verified = (int)($_POST["is_verified"] ?? 0);
//    $new_password = $_POST["new_password"] ?? "";
//
//    if ($name === "" || $surname === "" || $email === "") {
//        $error = "Name, surname, email are required.";
//    } else {
//        // email unique check (except current user)
//        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1");
//        mysqli_stmt_bind_param($stmt, "si", $email, $id);
//        mysqli_stmt_execute($stmt);
//        $check = mysqli_stmt_get_result($stmt);
//
//        if ($check && mysqli_num_rows($check) > 0) {
//            $error = "Email already used by another user.";
//        } else {
//            if ($new_password !== "") {
//                $hashed = password_hash($new_password, PASSWORD_BCRYPT);
//                $stmtU = mysqli_prepare($conn, "UPDATE users SET name=?, surname=?, email=?, role_id=?, is_verified=?, hashed_password=? WHERE id=? LIMIT 1");
//                mysqli_stmt_bind_param($stmtU, "sssiisi", $name, $surname, $email, $role_id, $is_verified, $hashed, $id);
//            } else {
//                $stmtU = mysqli_prepare($conn, "UPDATE users SET name=?, surname=?, email=?, role_id=?, is_verified=? WHERE id=? LIMIT 1");
//                mysqli_stmt_bind_param($stmtU, "sssiii", $name, $surname, $email, $role_id, $is_verified, $id);
//            }
//
//            if (!mysqli_stmt_execute($stmtU)) {
//                $error = "DB update failed: " . mysqli_error($conn);
//            } else {
//                header("Location: /e-pharma/public/admin/users.php");
//                exit;
//            }
//        }
//    }
//}
//
//// fetch user for form
//$stmt3 = mysqli_prepare($conn, "SELECT id, name, surname, email, role_id, is_verified FROM users WHERE id=? LIMIT 1");
//mysqli_stmt_bind_param($stmt3, "i", $id);
//mysqli_stmt_execute($stmt3);
//$res = mysqli_stmt_get_result($stmt3);
//$user = $res ? mysqli_fetch_assoc($res) : null;
//if (!$user) { header("Location: /e-pharma/public/admin/users.php"); exit; }
//?>
<!--<!doctype html>-->
<!--<html>-->
<!--<head><meta charset="utf-8"><title>Edit User</title></head>-->
<!--<body>-->
<!--<h2>Edit user #--><?php //echo (int)$user["id"]; ?><!--</h2>-->
<!--<p><a href="/e-pharma/public/admin/users.php">← Back</a></p>-->
<!---->
<?php //if ($error): ?><!--<p style="color:red;">--><?php //echo htmlspecialchars($error); ?><!--</p>--><?php //endif; ?>
<!---->
<!--<form method="post">-->
<!--    <p>Name: <input name="name" value="--><?php //echo htmlspecialchars($user["name"]); ?><!--" required></p>-->
<!--    <p>Surname: <input name="surname" value="--><?php //echo htmlspecialchars($user["surname"]); ?><!--" required></p>-->
<!--    <p>Email: <input name="email" type="email" value="--><?php //echo htmlspecialchars($user["email"]); ?><!--" required></p>-->
<!---->
<!--    <p>Role:-->
<!--        <select name="role_id">-->
<!--            <option value="1" --><?php //if((int)$user["role_id"]===1) echo "selected"; ?><!-->User (1)</option>-->
<!--            <option value="2" --><?php //if((int)$user["role_id"]===2) echo "selected"; ?><!-->Admin (2)</option>-->
<!--        </select>-->
<!--    </p>-->
<!---->
<!--    <p>Verified:-->
<!--        <select name="is_verified">-->
<!--            <option value="0" --><?php //if((int)$user["is_verified"]===0) echo "selected"; ?><!-->No</option>-->
<!--            <option value="1" --><?php //if((int)$user["is_verified"]===1) echo "selected"; ?><!-->Yes</option>-->
<!--        </select>-->
<!--    </p>-->
<!---->
<!--    <p>New password (optional): <input name="new_password" type="password"></p>-->
<!---->
<!--    <button type="submit">Save</button>-->
<!--</form>-->
<!---->
<!--</body>-->
<!--</html>-->
