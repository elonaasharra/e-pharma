<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/remember_me.php';
/** @var mysqli $conn */

rememberMeAutoLogin($conn);
// Nëse është admin, dërgoje te dashboard-i i adminit
if (isset($_SESSION["role_id"]) && (int)$_SESSION["role_id"] === 2) {
    header("Location: /e-pharma/public/admin/dashboard.php");
    exit;
}

elseif (isset($_SESSION["user_id"])) {
    include_once __DIR__ . '/../includes/login/header.php';
} else {
    include_once __DIR__ . '/../includes/no_login/header.php';
}
?>
<?php
// ================= BEST SELLERS =================
$bestSql = "
    SELECT id, name, description, price, image, sold_count
    FROM products
    WHERE is_active = 1
    ORDER BY sold_count DESC
    LIMIT 3
";
$bestRes = mysqli_query($conn, $bestSql);
$bestProducts = [];

if ($bestRes) {
    while ($row = mysqli_fetch_assoc($bestRes)) {
        $bestProducts[] = $row;
    }
}
?>




<!-- =================== CAROUSEL =================== -->
<div id="homeCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>

    <div class="carousel-inner">

        <div class="carousel-item active">
            <img src="/e-pharma/public/assets/images/cerave_carusel.webp" class="d-block w-100" alt="Offer 1">
            <div class="carousel-caption text-start">
                <h1>Ulje deri në 30%</h1>
                <p>Suplementet më të kërkuara këtë javë.</p>
                <p><a class="btn btn-lg btn-primary" href="/e-pharma/public/products.php">Shiko produktet</a></p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="/e-pharma/public/assets/images/cosmetic_carusel.jpg" class="d-block w-100" alt="Offer 2">
            <div class="carousel-caption">
                <h1>Ulje deri në 25%</h1>
                <p>Dermokozmetikë me çmime promocionale.</p>
                <p><a class="btn btn-lg btn-primary" href="/e-pharma/public/products.php">Bli tani</a></p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="/e-pharma/public/assets/images/mamas_carousel.jpg" class="d-block w-100" alt="Offer 3">
            <div class="carousel-caption text-end ms-auto">
                <h1>Ulje deri në 30%</h1>
                <p>Shëndet dhe kujdes për më të vegjlit.</p>
                <p><a class="btn btn-lg btn-primary" href="/e-pharma/public/products.php">Shiko kategorinë</a></p>
            </div>
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>


<section class="container my-5">

    <!-- ========= INFO STRIP ========= -->
    <div class="mini-strip mb-4">
        <div class="mini-row">

            <div class="mini-item">
                <div class="mini-icon">💳</div>
                <div class="mini-text">
                    <p class="mini-title">Payment Methods</p>
                    <p class="mini-sub">Cash / Card</p>
                </div>
            </div>

            <div class="mini-item">
                <div class="mini-icon">🚚</div>
                <div class="mini-text">
                    <p class="mini-title">Shipping</p>
                    <p class="mini-sub">ALBANIA,KOSOVO &amp; MACEDONIA</p>
                </div>
            </div>

            <a class="mini-link mini-item" href="https://instagram.com/USERNAME" target="_blank" rel="noopener">
                <div class="mini-icon">📷</div>
                <div class="mini-text">
                    <p class="mini-title">Follow us </p>
                    <p class="mini-sub">Instagram</p>
                </div>
            </a>
            <a class="mini-link mini-item"
               href="https://www.google.com/maps/search/?api=1&query=Rruga%20Myslym%20Shyri%2012%2C%20Tiran%C3%AB"
               target="_blank" rel="noopener">
                <div class="mini-icon">📍</div>
                <div class="mini-text">
                    <p class="mini-title">Visit us</p>
                    <p class="mini-sub">Open in Maps</p>
                </div>
            </a>


        </div>
    </div>

    <!-- ========= HEADER ========= -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Kategoritë kryesore</h3>
        <a class="text-decoration-none" href="/e-pharma/public/products.php">Shiko të gjitha →</a>
    </div>

    <!-- ========= CATEGORY CARDS ========= -->
    <div class="row g-4">
        <div class="col-12 col-md-6">
            <a class="text-decoration-none" href="/e-pharma/public/products.php?cat=dermokozmetike">
                <div class="cat-card">
                    <img src="/e-pharma/public/assets/images/kategorite/dermo_cosmetic_cover.png" alt="Dermokozmetikë">
                    <div class="cat-content">
                        <div class="cat-badge">
                        <h3 class="cat-title">Dermo Cosmetic</h3>
                        <span class="cat-cta">Shop Now</span>
                    </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6">
            <a class="text-decoration-none" href="/e-pharma/public/products.php?cat=baby">
                <div class="cat-card">
                    <img src="/e-pharma/public/assets/images/kategorite/mom_cover.png" alt="Baby Care">
                    <div class="cat-content">
                        <div class="cat-badge">
                        <h3 class="cat-title">Mom &amp; Kids</h3>
                        <span class="cat-cta">Shop Now</span>
                    </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 col-xl-3">
            <a class="text-decoration-none" href="/e-pharma/public/products.php?cat=suplemente">
                <div class="cat-card">
                    <img src="/e-pharma/public/assets/images/kategorite/suplement_cover.png" alt="Suplemente">
                    <div class="cat-content" >
                        <div class="cat-badge">
                        <h3 class="cat-title" >Suplemente</h3>
                        <span class="cat-cta">Shop Now</span>
                    </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 col-xl-3">
            <a class="text-decoration-none" href="/e-pharma/public/products.php?cat=higjiene">
                <div class="cat-card" ">
                    <img src="/e-pharma/public/assets/images/kategorite/skincare_cover.png" alt="Higjienë">
                    <div class="cat-content">
                        <div class="cat-badge">
                        <h3 class="cat-title" >Skincare</h3>
                        <span class="cat-cta">Shop Now</span>
                    </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 col-xl-3">
            <a class="text-decoration-none" href="/e-pharma/public/products.php?cat=oferta">
                <div class="cat-card">
                    <img src="/e-pharma/public/assets/images/kategorite/haircare_cover.png" alt="Oferta">
                    <div class="cat-content" >
                        <div class="cat-badge">
                        <h3 class="cat-title" >Haircare</h3>
                        <span class="cat-cta">Shop Now</span>
                    </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 col-xl-3">
            <a class="text-decoration-none" href="/e-pharma/public/products.php?cat=oferta">
                <div class="cat-card" >
                    <img src="/e-pharma/public/assets/images/kategorite/oral_care.png" alt="oralcare">
                    <div class="cat-content" >
                        <div class="cat-badge">
                        <h3 class="cat-title" >Oralcare</h3>
                        <span class="cat-cta">Shop Now</span>
                    </div>
                    </div>
                </div>
            </a>
        </div>
</section>
<!--bestsellers nga databaza-->
<section class="best-sellers py-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h3 class="mb-1">Best Sellers</h3>
                <p class="text-muted mb-0">Produktet më të kërkuara nga klientët tanë.</p>
            </div>

            <a class="text-decoration-none" href="/e-pharma/public/products.php?filter=bestsellers">
                Shiko të gjitha →
            </a>
        </div>

        <div class="row g-4 row-cols-2 row-cols-md-3 row-cols-xl-4">

            <?php if (empty($bestProducts)): ?>
                <div class="col-12">
                    <p class="text-muted">Nuk ka ende produkte best seller.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($bestProducts as $p): ?>
                <div class="col">
                    <div class="bs-card position-relative">

                        <div class="bs-thumb">
                            <img
                                    src="<?php echo htmlspecialchars($p['image']); ?>"
                                    alt="<?php echo htmlspecialchars($p['name']); ?>"
                            >
                        </div>

                        <div class="p-3">
                            <h5 class="bs-title"><?php echo htmlspecialchars($p['name']); ?></h5>
                            <p class="bs-desc"><?php echo htmlspecialchars($p['description']); ?></p>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="bs-price">
                                    <?php echo number_format((float)$p['price'], 0); ?> Lek
                                </div>

                                <div class="btn-group">
                                    <a href="/e-pharma/public/product.php?id=<?php echo (int)$p['id']; ?>"
                                       class="btn btn-sm btn-outline-secondary">
                                        Shiko
                                    </a>

                                    <a href="#"
                                       class="btn btn-sm btn-primary js-add-to-cart"
                                       data-product-id="<?php echo (int)$p['id']; ?>">
                                        Shto
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</section>


<?php
if (isset($_SESSION["user_id"])) {
    include_once __DIR__ . '/../includes/login/footer.php';
} else {
    include_once __DIR__ . '/../includes/no_login/footer.php';
}
?>
