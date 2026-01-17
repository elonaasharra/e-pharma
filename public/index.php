<?php
require_once __DIR__ . '/../includes/session.php';
if (isset($_SESSION["user_id"])) {
    include_once __DIR__ . '/../includes/login/header.php';
} else {
    include_once __DIR__ . '/../includes/no_login/header.php';
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

            <!-- Product 1 -->
            <div class="col">
                <div class="bs-card position-relative">
<!--                    <span class="bs-badge">Best Seller</span>-->

                    <div class="bs-thumb">
                        <img src="/e-pharma/public/assets/images/bestsellers/cerave.webp" alt="CeraVe Hydrating Cleanser">
                    </div>

                    <div class="p-3">
                        <h5 class="bs-title">CeraVe Hydrating Cleanser</h5>
                        <p class="bs-desc">Pastrues hidratues për lëkurë normale–të thatë.</p>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="bs-price">1,990 Lek</div>
                            <div class="btn-group">
                                <a href="/e-pharma/public/product.php?id=1" class="btn btn-sm btn-outline-secondary">Shiko</a>
                                <a href="/e-pharma/public/cart_add.php?id=1" class="btn btn-sm btn-primary">Shto</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="col">
                <div class="bs-card position-relative">
<!--                    <span class="bs-badge">Top</span>-->

                    <div class="bs-thumb">
                        <img src="/e-pharma/public/assets/images/bestsellers/nuxe.webp" alt="Nuxe Huile Prodigieuse">
                    </div>

                    <div class="p-3">
                        <h5 class="bs-title">Nuxe Huile Prodigieuse</h5>
                        <p class="bs-desc">Vaj multi-funksional për trup, fytyrë dhe flokë.</p>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="bs-price">2,490 Lek</div>
                            <div class="btn-group">
                                <a href="/e-pharma/public/product.php?id=2" class="btn btn-sm btn-outline-secondary">Shiko</a>
                                <a href="/e-pharma/public/cart_add.php?id=2" class="btn btn-sm btn-primary">Shto</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="col">
                <div class="bs-card position-relative">
<!--                    <span class="bs-badge">Best Seller</span>-->

                    <div class="bs-thumb">
                        <img src="/e-pharma/public/assets/images/bestsellers/vitaminC.webp" alt="Vitamin C">
                    </div>

                    <div class="p-3">
                        <h5 class="bs-title">Vitamin C 1000mg</h5>
                        <p class="bs-desc">Suplement për imunitet dhe energji ditore.</p>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="bs-price">890 Lek</div>
                            <div class="btn-group">
                                <a href="/e-pharma/public/product.php?id=3" class="btn btn-sm btn-outline-secondary">Shiko</a>
                                <a href="/e-pharma/public/cart_add.php?id=3" class="btn btn-sm btn-primary">Shto</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 4 -->
            <div class="col">
                <div class="bs-card position-relative">
<!--                    <span class="bs-badge">Top</span>-->

                    <div class="bs-thumb">
                        <img src="/e-pharma/public/assets/images/bestsellers/baby.webp" alt="Baby product">
                    </div>

                    <div class="p-3">
                        <h5 class="bs-title">Baby Care Lotion</h5>
                        <p class="bs-desc">Locion i butë për hidratim dhe kujdes ditor.</p>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="bs-price">1,290 Lek</div>
                            <div class="btn-group">
                                <a href="/e-pharma/public/product.php?id=4" class="btn btn-sm btn-outline-secondary">Shiko</a>
                                <a href="/e-pharma/public/cart_add.php?id=4" class="btn btn-sm btn-primary">Shto</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
