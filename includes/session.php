
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = 15*60; // 15 minuta

if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: /e-pharma/public/login.php");
    exit;
}

$_SESSION["last_activity"] = time();
