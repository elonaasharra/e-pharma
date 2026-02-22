<?php
require_once __DIR__ . '/auth.php';

// nese sesht admin
if (!isset($_SESSION["role_id"]) || (int)$_SESSION["role_id"] !== 2) {
    header("Location: /e-pharma/public/user/profile.php");
    exit;
}
