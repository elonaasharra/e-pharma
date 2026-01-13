<?php
//require_once __DIR__ . '/../../includes/admin_auth.php';
//require_once __DIR__ . '/../../includes/db.php';
///** @var mysqli $conn */
//
//$error = "";
//$success = "";
//
//if ($_SERVER["REQUEST_METHOD"] === "POST") {
//    $name = trim($_POST["name"] ?? "");
//    $surname = trim($_POST["surname"] ?? "");
//    $email = trim($_POST["email"] ?? "");
//    $password = $_POST["password"] ?? "";
//    $role_id = (int)($_POST["role_id"] ?? 1);
//    $is_verified = (int)($_POST["is_verified"] ?? 0);
//
//    if ($name === "" || $surname === "" || $email === "" || $password === "") {
//        $error = "All fields are required.";
//    } else {
//        // Check email unique
//        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
//        mysqli_stmt_bind_param($stmt, "s", $email);
//        mysqli_stmt_execute($stmt);
//        $check = mysqli_stmt_get_result($stmt);
//
//        if ($check && mysqli_num_rows($check) > 0) {
//            $error = "Email already exists.";
//        } else {
//            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
//            $verify_token = md5(uniqid(mt_rand(), true));
//            $created_at = date("Y-m-d H:i:s");
//
//            $stmt2 = mysqli_prepare($conn, "INSERT INTO users (name, surname, email, hashed_password, role_id, verify_token, is_verified, created_at)
//                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
//            mysqli_stmt_bind_param($stmt2, "ssssisis", $name, $surname, $email, $hashed_password, $role_id, $verify_token, $is_verified, $created_at);
//
//            if (mysqli_stmt_execute($stmt2)) {
//                header("Location: /e-pharma/public/admin/users.php");
//                exit;
//            } else {
//                $error = "DB insert failed: " . mysqli_error($conn);
//            }
//        }
//    }
//}
//?>
<!--<!doctype html>-->
<!--<html>-->
<!--<head><meta charset="utf-8"><title>Add User</title></head>-->
<!--<body>-->
<!--<h2>Add new user</h2>-->
<!---->
<!--<p><a href="/e-pharma/public/admin/users.php">← Back to Users</a></p>-->
<!---->
<?php //if ($error): ?><!--<p style="color:red;">--><?php //echo htmlspecialchars($error); ?><!--</p>--><?php //endif; ?>
<!---->
<!--<form method="post">-->
<!--    <p>Name: <input name="name" required></p>-->
<!--    <p>Surname: <input name="surname" required></p>-->
<!--    <p>Email: <input name="email" type="email" required></p>-->
<!--    <p>Password: <input name="password" type="password" required></p>-->
<!---->
<!--    <p>Role:-->
<!--        <select name="role_id">-->
<!--            <option value="1">User (1)</option>-->
<!--            <option value="2">Admin (2)</option>-->
<!--        </select>-->
<!--    </p>-->
<!---->
<!--    <p>Verified:-->
<!--        <select name="is_verified">-->
<!--            <option value="0">No</option>-->
<!--            <option value="1">Yes</option>-->
<!--        </select>-->
<!--    </p>-->
<!---->
<!--    <button type="submit">Create</button>-->
<!--</form>-->
<!---->
<!--</body>-->
<!--</html>-->
