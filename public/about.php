<?php
require_once __DIR__ . '/../includes/session.php';
$is_logged_in = isset($_SESSION['user_id']);

if ($is_logged_in) {
    require __DIR__ . '/../includes/login/header.php';
} else {
    require __DIR__ . '/../includes/no_login/header.php';
}
?>

<style>
    /* vetëm pak rregullime, pjesa tjetër Bootstrap */
    .about-hero {
        border-radius: 18px;
        overflow: hidden;
    }
    .about-hero .hero-bg {
        background: #f8f9fa;
    }
    .icon-badge{
        width: 44px; height: 44px;
        display:flex; align-items:center; justify-content:center;
        border-radius: 12px;
        background: #eef2ff;
        font-weight: 800;
    }
    .category-chip{
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 999px;
        padding: 8px 12px;
        background: #fff;
        display:inline-flex;
        align-items:center;
        gap: 8px;
        margin: 6px 8px 0 0;
        font-size: 14px;
    }
    .category-dot{
        width: 10px; height: 10px;
        border-radius: 50%;
        background:#0d6efd;
        display:inline-block;
    }
</style>

<div class="container py-4">

    <!-- HERO -->
    <div class="card shadow-sm about-hero mb-4">
        <div class="row g-0">
            <div class="col-lg-7 p-4 p-lg-5">
                <h2 class="fw-bold mb-2">Rreth nesh — E-Pharma</h2>
                <p class="text-muted mb-3">
                    E-Pharma është një farmaci online e ndërtuar për ta bërë porosinë e produkteve
                    farmaceutike dhe të kujdesit personal sa më të thjeshtë. Ne synojmë një eksperiencë
                    të qartë, të shpejtë dhe të besueshme — nga shfletimi i kategorive, deri te pagesa dhe fatura.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">Pagesa të sigurta</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">Produkte të përzgjedhura</span>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2">Mbështetje klienti</span>
                </div>
            </div>

            <div class="col-lg-5 hero-bg p-4 p-lg-5 border-start">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="icon-badge">✓</div>
                    <div>
                        <div class="fw-semibold">Cilësi & Verifikim</div>
                        <div class="text-muted small">Produkte të përzgjedhura dhe informacion i qartë.</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="icon-badge">🔒</div>
                    <div>
                        <div class="fw-semibold">Privatësi & Siguri</div>
                        <div class="text-muted small">Të dhënat trajtohen me kujdes dhe siguri.</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <div class="icon-badge">💳</div>
                    <div>
                        <div class="fw-semibold">PayPal / Stripe</div>
                        <div class="text-muted small">Pagesat kryhen përmes palëve të treta të besueshme.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KATEGORITË -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2">Kategoritë kryesore</h5>
                    <p class="text-muted mb-3">
                        Për të thjeshtuar kërkimin, produktet janë organizuar në kategori të dedikuara:
                    </p>

                    <div class="mb-2">
                        <span class="category-chip"><span class="category-dot"></span> Dermo Cosmetic</span>
                        <span class="category-chip"><span class="category-dot"></span> Mom & Kids</span>
                        <span class="category-chip"><span class="category-dot"></span> Suplemente</span>
                        <span class="category-chip"><span class="category-dot"></span> Skincare</span>
                        <span class="category-chip"><span class="category-dot"></span> Haircare</span>
                        <span class="category-chip"><span class="category-dot"></span> Oral Care</span>
                    </div>

                    <hr class="my-3">

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <span class="fw-semibold">Dermo Cosmetic</span> — produkte dermo-farmaceutike dhe kujdes i specializuar për lëkurën.
                        </li>
                        <li class="list-group-item px-0">
                            <span class="fw-semibold">Mom & Kids</span> — produkte për nënat dhe fëmijët, të përshtatshme për përdorim të përditshëm.
                        </li>
                        <li class="list-group-item px-0">
                            <span class="fw-semibold">Suplemente</span> — vitamina/minerale dhe mbështetje për mirëqenie.
                        </li>
                        <li class="list-group-item px-0">
                            <span class="fw-semibold">Skincare</span> — pastrim, hidratim dhe rutina për fytyrën/trupin.
                        </li>
                        <li class="list-group-item px-0">
                            <span class="fw-semibold">Haircare</span> — produkte për flokë, trajtime dhe kujdes specifik.
                        </li>
                        <li class="list-group-item px-0">
                            <span class="fw-semibold">Oral Care</span> — higjienë orale: pasta, shpëlarës, kujdes i përditshëm.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- LICENCA -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2">Licencë & staf (demo)</h5>
                    <p class="text-muted mb-3">
                        (Të dhëna shembull për projekt/për prezantim)
                    </p>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <tbody>
                            <tr>
                                <td class="text-muted">Emri i farmacisë</td>
                                <td class="fw-semibold">E-Pharma Online</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Licenca</td>
                                <td class="fw-semibold">AL-PH/2026-0147</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Farmacist përgjegjës</td>
                                <td class="fw-semibold">Dr. Anxhela Rama</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Drejtues i platformës</td>
                                <td class="fw-semibold">Elona Sharra</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kontakt</td>
                                <td class="fw-semibold">support@e-pharma.al • +355 69 000 0000</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-light border mt-3 mb-0">
                        <div class="small text-muted">
                            Të gjitha të dhënat personale trajtohen në përputhje me politikat e privatësisë dhe sigurisë.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="card shadow-sm">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="fw-bold">Gati për të parë produktet?</div>
                <div class="text-muted">Shfleto kategoritë dhe zgjidh produktet që të duhen.</div>
            </div>
            <a class="btn btn-primary" href="/e-pharma/public/products.php">Shko te produktet</a>
        </div>
    </div>

</div>

<?php
if ($is_logged_in) {
    require __DIR__ . '/../includes/login/footer.php';
} else {
    require __DIR__ . '/../includes/no_login/footer.php';
}
?>
