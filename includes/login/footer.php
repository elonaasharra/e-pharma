<footer class="footer-nologin mt-5">
    <div class="container py-4">
        <div class="row">

            <div class="col-md-4 mb-3">
                <h5>E-Pharma</h5>
                <p class="small">
                    Online pharmacy platform për blerje të shpejtë dhe të sigurt.
                </p>
            </div>

            <div class="col-md-4 mb-3">
                <h6>Links</h6>
                <ul class="list-unstyled">
                    <li><a href="/e-pharma/public/index.php">Home</a></li>
                    <li><a href="/e-pharma/public/user/profile.php">My Profile</a></li>

                    <?php if ((int)($_SESSION["role_id"] ?? 0) === 2): ?>
                        <li><a href="/e-pharma/public/admin/users.php">Admin</a></li>
                    <?php endif; ?>

                    <li><a href="/e-pharma/public/logout.php">Logout</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-3">
                <h6>Contact</h6>
                <p class="small mb-1">📧 support@epharma.com</p>
                <p class="small mb-0">📞 +383 44 000 000</p>
            </div>

        </div>

        <hr>

        <div class="text-center small">
            © <?php echo date('Y'); ?> E-Pharma. All rights reserved.
        </div>
    </div>
</footer>

<!-- Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/e-pharma/public/user/edit_profile.php') {
    echo '<script src="/e-pharma/public/assets/js/edit_profile.js"></script>';
}
if ($path === '/e-pharma/public/user/profile.php') {
    echo '<script src="/e-pharma/public/assets/js/profile.js"></script>';
}
$pages_need_cart = [
        '/e-pharma/public/products.php',
        '/e-pharma/public/index.php',
        '/e-pharma/public/my_cart.php',
];

if (in_array($path, $pages_need_cart, true)) {
    echo '<script src="/e-pharma/public/assets/js/cart.js"></script>';
}
?>
</body>
</html>

