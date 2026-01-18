<?php
require_once __DIR__ . '/../../includes/session.php';
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

$order_id = isset($_POST['order_id']) ? trim((string)$_POST['order_id']) : '';
if ($order_id === '') {
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
$total = (float)cart_get_total($conn, $user_id);
$items = function_exists('cart_get_items') ? cart_get_items($conn, $user_id) : [];

if ($total <= 0 || empty($items)) {
    echo json_encode(['ok' => false, 'error' => 'Cart empty after capture (DB mismatch).', 'capture' => $capture]);
    exit;
}

$provider = 'paypal';
$status = 'Success';

$conn->begin_transaction();

try {
    // 3.1 insert order
    $sql = "INSERT INTO orders (user_id, provider, provider_order_id, currency, amount, status, counted_for_sales)
            VALUES (?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare orders failed: " . $conn->error);
    }

    $amount = number_format($total, 2, '.', '');
    $stmt->bind_param("isssds", $user_id, $provider, $order_id, $currency, $amount, $status);
    $stmt->execute();
    $new_order_id = (int)$conn->insert_id;
    $stmt->close();

    // 3.1.1 Mark counted_for_sales = 1 (idempotency guard)
    $mark = $conn->prepare("UPDATE orders SET counted_for_sales = 1 WHERE id = ? AND counted_for_sales = 0");
    if (!$mark) {
        throw new Exception("Prepare counted_for_sales failed: " . $conn->error);
    }
    $mark->bind_param("i", $new_order_id);
    $mark->execute();
    if ($mark->affected_rows !== 1) {
        $mark->close();
        throw new Exception("Order already counted_for_sales (duplicate capture?)");
    }
    $mark->close();

    // 3.2 insert order_items
    $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total)
                VALUES (?, ?, ?, ?, ?, ?)";
    $stmtItem = $conn->prepare($sqlItem);
    if (!$stmtItem) {
        throw new Exception("Prepare order_items failed: " . $conn->error);
    }

    foreach ($items as $it) {
        $pid   = (int)$it['product_id'];
        $pname = (string)$it['product_name'];
        $uprice = (float)$it['unit_price'];
        $qty   = (int)$it['quantity'];
        $line  = (float)$it['line_total'];

        $stmtItem->bind_param("iisdid", $new_order_id, $pid, $pname, $uprice, $qty, $line);
        $stmtItem->execute();
    }
    $stmtItem->close();

    // 3.3 Update products: decrement stock + increment sold_count (atomik)
    $sqlStock = "UPDATE products
                 SET stock = stock - ?, sold_count = sold_count + ?
                 WHERE id = ? AND stock >= ?";
    $stmtStock = $conn->prepare($sqlStock);
    if (!$stmtStock) {
        throw new Exception("Prepare stock/sold update failed: " . $conn->error);
    }

    foreach ($items as $it) {
        $pid = (int)$it['product_id'];
        $qty = (int)$it['quantity'];

        $stmtStock->bind_param("iiii", $qty, $qty, $pid, $qty);
        $stmtStock->execute();

        if ($stmtStock->affected_rows !== 1) {
            throw new Exception("Not enough stock (or product missing) for product_id=$pid, qty=$qty");
        }
    }
    $stmtStock->close();

    // 3.4 shëno shportën si checked_out
    $stmtCart = $conn->prepare("UPDATE carts SET status='checked_out' WHERE user_id=? AND status='active'");
    if (!$stmtCart) {
        throw new Exception("Prepare carts update failed: " . $conn->error);
    }
    $stmtCart->bind_param("i", $user_id);
    $stmtCart->execute();
    $stmtCart->close();

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['ok' => false, 'error' => 'DB save failed: ' . $e->getMessage(), 'capture' => $capture]);
    exit;
}

echo json_encode([
    'ok' => true,
    'message' => 'Payment completed, saved, stock updated and sold_count incremented.',
    'order_db_id' => $new_order_id,
    'provider_order_id' => $order_id,
    'amount' => number_format($total, 2, '.', ''),
    'currency' => $currency,
    'capture' => $capture
]);
