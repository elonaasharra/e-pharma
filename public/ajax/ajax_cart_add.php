<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/cart.php';
require_once __DIR__ . '/../../includes/remember_me.php';

/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'ok' => false,
        'error' => 'LOGIN_REQUIRED',
        'message' => 'Ju lutem logohuni për të shtuar produkte në shportë.',
        'login_url' => '/e-pharma/public/login.php'
    ]);
    exit;
}

// Lejo vetëm POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "INVALID_METHOD"]);
    exit;
}

// Duhet të jetë i loguar
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "error" => "NOT_LOGGED_IN"]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($qty < 1) $qty = 1;

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "INVALID_PRODUCT_ID"]);
    exit;
}

$result = cart_add_item($conn, $user_id, $product_id, $qty);

if (!$result["ok"]) {
    // Nëse error-i ka të bëjë me stokun, vendos kode standarde
    $msg = isset($result["error"]) ? (string)$result["error"] : "UNKNOWN_ERROR";

    $isStockProblem = (
        stripos($msg, "stok") !== false ||
        stripos($msg, "stock") !== false
    );

    http_response_code(400);
    echo json_encode([
        "ok" => false,
        "error" => $isStockProblem ? "OUT_OF_STOCK" : "ADD_FAILED",
        "message" => $msg
    ]);
    exit;
}

// kthe edhe numrin e artikujve për header
$count = cart_count_items($conn, $user_id);

echo json_encode([
    "ok" => true,
    "cart_count" => $count
]);
