<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/cart.php';

// Header për user të loguar
require_once __DIR__ . '/../includes/login/header.php';

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$cart_count = $user_id ? cart_count_items($conn, $user_id) : 0;

// Merr produktet aktive
$sql = "SELECT id, name, description, price, stock, image
        FROM products
        WHERE is_active = 1
        ORDER BY created_at DESC";
$res = $conn->query($sql);
?>
<!doctype html>
<html lang="sq">
<head>
    <meta charset="utf-8">
    <title>Produktet</title>
</head>
<body>

<h2>Produktet</h2>

<!-- (opsionale) shfaq numrin e shportës edhe këtu -->
<p>Shporta: <strong id="cart-count"><?php echo (int)$cart_count; ?></strong></p>

<?php if ($res && $res->num_rows > 0): ?>
    <?php while ($p = $res->fetch_assoc()): ?>
        <div style="border:1px solid #ddd; padding:12px; margin:10px 0;">
            <h3><?php echo htmlspecialchars($p['name']); ?></h3>

            <?php if (!empty($p['description'])): ?>
                <p><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
            <?php endif; ?>

            <p><strong>Çmimi:</strong> <?php echo number_format((float)$p['price'], 2); ?> ALL</p>
            <p><strong>Stok:</strong> <?php echo (int)$p['stock']; ?></p>

            <button
                type="button"
                data-add-to-cart
                data-product-id="<?php echo (int)$p['id']; ?>"
            >
                Shto në shportë
            </button>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Nuk ka produkte për momentin.</p>
<?php endif; ?>

<script src="/e-pharma/public/assets/js/cart.js"></script>
</body>
</html>
