<?php
//require_once __DIR__ . '/../../includes/admin_auth.php';
//require_once __DIR__ . '/../../includes/db.php';
///** @var mysqli $conn */
//
//if ($_SERVER["REQUEST_METHOD"] !== "POST") {
//    header("Location: /e-pharma/public/admin/users.php");
//    exit;
//}
//
//$id = (int)($_POST["id"] ?? 0);
//if ($id <= 0) {
//    header("Location: /e-pharma/public/admin/users.php");
//    exit;
//}
//
//// (Opsionale) mos lejo adminin të fshijë veten
//if ($id === (int)$_SESSION["user_id"]) {
//    header("Location: /e-pharma/public/admin/users.php");
//    exit;
//}
//
//$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=? LIMIT 1");
//mysqli_stmt_bind_param($stmt, "i", $id);
//mysqli_stmt_execute($stmt);
//
//header("Location: /e-pharma/public/admin/users.php");
//exit;
