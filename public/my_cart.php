<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/cart.php';
require_once __DIR__ . '/../includes/login/header.php';
/** @var mysqli $conn */


$user_id = (int)$_SESSION["user_id"];
$items = cart_get_items($conn, $user_id);
$total = cart_get_total($conn, $user_id);
?>

<div class="container mt-4">
    <h3>My Cart</h3>

    <?php if (empty($items)): ?>
        <div class="alert alert-warning">
            Shporta është bosh.
        </div>
    <?php else: ?>

        <table class="table table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>Produkt</th>
                <th>Çmimi</th>
                <th>Sasia</th>
                <th>Totali</th>
                <th>Veprime</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($items as $row): ?>
                <tr data-product="<?php echo (int)$row['product_id']; ?>">
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>

                    <!-- Ruaj edhe unit price si data-attribute (më e sigurt se children[1]) -->
                    <td data-unit-price="<?php echo number_format((float)$row['unit_price'], 2, '.', ''); ?>">
                        <?php echo number_format((float)$row['unit_price'], 2, '.', ''); ?> €
                    </td>

                    <td>
                        <input type="number"
                               class="form-control form-control-sm qty"
                               value="<?php echo (int)$row['quantity']; ?>"
                               min="1"
                               style="width:80px">
                    </td>

                    <td>
                        <span class="line-total"><?php echo number_format((float)$row['line_total'], 2, '.', ''); ?></span> €
                    </td>

                    <td>
                        <button type="button" class="btn btn-sm btn-danger btn-remove">
                            Fshi
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

        <div class="text-end">
            <h5>Total: <span id="cart-total"><?php echo number_format((float)$total, 2, '.', ''); ?></span> €</h5>
            <a href="/e-pharma/public/checkout.php" class="btn btn-success">
                Vazhdoni në Pagesë
            </a>
        </div>

    <?php endif; ?>
</div>

<script>
    function recalcTotal() {
        let sum = 0;
        document.querySelectorAll('.line-total').forEach(el => {
            const v = parseFloat(el.textContent);
            if (!isNaN(v)) sum += v;
        });
        const totalEl = document.getElementById('cart-total');
        if (totalEl) totalEl.textContent = sum.toFixed(2);
    }

    /* UPDATE QTY */
    document.querySelectorAll('input.qty').forEach(inp => {
        inp.addEventListener('change', async function () {
            const tr = this.closest('tr');
            const productId = tr.getAttribute('data-product');

            let qty = parseInt(this.value, 10);
            if (!qty || qty < 1) qty = 1;
            this.value = qty;

            // unit price nga data attribute (më stabile)
            const unitPrice = parseFloat(tr.querySelector('td[data-unit-price]').getAttribute('data-unit-price'));

            const fd = new FormData();
            fd.append('product_id', productId);
            fd.append('qty', qty);

            const r = await fetch('/e-pharma/public/ajax/ajax_cart_update_qty.php', {
                method: 'POST',
                body: fd
            });
            const j = await r.json();

            if (!j.ok) {
                alert(j.error || 'Update failed');
                return;
            }

            // update subtotal row
            const lineTotal = (unitPrice * qty);
            tr.querySelector('.line-total').textContent = lineTotal.toFixed(2);

            recalcTotal();
        });
    });

    /* REMOVE ITEM */
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', async function () {
            const tr = this.closest('tr');
            const productId = tr.getAttribute('data-product');

            if (!confirm("Ta fshijmë produktin nga shporta?")) return;

            const fd = new FormData();
            fd.append('product_id', productId);

            const r = await fetch('/e-pharma/public/ajax/ajax_cart_remove_item.php', {
                method: 'POST',
                body: fd
            });
            const j = await r.json();

            if (!j.ok) {
                alert(j.error || "Delete failed");
                return;
            }

            // hiqe rreshtin
            tr.remove();

            // rifresko totalin
            recalcTotal();

            // nëse s’ka më rreshta, rifresko që të shfaqë “shporta bosh”
            if (document.querySelectorAll('tbody tr').length === 0) {
                window.location.reload();
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../includes/login/footer.php'; ?>
