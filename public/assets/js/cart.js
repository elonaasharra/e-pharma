$(function () {

    // Butoni "Shto në shportë" duhet të ketë class .js-add-to-cart
    // dhe atribut: data-product-id="123"
    $(document).on("click", ".js-add-to-cart", function (e) {
        e.preventDefault();

        var pid = parseInt($(this).data("product-id"), 10);
        if (!pid) return;

        $.ajax({
            type: "POST",
            url: "/e-pharma/public/ajax/ajax_cart_add.php",
            data: { action: "add", product_id: pid, qty: 1 },
            dataType: "json",
            success: function (res) {
                if (res && res.ok) {
                    if (typeof res.cart_count !== "undefined") {
                        $("#cart-count").text(res.cart_count);
                    }
                    // opsionale
                    // alert("U shtua në shportë!");
                } else {
                    // nëse s’je logged in → login
                    if (res && (res.error === "Not logged in" || res.message === "Not logged in")) {
                        window.location.href = "/e-pharma/public/login.php";
                        return;
                    }
                    alert((res && (res.error || res.message)) ? (res.error || res.message) : "Nuk u shtua në shportë.");
                }
            },
            error: function (xhr) {
                // nëse backend kthen 401
                if (xhr.status === 401) {
                    window.location.href = "/e-pharma/public/login.php";
                    return;
                }

                // ✅ FIX për rastin tënd: serveri kthen 302 redirect te login.php
                // jQuery e quan "parsererror" sepse priste JSON dhe mori HTML
                if (
                    (xhr.status === 302) ||
                    (xhr.responseURL && xhr.responseURL.indexOf("login.php") !== -1) ||
                    (xhr.responseText && xhr.responseText.toLowerCase().indexOf("<html") !== -1)
                ) {
                    window.location.href = "/e-pharma/public/login.php";
                    return;
                }

                alert("AJAX error");
            }
        });
    });

});
