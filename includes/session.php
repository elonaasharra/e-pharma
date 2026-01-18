<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/remember_me.php";

/** @var mysqli $conn */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 15 minuta (për test mund ta bësh 15 sekonda)
$timeout = 15*60 ;

// 1) Nëse ka user në session dhe ka kaluar timeout → çlogo vetëm session-in
if (isset($_SESSION["user_id"], $_SESSION["last_activity"])) {
    if (time() - (int)$_SESSION["last_activity"] > $timeout) {
        unset($_SESSION["user_id"], $_SESSION["user_email"], $_SESSION["role_id"], $_SESSION["last_activity"]);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}

// 2) Nëse s’ka user në session → provo remember me (vetëm nëse ekziston cookie)
if (!isset($_SESSION["user_id"])) {
    rememberMeAutoLogin($conn); // nëse s’punon, thjesht kthen false
}

// 3) Rifresko aktivitetin vetëm kur është i loguar
if (isset($_SESSION["user_id"])) {
    $_SESSION["last_activity"] = time();
}
