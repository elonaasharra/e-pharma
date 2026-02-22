<?php
require_once __DIR__ . "/db.php"; // vetem nje here
require_once __DIR__ . "/remember_me.php";

/** @var mysqli $conn */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 15 minuta zgjatja e sesionit
$timeout = 15*60 ;

//  Nese ka user ne session dhe ka kaluar timeout , çlogo vetem session-in
if (isset($_SESSION["user_id"], $_SESSION["last_activity"])) {
    if (time() - (int)$_SESSION["last_activity"] > $timeout) {
        unset($_SESSION["user_id"], $_SESSION["user_email"], $_SESSION["role_id"], $_SESSION["last_activity"]);//useri nuk konsiderohet me i loguar

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true); // per te mbrojtur nga sulmet
        }
    }
}

// Nese s’ka user ne session → provo remember me
if (!isset($_SESSION["user_id"])) {
    rememberMeAutoLogin($conn); // nese s’punon, thjesht kthen false

}

//  Rifresko aktivitetin vetem kur eshte i loguar
if (isset($_SESSION["user_id"])) {
    $_SESSION["last_activity"] = time();
}
