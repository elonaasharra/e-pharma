<?php
require_once __DIR__ . '/../includes/db.php';
/** @var mysqli $conn */

$token = isset($_GET["token"]) ? mysqli_real_escape_string($conn, $_GET["token"]) : "";

if (empty($token)) {
    die("Invalid token");
}

// gjej user-in me token
$q = "SELECT id, is_verified FROM users WHERE verify_token = '".$token."' LIMIT 1";
$r = mysqli_query($conn, $q);//rezultati qe kthen query nga databaza

if (!$r) {
    die("DB error: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($r);//merr nje rresht nga rezulati i databazes dhe e kthen si array assoc ,pra array me celesa

if (!$user) {
    die("Token not found");
}

if ((int)$user["is_verified"] === 1) {
    die("Already verified");
}

$uid = (int)$user["id"];

// update: verified + fshi token
$u = "UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ".$uid;//token fshihet qe te mos perdoret me
$ru = mysqli_query($conn, $u);//dergon queryn ne db dhe ekzekuton update

if (!$ru) {
    die("DB error: " . mysqli_error($conn));
}
//line 9 shiko te login.php
header("Location: /e-pharma/public/login.php?verified=1");
exit;
