<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/cart.php';
require_once __DIR__ . '/../../includes/paypal_client.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$order_id = isset($_POST['order_id']) ? trim($_POST['order_id']) : null;
if (!$order_id) {
    echo json_encode(['ok' => false, 'error' => 'Missing order_id']);
    exit;
}

// 1) CAPTURE në PayPal
list($ok, $err, $capture) = paypal_capture_order($order_id);

if (!$ok) {
    echo json_encode(['ok' => false, 'error' => $err, 'details' => $capture]);
    exit;
}

// 2) Merr total + items nga shporta (server-side)
$currency = 'EUR';
$total = cart_get_total($conn, $user_id);
$items = function_exists('cart_get_items') ? cart_get_items($conn, $user_id) : [];

if ($total <= 0 || empty($items)) {
    // Pagesa u kap, por shporta në DB s’ka items -> diçka s’përputhet
    echo json_encode(['ok' => false, 'error' => 'Cart empty after capture (DB mismatch).', 'capture' => $capture]);
    exit;
}

$provider = 'paypal';
$status = 'Success';

// 3) Ruaje order + items në DB (si profesori)
$conn->begin_transaction();

try {
    // 3.1 insert order (mos e fus dy herë të njëjtin provider_order_id)
    $sql = "INSERT INTO orders (user_id, provider, provider_order_id, currency, amount, status)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Prepare orders failed: " . $conn->error);

    $amount = number_format((float)$total, 2, '.', '');
    $stmt->bind_param("isssds", $user_id, $provider, $order_id, $currency, $amount, $status);
    $stmt->execute();
    $new_order_id = (int)$conn->insert_id;

    // 3.2 insert order_items
    $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total)
                VALUES (?, ?, ?, ?, ?, ?)";
    $stmtItem = $conn->prepare($sqlItem);
    if (!$stmtItem) throw new Exception("Prepare order_items failed: " . $conn->error);

    foreach ($items as $it) {
        $pid = (int)$it['product_id'];
        $pname = (string)$it['product_name'];
        $uprice = (float)$it['unit_price'];
        $qty = (int)$it['quantity'];
        $line = (float)$it['line_total'];

        $stmtItem->bind_param("iisdid", $new_order_id, $pid, $pname, $uprice, $qty, $line);
        $stmtItem->execute();
    }

    // 3.3 shëno shportën si checked_out
    $sql = "UPDATE carts SET status='checked_out' WHERE user_id=? AND status='active'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Prepare carts update failed: " . $conn->error);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['ok' => false, 'error' => 'DB save failed: ' . $e->getMessage(), 'capture' => $capture]);
    exit;
}

echo json_encode([
    'ok' => true,
    'message' => 'Payment completed and saved.',
    'order_db_id' => $new_order_id,
    'provider_order_id' => $order_id,
    'amount' => number_format((float)$total, 2, '.', ''),
    'currency' => $currency,
    'capture' => $capture
]);
