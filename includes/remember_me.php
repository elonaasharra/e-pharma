<?php
// includes/remember_me.php
require_once __DIR__ . "/db.php";

function generateRememberToken() {
    return md5(uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true));
}

function rememberMeAutoLogin($conn) {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // nëse je i loguar, s’ka nevojë
    if (isset($_SESSION["user_id"])) {
        return;
    }

    if (!isset($_COOKIE["remember_me"]) || empty($_COOKIE["remember_me"])) {
        return;
    }

    $token = $_COOKIE["remember_me"];
    $token_hash = hash("sha256", $token);

    // gjej token në DB + user-in
    $q = "SELECT rt.id AS rt_id, rt.user_id, u.email, u.role_id
          FROM remember_tokens rt
          INNER JOIN users u ON u.id = rt.user_id
          WHERE rt.token_hash = '".$token_hash."'
            AND rt.expires_at > NOW()
          LIMIT 1";
    $r = mysqli_query($conn, $q);
    if (!$r) return;

    $row = mysqli_fetch_assoc($r);
    if (!$row) return;

    // login automatik
    $_SESSION["user_id"] = (int)$row["user_id"];
    $_SESSION["user_email"] = $row["email"];
    $_SESSION["role_id"] = (int)$row["role_id"];
    $_SESSION["last_activity"] = time();

    // token rotation (rrit sigurinë)
    $new_token = generateRememberToken();
    $new_hash = hash("sha256", $new_token);
    $expires = date("Y-m-d H:i:s", time() + 60*60*24*30); // 30 ditë

    mysqli_query($conn, "UPDATE remember_tokens SET
        token_hash = '".$new_hash."',
        expires_at = '".$expires."'
        WHERE id = ".$row["rt_id"]."
    ");

    setcookie("remember_me", $new_token, time() + 60*60*24*30, "/");
}
