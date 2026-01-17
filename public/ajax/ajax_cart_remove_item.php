<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

$user_id = (int)($_SESSION['user_id'] ?? 0);
$product_id = (int)($_POST['product_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'Not logged in']);
    exit;
}
if ($product_id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'Invalid product']);
    exit;
}

// gjej cart aktiv
$sql = "SELECT id FROM carts WHERE user_id=? AND status='active' LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) { echo json_encode(['ok'=>false,'error'=>'DB error']); exit; }

$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
    echo json_encode(['ok' => true]); // s’ka cart aktiv
    exit;
}

$cart_id = (int)$row['id'];

// fshi item
$sql = "DELETE FROM cart_items WHERE cart_id=? AND product_id=?";
$stmt = $conn->prepare($sql);
if (!$stmt) { echo json_encode(['ok'=>false,'error'=>'DB error']); exit; }

$stmt->bind_param("ii", $cart_id, $product_id);
$stmt->execute();

echo json_encode(['ok' => true]);
