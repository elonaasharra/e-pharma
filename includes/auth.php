<?php
require_once __DIR__ . "/session.php";
/** @var mysqli $conn */

if (!isset($_SESSION["user_id"])) {
    header("Location: /e-pharma/public/login.php");
    exit;
}
