<?php
// includes/cart.php
require_once __DIR__ . '/db.php';

/**
 * Merr cart-in aktiv të user-it; nëse s'ka, e krijon.
 * @return int cart_id
 */
function cart_get_or_create_active(mysqli $conn, int $user_id): int
{
    $sql = "SELECT id FROM carts WHERE user_id = ? AND status = 'active' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return (int)$row['id'];
    }

    $sql = "INSERT INTO carts (user_id, status) VALUES (?, 'active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    return (int)$conn->insert_id;
}

/**
 * Shto produkt në shportë.
 * - nëse ekziston rreshti (cart_id, product_id) => rrit quantity
 * - përndryshe => krijon rresht të ri
 * RREGULLIM: kontroll stokun dhe mos lejo sasi > stok.
 */
function cart_add_item(mysqli $conn, int $user_id, int $product_id, int $qty = 1): array
{
    if ($qty < 1) $qty = 1;

    $cart_id = cart_get_or_create_active($conn, $user_id);

    // Merr çmimin + stok + aktiv nga products
    $sql = "SELECT price, stock, is_active FROM products WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $p = $res->fetch_assoc();

    if (!$p) {
        return ["ok" => false, "error" => "Produkti nuk ekziston."];
    }
    if ((int)$p["is_active"] !== 1) {
        return ["ok" => false, "error" => "Produkti nuk është aktiv."];
    }

    $unit_price = (float)$p["price"];
    $stock = (int)$p["stock"];

    if ($stock <= 0) {
        return ["ok" => false, "error" => "Nuk ka stok për këtë produkt."];
    }

    // Sa është aktualisht në shportë?
    $sql = "SELECT quantity FROM cart_items WHERE cart_id = ? AND product_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $current_qty = $row ? (int)$row['quantity'] : 0;

    // Sasia e re e kërkuar
    $new_qty = $current_qty + $qty;

    if ($new_qty > $stock) {
        return [
            "ok" => false,
            "error" => "Sasia e kërkuar kalon stokun. Stok aktual: {$stock}, në shportë: {$current_qty}."
        ];
    }

    // Provo update (nëse ekziston)
    $sql = "UPDATE cart_items
            SET quantity = quantity + ?, unit_price = ?, updated_at = CURRENT_TIMESTAMP
            WHERE cart_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idii", $qty, $unit_price, $cart_id, $product_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        // nuk ekzistonte -> insert
        $sql = "INSERT INTO cart_items (cart_id, product_id, quantity, unit_price)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $cart_id, $product_id, $qty, $unit_price);
        $stmt->execute();
    }

    return ["ok" => true, "cart_id" => $cart_id];
}

/** Fshi produktin nga shporta aktive */
function cart_remove_item(mysqli $conn, int $user_id, int $product_id): array
{
    $sql = "SELECT id FROM carts WHERE user_id = ? AND status = 'active' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if (!$row) {
        return ["ok" => true];
    }

    $cart_id = (int)$row["id"];

    $sql = "DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();

    return ["ok" => true];
}

/** Numri total i artikujve (shuma e quantity) në shportën aktive */
function cart_count_items(mysqli $conn, int $user_id): int
{
    $sql = "SELECT COALESCE(SUM(ci.quantity), 0) AS cnt
            FROM carts c
            LEFT JOIN cart_items ci ON ci.cart_id = c.id
            WHERE c.user_id = ? AND c.status = 'active'";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return 0;

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;

    return $row ? (int)$row["cnt"] : 0;
}

function cart_get_total(mysqli $conn, int $user_id): float
{
    $sql = "SELECT COALESCE(SUM(ci.quantity * ci.unit_price), 0) AS total
            FROM carts c
            LEFT JOIN cart_items ci ON ci.cart_id = c.id
            WHERE c.user_id = ? AND c.status = 'active'";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return 0.0;

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;

    return $row ? (float)$row["total"] : 0.0;
}

function cart_get_items(mysqli $conn, int $user_id): array
{
    $sql = "SELECT
                ci.product_id,
                p.name AS product_name,
                ci.unit_price,
                ci.quantity,
                (ci.unit_price * ci.quantity) AS line_total
            FROM carts c
            JOIN cart_items ci ON ci.cart_id = c.id
            JOIN products p ON p.id = ci.product_id
            WHERE c.user_id = ? AND c.status = 'active'
            ORDER BY ci.id ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $res = $stmt->get_result();
    $items = [];

    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
    }

    return $items;
}
