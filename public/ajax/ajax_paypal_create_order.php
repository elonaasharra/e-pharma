<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/cart.php';
require_once __DIR__ . '/../../includes/paypal_client.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = (int)$_SESSION["user_id"];

// Llogarit totalin nga DB (shporta aktive)
$total = cart_get_total($conn, $user_id);
if ($total <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Cart is empty']);
    exit;
}

$currency = 'EUR'; // ose 'ALL' sipas projektit

$return_url = 'http://localhost/e-pharma/public/paypal_return.php';
$cancel_url = 'http://localhost/e-pharma/public/paypal_cancel.php';

list($ok, $err, $data) = paypal_create_order($total, $currency, $return_url, $cancel_url);

if (!$ok) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $err, 'details' => $data]);
    exit;
}

echo json_encode([
    'ok' => true,
    'order_id' => $data['id'],          // kjo i duhet PayPal Buttons
    'total' => number_format($total, 2, '.', '')
]);

