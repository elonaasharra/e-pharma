<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/cart.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

// Lejo vetëm POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Metodë e pavlefshme."]);
    exit;
}

// Duhet të jetë i loguar
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "error" => "Nuk je i loguar."]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "product_id i pavlefshëm."]);
    exit;
}

$result = cart_add_item($conn, $user_id, $product_id, $qty);
if (!$result["ok"]) {
    http_response_code(400);
    echo json_encode($result);
    exit;
}

// kthe edhe numrin e artikujve për header
$count = cart_count_items($conn, $user_id);

echo json_encode([
    "ok" => true,
    "cart_count" => $count
]);
