<?php
require_once __DIR__ . '/../includes/session.php';

$order_id = isset($_GET['token']) ? $_GET['token'] : null;
if (!$order_id) {
    echo "Missing token/order id";
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PayPal Return</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; }
        .box { max-width: 650px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; padding: 20px; }
        .ok { color: #0a7a28; font-weight: bold; }
        .err { color: #b00020; font-weight: bold; }
        button { padding: 10px 14px; cursor: pointer; }
        pre { background: #f6f6f6; padding: 12px; border-radius: 8px; overflow: auto; }
        .small { font-size: 13px; color: #555; }
    </style>
</head>
<body>

<div class="box">
    <h3>PayPal approved ✅</h3>
    <p><b>Order ID:</b> <?php echo htmlspecialchars($order_id); ?></p>

    <p id="msg" class="small">Kliko “Finalize Payment” për të përfunduar pagesën.</p>

    <button id="captureBtn">Finalize Payment (Capture)</button>



    <p style="margin-top:18px;">
        <a href="/e-pharma/public/index.php">Kthehu te Homepage</a>
    </p>
</div>

<script>
    async function capturePayment() {
        const msg = document.getElementById('msg');
        const out = document.getElementById('out');
        const btn = document.getElementById('captureBtn');

        msg.textContent = "Duke finalizuar pagesën...";
        out.textContent = "";
        btn.disabled = true;

        const fd = new FormData();
        fd.append('order_id', <?php echo json_encode($order_id); ?>);

        try {
            const r = await fetch('/e-pharma/public/ajax/ajax_paypal_capture_order.php', {
                method: 'POST',
                body: fd
            });

            const j = await r.json();

            if (j.ok && j.capture && j.capture.status === "COMPLETED") {
                msg.innerHTML = "<span class='ok'>✅ Pagesa u krye me sukses!</span> (Status: COMPLETED)";
            } else {
                msg.innerHTML = "<span class='err'>❌ Pagesa dështoi.</span> Shiko detajet më poshtë.";
                btn.disabled = false;
            }
        } catch (e) {
            msg.innerHTML = "<span class='err'>❌ Gabim gjatë kërkesës:</span> " + e.message;
            btn.disabled = false;
        }
    }

    document.getElementById('captureBtn').addEventListener('click', capturePayment);

    // OPSIONALE: nëse do ta bëjë automatik capture sapo hapet faqja,
    // hiq "//" nga rreshti poshtë.
    // window.addEventListener('load', capturePayment);
</script>

</body>
</html>
