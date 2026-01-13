<?php
//require_once __DIR__ . '/../../includes/admin_auth.php';
//require_once __DIR__ . '/../../includes/db.php';
///** @var mysqli $conn */
//
//$sql = "SELECT id, name, surname, email, role_id, is_verified, created_at
//        FROM users
//        ORDER BY id DESC";
//$res = mysqli_query($conn, $sql);
//if (!$res) die("DB error: " . mysqli_error($conn));
//?>
<!--<!doctype html>-->
<!--<html>-->
<!--<head><meta charset="utf-8"><title>Admin - Users</title></head>-->
<!--<body>-->
<!--<h2>Users (Admin)</h2>-->
<!---->
<!--<p>-->
<!--    <a href="/e-pharma/public/admin/add_user.php">+ Add new user</a> |-->
<!--    <a href="/e-pharma/public/admin/dashboard.php">Dashboard</a> |-->
<!--    <a href="/e-pharma/public/logout.php">Logout</a>-->
<!--</p>-->
<!---->
<!--<table border="1" cellpadding="8" cellspacing="0">-->
<!--    <tr>-->
<!--        <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Verified</th><th>Created</th><th>Actions</th>-->
<!--    </tr>-->
<!---->
<!--    --><?php //while($u = mysqli_fetch_assoc($res)): ?>
<!--        <tr>-->
<!--            <td>--><?php //echo (int)$u["id"]; ?><!--</td>-->
<!--            <td>--><?php //echo htmlspecialchars($u["name"] . " " . $u["surname"]); ?><!--</td>-->
<!--            <td>--><?php //echo htmlspecialchars($u["email"]); ?><!--</td>-->
<!--            <td>--><?php //echo (int)$u["role_id"]; ?><!--</td>-->
<!--            <td>--><?php //echo ((int)$u["is_verified"] === 1) ? "YES" : "NO"; ?><!--</td>-->
<!--            <td>--><?php //echo htmlspecialchars($u["created_at"]); ?><!--</td>-->
<!--            <td>-->
<!--                <a href="/e-pharma/public/admin/edit_user.php?id=--><?php //echo (int)$u["id"]; ?><!--">Edit</a>-->
<!--                |-->
<!--                <form action="/e-pharma/public/admin/delete_user.php" method="post" style="display:inline;"-->
<!--                      onsubmit="return confirm('Are you sure you want to delete this user?');">-->
<!--                    <input type="hidden" name="id" value="--><?php //echo (int)$u["id"]; ?><!--">-->
<!--                    <button type="submit">Delete</button>-->
<!--                </form>-->
<!--            </td>-->
<!--        </tr>-->
<!--    --><?php //endwhile; ?>
<!--</table>-->
<!---->
<!--</body>-->
<!--</html>-->
