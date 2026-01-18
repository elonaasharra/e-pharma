<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../cart.php';
/** @var mysqli $conn */

/* Nëse s’është i loguar → login */
if (!isset($_SESSION["user_id"])) {
    header("Location: /e-pharma/public/login.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];
$cart_count = cart_count_items($conn, $user_id);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Pharma</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Local CSS -->
    <link rel="stylesheet" href="/e-pharma/public/assets/css/register.css">
<!--    sepse perdorim modelin e njejt-->
    <link rel="stylesheet" href="/e-pharma/public/assets/css/header_nologin.css">
    <link rel="stylesheet" href="/e-pharma/public/assets/css/footer_nologin.css">
    <link rel="stylesheet" href="/e-pharma/public/assets/css/index.css">
    <?php
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path === '/e-pharma/public/user/profile.php') {
        echo '<link rel="stylesheet" href="/e-pharma/public/assets/css/profile.css">';
    }
    if ($path === '/e-pharma/public/user/edit_profile.php') {
        echo '<link rel="stylesheet" href="/e-pharma/public/assets/css/edit_profile.css">';
    }
    ?>

</head>
<body>

<nav class="py-2 bg-light border-bottom topbar-sticky">
    <div class="container d-flex flex-wrap">
        <ul class="nav me-auto">
            <li class="nav-item">
                <a href="/e-pharma/public/index.php" class="nav-link link-dark px-2 active">Home</a>
            </li>

            <li class="nav-item">
                <a href="/e-pharma/public/products.php" class="nav-link link-dark px-2">Products</a>
            </li>

            <li class="nav-item">
                <a href="/e-pharma/public/my_cart.php" class="nav-link link-dark px-2">
                    🛒 Shporta (
                    <span id="cart-count"><?php echo (int)$cart_count; ?></span>
                    )
                </a>
            </li>

            <li class="nav-item">
                <a href="/e-pharma/public/about.php" class="nav-link link-dark px-2">About</a>
            </li>
<!-- nese dum qe admini ta shofi si user-->
<!--            --><?php //if ((int)($_SESSION["role_id"] ?? 0) === 2): ?>
<!--                <li class="nav-item">-->
<!--                    <a href="/e-pharma/public/admin/users.php" class="nav-link link-dark px-2">Admin</a>-->
<!--                </li>-->
<!--            --><?php //endif; ?>
        </ul>

        <ul class="nav">
            <li class="nav-item">
                <a href="/e-pharma/public/user/profile.php" class="nav-link link-dark px-2">
                    My Profile
                </a>
            </li>
            <li class="nav-item">
                <a href="/e-pharma/public/logout.php" class="nav-link link-dark px-2">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<header class="py-3 mb-4 border-bottom">
    <div class="container d-flex flex-wrap justify-content-center">
        <a href="/e-pharma/public/index.php"
           class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto text-dark text-decoration-none">
            <span class="fs-4">E-Pharma</span>
        </a>

<!--        <form class="col-12 col-lg-auto mb-3 mb-lg-0">-->
<!--            <input type="search" class="form-control" placeholder="Search..." aria-label="Search">-->
<!--        </form>-->
        <form class="col-12 col-lg-auto mb-3 mb-lg-0" method="GET" action="/e-pharma/public/products.php">
            <input
                    type="search"
                    class="form-control"
                    name="q"
                    placeholder="Search products..."
                    aria-label="Search"
                    value="<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            >
        </form>

    </div>
</header>
