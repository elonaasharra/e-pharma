</main>

<footer class="border-top py-3">
    <div class="container text-center small text-muted">
        © <?php echo date('Y'); ?> E-Pharma Admin
    </div>
</footer>

<!-- jQuery (se po perdor AJAX me jQuery) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap bundle (me Popper brenda) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!empty($page_scripts)) echo $page_scripts; ?>

</body>
</html>
