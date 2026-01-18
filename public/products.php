<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/remember_me.php';
require_once __DIR__ . '/../includes/cart.php';
/** @var mysqli $conn */

// Auto-login nëse ekziston remember_me cookie
rememberMeAutoLogin($conn);

/**
 * HEADER sipas login
 */
if (isset($_SESSION["user_id"])) {
    require_once __DIR__ . '/../includes/login/header.php';
} else {
    require_once __DIR__ . '/../includes/no_login/header.php';
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$cart_count = $user_id ? cart_count_items($conn, $user_id) : 0;

/**
 * KATEGORITË (duhet të përputhen me Home)
 */
$categories = [
        'dermokozmetike' => 'Dermo Cosmetic',
        'baby'           => 'Mom & Kids',
        'suplemente'     => 'Suplemente',
        'higjiene'       => 'Skincare',
        'haircare'       => 'Haircare',
        'oralcare'       => 'Oralcare',
];

/**
 * Lexo kategorinë nga URL
 * products.php?cat=dermokozmetike
 */
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : '';
if ($cat !== '' && !isset($categories[$cat])) {
    $cat = '';
}
//  KETE HOQA
//
//$res = null;
//
//if ($cat !== '') {
//    $sql = "SELECT id, name, description, price, stock, image
//            FROM products
//            WHERE is_active = 1 AND category_slug = ?
//            ORDER BY created_at DESC";
//    $stmt = $conn->prepare($sql);
//    $stmt->bind_param("s", $cat);
//    $stmt->execute();
//    $res = $stmt->get_result();
//} else {
//    $sql = "SELECT id, name, description, price, stock, image
//            FROM products
//            WHERE is_active = 1
//            ORDER BY created_at DESC";
//    $res = $conn->query($sql);
//}

// SHTOVA KETE KOD
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$res = null;

$sql = "SELECT id, name, description, price, stock, image
        FROM products
        WHERE is_active = 1";

$params = [];
$types  = "";

// filter kategori
if ($cat !== '') {
    $sql .= " AND category_slug = ?";
    $types .= "s";
    $params[] = $cat;
}

// search text
if ($q !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $types .= "ss";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();

?>

<!-- ================= PAGE TITLE ================= -->
<div class="container my-4">
    <h2 class="mb-3">
        Produktet
        <?php if ($cat): ?>
            — <?php echo htmlspecialchars($categories[$cat]); ?>
        <?php endif; ?>
    </h2>

    <!-- Shporta -->
    <p>Shporta: <strong id="cart-count"><?php echo (int)$cart_count; ?></strong></p>

    <!-- ================= FILTER LINKS ================= -->
    <!-- ================= FILTER LINKS (BOOTSTRAP PILLS) ================= -->
    <style>
        /* vetem pak polish (opsionale) */
        .filter-bar{
            background: #fff;
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 16px;
            padding: 12px;
            box-shadow: 0 10px 22px rgba(0,0,0,.05);
        }
        .filter-bar .nav-pills .nav-link{
            border-radius: 999px;
            padding: 8px 14px;
            font-weight: 600;
        }
        .filter-bar .nav-pills .nav-link.active{
            box-shadow: 0 8px 18px rgba(13,110,253,.25);
        }
    </style>

    <div class="filter-bar mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="small text-muted">
                Zgjidh kategorinë:
                <?php if ($cat): ?>
                    <span class="fw-semibold"><?php echo htmlspecialchars($categories[$cat]); ?></span>
                <?php else: ?>
                    <span class="fw-semibold">Të gjitha</span>
                <?php endif; ?>
            </div>

            <?php if ($cat): ?>
                <a class="btn btn-outline-secondary btn-sm"
                   href="/e-pharma/public/products.php">
                    Hiq filtrin
                </a>
            <?php endif; ?>
        </div>

        <ul class="nav nav-pills mt-2 flex-wrap gap-2">
            <li class="nav-item">
                <a
                        class="nav-link <?php echo ($cat === '' ? 'active' : ''); ?>"
                        href="/e-pharma/public/products.php"
                >
                    Të gjitha
                </a>
            </li>

            <?php foreach ($categories as $slug => $label): ?>
                <li class="nav-item">
                    <a
                            class="nav-link <?php echo ($cat === $slug ? 'active' : ''); ?>"
                            href="/e-pharma/public/products.php?cat=<?php echo urlencode($slug); ?>"
                    >
                        <?php echo htmlspecialchars($label); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- ================= PRODUCTS ================= -->
    <div class="row g-4">

        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($p = $res->fetch_assoc()): ?>
                <?php $outOfStock = ((int)$p['stock'] <= 0); ?>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">

                        <?php if (!empty($p['image'])): ?>
                            <img
                                    src="<?php echo htmlspecialchars($p['image']); ?>"
                                    class="card-img-top"
                                    style="height:220px; object-fit:cover;"
                                    alt="<?php echo htmlspecialchars($p['name']); ?>"
                            >
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>

                            <?php if (!empty($p['description'])): ?>
                                <p class="card-text small text-muted">
                                    <?php echo nl2br(htmlspecialchars($p['description'])); ?>
                                </p>
                            <?php endif; ?>

                            <p class="mt-auto mb-1">
                                <strong><?php echo number_format((float)$p['price'], 2); ?> ALL</strong>
                            </p>

                            <p class="small mb-2">
                                Stok: <?php echo (int)$p['stock']; ?>
                            </p>

                            <!-- ✅ FIX: përdor .js-add-to-cart që e kap cart.js -->
                            <a
                                    href="#"
                                    class="btn btn-primary btn-sm js-add-to-cart <?php echo $outOfStock ? 'disabled' : ''; ?>"
                                    data-product-id="<?php echo (int)$p['id']; ?>"
                                    <?php echo $outOfStock ? 'aria-disabled="true"' : ''; ?>
                            >
                                <?php echo $outOfStock ? 'Nuk ka stok' : 'Shto në shportë'; ?>
                            </a>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-muted">
                    Nuk ka produkte<?php echo $cat ? " për këtë kategori." : " për momentin."; ?>
                </p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
/**
 * FOOTER sipas login
 */
if (isset($_SESSION["user_id"])) {
    include_once __DIR__ . '/../includes/login/footer.php';
} else {
    include_once __DIR__ . '/../includes/no_login/footer.php';
}
?>
