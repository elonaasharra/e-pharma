<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/cart.php';
require_once __DIR__ . '/../includes/paypal_config.php';
require_once __DIR__ . '/../includes/login/header.php';
/** @var mysqli $conn */

$user_id = (int)$_SESSION["user_id"];
$total = cart_get_total($conn, $user_id);
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h3 class="mb-3">Checkout (PayPal Sandbox)</h3>

            <p>
                Totali i shportës:
                <strong><?php echo number_format($total, 2, '.', ''); ?> EUR</strong>
            </p>

            <!-- Mesazhi i pagesës do shfaqet këtu -->
            <div id="pay-msg"></div>

            <?php if ($total <= 0): ?>
                <div class="alert alert-warning">
                    Shporta është bosh. Shto produkte te Products.
                </div>
            <?php else: ?>
                <div id="paypal-buttons"></div>
<!--                <pre id="out" class="mt-3 small"></pre>-->
            <?php endif; ?>


        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/login/footer.php'; ?>

<!-- PayPal JS SDK (popup brenda faqes) -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo urlencode(PAYPAL_CLIENT_ID); ?>&currency=EUR"></script>

<script>
    const msg = document.getElementById('pay-msg');
    // const out = document.getElementById('out');

    if (document.getElementById('paypal-buttons')) {
        paypal.Buttons({

            createOrder: async function () {
                msg.innerHTML = "";
                // out.textContent = "Creating order...";

                const r = await fetch('/e-pharma/public/ajax/ajax_paypal_create_order.php', { method: 'POST' });
                const j = await r.json();

                if (!j.ok) {
                    msg.innerHTML = "<div class='alert alert-danger mt-3'>Gabim gjatë krijimit të order: " +
                        (j.error || "Create order failed") + "</div>";
                    throw new Error(j.error || "Create order failed");
                }

                msg.innerHTML = "<div class='alert alert-info mt-3'>Order u krijua. Po hapet PayPal...</div>";
                return j.order_id;
            },

            onApprove: async function (data) {
                msg.innerHTML = "<div class='alert alert-info mt-3'>Pagesa po konfirmohet...</div>";
                // out.textContent = "Capturing order...";

                const fd = new FormData();
                fd.append('order_id', data.orderID);

                const r = await fetch('/e-pharma/public/ajax/ajax_paypal_capture_order.php', {
                    method: 'POST',
                    body: fd
                });
                const j = await r.json();

                if (!j.ok) {
                    msg.innerHTML = "<div class='alert alert-danger mt-3'> Pagesa dështoi: " +
                        (j.error || "Capture failed") + "</div>";
                    return;
                }

                msg.innerHTML = "<div class='alert alert-success mt-3'> Pagesa u krye me sukses! Po hapet fatura...</div>";

                setTimeout(() => {
                    window.location.href = "/e-pharma/public/invoice.php?order_id=" + j.order_db_id;
                }, 1200);
            },

            onCancel: function () {
                msg.innerHTML = "<div class='alert alert-warning mt-3'> Pagesa u anulua.</div>";
            },

            onError: function (err) {
                console.error(err);
                msg.innerHTML = "<div class='alert alert-danger mt-3'> Gabim gjatë pagesës. Shiko Console.</div>";
            }

        }).render('#paypal-buttons');
    }
</script>
