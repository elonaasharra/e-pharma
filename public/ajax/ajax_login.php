<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/remember_me.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8'); // i thot browserit dhe js qe pergjigja qe kthen serveri eshte ne json
session_start();   // nis nje session , pra krijohet nje hapsir ku ruhen te dhena per perdoruesin

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {                 // kontrollon qe kerkesa te vije vetem me metoden post , pra perdoret vetem me ajax nga post , nuk mund ta hapim direkt nga browseri
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit;
}

//$_POST eshte nje array speciale ne PHP qe mban te dhenat qe dergohen nga formulari ose AJAX me metoden POST.

if (!isset($_POST["action"]) || $_POST["action"] !== "login") {  // siguron qe file te perdoret vetem per loginin
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
    exit;
}

$email = isset($_POST["email"]) ? trim($_POST["email"]) : ""; //Ky rresht kontrollon nëse ekziston vlera email në $_POST; nëse po, e merr dhe heq hapësirat bosh me trim(), ndërsa nëse jo, vendos vlerë bosh.
$password = isset($_POST["password"]) ? (string)$_POST["password"] : ""; // nese nk ekziston vendoset vlera bosh
$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "unknown";// perdoret per te ruajtur ip e perdoruesit per siguri dhe logim
$remember_me = isset($_POST["remember_me"]) ? (int)$_POST["remember_me"] : 0;

$email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

//validim backend

if (!preg_match($email_regex, $email)) {//  preg_match() është funksion në PHP që kontrollon nëse një tekst përputhet me një rregull (regex).
    http_response_code(422);
    echo json_encode(["message" => "Invalid email format"]);
    exit;
}

if ($password === "") {
    http_response_code(422);
    echo json_encode(["message" => "Password can not be empty"]);
    exit;
}

//Nese numri i tentativave te gabuara esht >=7 e bllokon per 30 min qe ta provoj serish me ate user
// $stmt esht nje prepared statemend per ekzekutimin e nje query ne menyr te sigurt , pra objekt qe mban querin e pergatitur

$stmt = $conn->prepare("
    SELECT COUNT(*) AS cnt
    FROM login_attempts
    WHERE email = ?
      AND success = 0
      AND attempt_time >= (NOW() - INTERVAL 30 MINUTE)
");


if (!$stmt) { // kontrollon nese pergatitja e queryt dhe ekzekutimi ne databaz ka deshtuar
    http_response_code(500);
    echo json_encode(["message" => "DB error", "error" => $conn->error]);
    exit;
}

$stmt->bind_param("s", $email); // s esht type dhe dmth string
$stmt->execute();                           // ekzekuton querin
$res = $stmt->get_result();                  // merr rezultatin e querit
$row_lock = $res ? $res->fetch_assoc() : null; // nese ka rezultat nga db merr rreshtin nese jo vendos null
$failed_count = $row_lock ? (int)$row_lock["cnt"] : 0;

if ($failed_count >= 7) {
    http_response_code(429);
    echo json_encode(["message" => "Too many failed attempts. Try again after 30 minutes."]);
    exit;
}

//gjej userin nga emaili

$stmt = $conn->prepare("
    SELECT id, name, surname, email, hashed_password, is_verified, role_id
    FROM users
    WHERE email = ?
    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["message" => "DB error", "error" => $conn->error]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;

if (!$user) {
    // log failed attempt (user not found)
    $stmt2 = $conn->prepare("
        INSERT INTO login_attempts (user_id, email, ip_address, attempt_time, success)
        VALUES (NULL, ?, ?, NOW(), 0)
    ");

    if ($stmt2) {
        $stmt2->bind_param("ss", $email, $ip);
        $stmt2->execute();
    }

    http_response_code(401);
    echo json_encode(["message" => "Invalid credentials"]);
    exit;
}

if ((int)$user["is_verified"] !== 1) {
    http_response_code(403);
    echo json_encode(["message" => "Please verify your email first"]);
    exit;
}

$ok = password_verify($password, (string)$user["hashed_password"]);

if (!$ok) {
    // log failed attempt
    $stmt2 = $conn->prepare("
        INSERT INTO login_attempts (user_id, email, ip_address, attempt_time, success)
        VALUES (?, ?, ?, NOW(), 0)
    ");

    if ($stmt2) {
        $uid = (int)$user["id"];
        $stmt2->bind_param("iss", $uid, $email, $ip);
        $stmt2->execute();
    }

    http_response_code(401);
    echo json_encode(["message" => "Invalid credentials"]);
    exit;
}
// succes , log succes

$stmt2 = $conn->prepare("
    INSERT INTO login_attempts (user_id, email, ip_address, attempt_time, success)
    VALUES (?, ?, ?, NOW(), 1)
");

if ($stmt2) {
    $uid = (int)$user["id"];
    $stmt2->bind_param("iss", $uid, $email, $ip);
    $stmt2->execute();
}

// reset failed attempts in last 30 min (optional)
$stmt3 = $conn->prepare("
    DELETE FROM login_attempts
    WHERE email = ?
      AND success = 0
      AND attempt_time >= (NOW() - INTERVAL 30 MINUTE)
");
if ($stmt3) {
    $stmt3->bind_param("s", $email);
    $stmt3->execute();
}

// session

$_SESSION["user_id"] = (int)$user["id"];
$_SESSION["user_email"] = (string)$user["email"];
$_SESSION["role_id"] = (int)$user["role_id"];
$_SESSION["last_activity"] = time();


//  REMEMBER ME (token ne DB + cookie)
//  Rotation: në çdo login show/refresh token

if ($remember_me === 1) {
    $token = generateRememberToken();
    $token_hash = hash("sha256", $token);
    $expires_at = date("Y-m-d H:i:s", time() + 60 * 60 * 24 * 30);

    // fshi token-at e vjetër
    $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
    if ($stmt) {
        $uid = (int)$user["id"];
        $stmt->bind_param("i", $uid);
        $stmt->execute();
    }

    // ruaj token-in e ri
    $stmt = $conn->prepare("
        INSERT INTO remember_tokens (user_id, token_hash, expires_at, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["message" => "DB error", "error" => $conn->error]);
        exit;
    }
    $uid = (int)$user["id"];
    $stmt->bind_param("iss", $uid, $token_hash, $expires_at);
    $stmt->execute();

    // cookie flags
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');//Kontrollon nëse faqja po hapet me HTTPS. Nëse po, cookie vendoset si “secure”.
    setcookie("remember_me", $token, [ //Vendos cookie remember_me me token-in për 30 ditë.
        "expires"  => time() + 60 * 60 * 24 * 30,
        "path"     => "/",
        "secure"   => $secure,
        "httponly" => true,
        "samesite" => "Lax",//ul rrezikun e sulmeve CSRF
    ]);
} else {
    // fshi token-at nga DB kur nuk zgjidhet remember me
    $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
    if ($stmt) {
        $uid = (int)$user["id"];
        $stmt->bind_param("i", $uid);
        $stmt->execute();
    }

    // fshi cookie
    setcookie("remember_me", "", [
        "expires"  => time() - 3600,
        "path"     => "/",
        "samesite" => "Lax",
    ]);
}

// bejm redirek bazuar te roli

$location = "/e-pharma/public/user/profile.php";

if ((int)$user["role_id"] === 2) {
    $location = "/e-pharma/public/admin/dashboard.php";
}

http_response_code(200);
echo json_encode(["message" => "Login success", "location" => $location]);
exit;
