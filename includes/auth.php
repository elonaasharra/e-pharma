<?php
//require_once __DIR__ . "/db.php";
require_once __DIR__ . "/session.php";
//require_once __DIR__ . "/remember_me.php";
/** @var mysqli $conn */

//rememberMeAutoLogin($conn);

if (!isset($_SESSION["user_id"])) {
    header("Location: /e-pharma/public/login.php");
    exit;
}
