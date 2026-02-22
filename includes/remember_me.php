<?php
require_once __DIR__ . "/db.php";

function generateRememberToken(): string {      //funksion i cili gjeneron token per cookies
    return bin2hex(random_bytes(32));
}

function rememberMeAutoLogin(mysqli $conn): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION["user_id"])) {  //nese user_id eshte i loguar dmth qe ka userid ne session dhe skemi nevoje te kontrollojm cookie
        return true;
    }

    if (empty($_COOKIE["remember_me"])) { //nese cookie remember me esht bosh ose nuk ekziston ska si te behet autologin
        return false;
    }

    $token = (string)$_COOKIE["remember_me"];
    $token_hash = hash("sha256", $token);

    $sql = "SELECT rt.id AS rt_id, rt.user_id, u.email, u.role_id
            FROM remember_tokens rt
            JOIN users u ON u.id = rt.user_id
            WHERE rt.token_hash = ?
              AND rt.expires_at > NOW()
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;//nese premare deshton dmth

    $stmt->bind_param("s", $token_hash);//mbush ? me token hash
    $stmt->execute();//ekzekutohet query ne databaz
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;

    if (!$row) {
        return false; //nese nuk u gjet token ose ka skaduar , nuk behet autologin dhe kthehet false
    }

    $_SESSION["user_id"] = (int)$row["user_id"];
    $_SESSION["user_email"] = (string)$row["email"];
    $_SESSION["role_id"] = (int)$row["role_id"];
    $_SESSION["last_activity"] = time();

    // $upd = $conn->prepare("UPDATE remember_tokens SET last_used_at = NOW() WHERE id = ?");
    // if ($upd) { $rid = (int)$row["rt_id"]; $upd->bind_param("i", $rid); $upd->execute(); }

    return true;
}
