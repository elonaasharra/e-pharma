<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/login/header.php';
/** @var mysqli $conn */

$user_id = (int)$_SESSION['user_id'];
$order_id = (int)($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Order ID mungon.</div></div>";
    require_once __DIR__ . '/../includes/login/footer.php';
    exit;
}

// Admin = role_id 2 (siç e ke ti). Admin mund të shohë çdo faturë.
$is_admin = ((int)($_SESSION['role_id'] ?? 0) === 2);

// Merr porosinë
if ($is_admin) {
    $sql = "SELECT * FROM orders WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
} else {
    $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
}

$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();

if (!$order) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Fatura nuk u gjet ose s’ke akses.</div></div>";
    require_once __DIR__ . '/../includes/login/footer.php';
    exit;
}

// Merr item-at e porosisë
$sql = "SELECT oi.*, p.name AS product_name
        FROM order_items oi
        LEFT JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id = ?
        ORDER BY oi.id ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();
$items = [];
while ($row = $res->fetch_assoc()) $items[] = $row;

// Totali nga DB (prefero order.total_amount nëse e ke)
$total_amount = isset($order['total_amount']) ? (float)$order['total_amount'] : 0.0;
?>

<div class="container mt-4" id="invoice">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h3 class="mb-1">Faturë</h3>
            <div class="text-muted">E-Pharma</div>
        </div>
        <div class="text-end">
            <div><strong>Nr. Fature:</strong> #<?php echo (int)$order['id']; ?></div>
            <div><strong>Data:</strong> <?php echo htmlspecialchars($order['created_at'] ?? date('Y-m-d H:i')); ?></div>
            <div><strong>Status:</strong> <?php echo htmlspecialchars($order['status'] ?? ''); ?></div>
        </div>
    </div>

    <hr>

    <div class="mb-3">
        <div><strong>Klienti (User ID):</strong> <?php echo (int)$order['user_id']; ?></div>
        <?php if (!empty($order['paypal_order_id'])): ?>
            <div><strong>PayPal Order:</strong> <?php echo htmlspecialchars($order['paypal_order_id']); ?></div>
        <?php endif; ?>
    </div>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
        <tr>
            <th>Produkt</th>
            <th class="text-end">Çmimi</th>
            <th class="text-center">Sasia</th>
            <th class="text-end">Nëntotali</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $it):
            $price = (float)($it['unit_price'] ?? 0);
            $qty   = (int)($it['quantity'] ?? 0);
            $line  = $price * $qty;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($it['product_name'] ?? ('Product #' . (int)$it['product_id'])); ?></td>
                <td class="text-end"><?php echo number_format($price, 2, '.', ''); ?> €</td>
                <td class="text-center"><?php echo $qty; ?></td>
                <td class="text-end"><?php echo number_format($line, 2, '.', ''); ?> €</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="3" class="text-end">TOTAL</th>
            <th class="text-end"><?php echo number_format($total_amount, 2, '.', ''); ?> €</th>
        </tr>
        </tfoot>
    </table>

    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()">Print / Save as PDF</button>
        <a class="btn btn-primary" href="/e-pharma/public/index.php">Kthehu</a>
    </div>
</div>

<style>
    @media print {
        nav, header, footer, .btn { display: none !important; }
        #invoice { margin-top: 0 !important; }
    }
</style>

<?php require_once __DIR__ . '/../includes/login/footer.php'; ?>
