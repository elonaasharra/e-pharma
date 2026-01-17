// public/assets/js/cart.js

document.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-add-to-cart]");
    if (!btn) return;

    const productId = btn.getAttribute("data-product-id");
    const qtyInputSelector = btn.getAttribute("data-qty-input"); // opsionale
    let qty = 1;

    if (qtyInputSelector) {
        const qtyEl = document.querySelector(qtyInputSelector);
        if (qtyEl) qty = parseInt(qtyEl.value || "1", 10);
    }

    const formData = new FormData();
    formData.append("product_id", productId);
    formData.append("qty", qty);

    try {
        const res = await fetch("/e-pharma/public/ajax/ajax_cart_add.php", {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        });

        const data = await res.json();

        if (!data.ok) {
            alert(data.error || "Ndodhi një gabim.");
            return;
        }

        // Përditëso badge-in në header
        const badge = document.getElementById("cart-count");
        if (badge) badge.textContent = data.cart_count;

        alert("Produkti u shtua në shportë ✅");
    } catch (err) {
        console.error(err);
        alert("Problem me serverin / rrjetin.");
    }
});
