<?php
require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';

function bad_request($msg) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

function server_error($msg, $details = '') {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $msg, 'details' => $details]);
    exit;
}

/**
 * Upload image (optional). Returns public path like /e-pharma/public/uploads/xxx.jpg
 */
function handle_optional_image_upload() {
    if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return null; // no file uploaded
    }

    $tmp  = $_FILES['image']['tmp_name'];
    $size = (int)($_FILES['image']['size'] ?? 0);

    if ($size <= 0) bad_request("Invalid image upload");
    if ($size > 3 * 1024 * 1024) bad_request("Image too large (max 3MB)");

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $tmp) : '';
    if ($finfo) finfo_close($finfo);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];
    if (!isset($allowed[$mime])) bad_request("Invalid image type (jpg/png/webp)");

    $dirFs = __DIR__ . '/../uploads/';
    if (!is_dir($dirFs)) {
        if (!@mkdir($dirFs, 0755, true)) server_error("Cannot create uploads directory");
    }

    $filename = 'p_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $destFs = $dirFs . $filename;

    if (!move_uploaded_file($tmp, $destFs)) server_error("Upload failed");

    return '/e-pharma/public/uploads/' . $filename;
}

if ($action === 'disable_product') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) bad_request("Invalid product id");

    $stmt = mysqli_prepare($conn, "UPDATE products SET is_active = 0 WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);

    if (!$ok) {
        $err = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        server_error("DB error", $err);
    }

    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found or already disabled']);
        exit;
    }

    echo json_encode(['status' => 'success', 'message' => 'Product disabled successfully']);
    exit;
}

if ($action === 'add_product') {
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '0');
    $category_slug = trim($_POST['category_slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = (int)($_POST['is_active'] ?? 1);

    if (mb_strlen($name) < 2) bad_request("Invalid name");
    if (!is_numeric($price) || (float)$price <= 0) bad_request("Invalid price");
    if (!is_numeric($stock) || (int)$stock < 0) bad_request("Invalid stock");
    if ($category_slug === '') bad_request("Invalid category");

    $price_f = (float)$price;
    $stock_i = (int)$stock;
    $is_active = ($is_active === 1) ? 1 : 0;

    // ✅ optional image upload
    $image = '';
    $uploadedPath = handle_optional_image_upload();
    if ($uploadedPath !== null) {
        $image = $uploadedPath;
    }

    $stmt = mysqli_prepare($conn, "
        INSERT INTO products (name, description, price, stock, category_slug, image, is_active, sold_count)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)
    ");
    mysqli_stmt_bind_param($stmt, "ssdissi", $name, $description, $price_f, $stock_i, $category_slug, $image, $is_active);
    $ok = mysqli_stmt_execute($stmt);

    if (!$ok) {
        $err = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        server_error("DB error", $err);
    }

    mysqli_stmt_close($stmt);
    echo json_encode(['status' => 'success', 'message' => 'Product added successfully']);
    exit;
}

if ($action === 'update_product') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) bad_request("Invalid product id");

    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '0');
    $category_slug = trim($_POST['category_slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = (int)($_POST['is_active'] ?? 1);

    if (mb_strlen($name) < 2) bad_request("Invalid name");
    if (!is_numeric($price) || (float)$price <= 0) bad_request("Invalid price");
    if (!is_numeric($stock) || (int)$stock < 0) bad_request("Invalid stock");
    if ($category_slug === '') bad_request("Invalid category");

    $price_f = (float)$price;
    $stock_i = (int)$stock;
    $is_active = ($is_active === 1) ? 1 : 0;

    // ✅ keep existing image by default
    $image = '';
    $stmt0 = mysqli_prepare($conn, "SELECT image FROM products WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($stmt0, "i", $id);
    mysqli_stmt_execute($stmt0);
    $r0 = mysqli_stmt_get_result($stmt0);
    $old = $r0 ? mysqli_fetch_assoc($r0) : null;
    mysqli_stmt_close($stmt0);
    $image = $old['image'] ?? '';

    // ✅ if new file uploaded, replace
    $uploadedPath = handle_optional_image_upload();
    if ($uploadedPath !== null) {
        $image = $uploadedPath;
    }

    $stmt = mysqli_prepare($conn, "
        UPDATE products
        SET name=?, description=?, price=?, stock=?, category_slug=?, image=?, is_active=?
        WHERE id=?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "ssdissii", $name, $description, $price_f, $stock_i, $category_slug, $image, $is_active, $id);
    $ok = mysqli_stmt_execute($stmt);

    if (!$ok) {
        $err = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        server_error("DB error", $err);
    }

    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected === 0) {
        echo json_encode(['status' => 'success', 'message' => 'No changes (or product not found)']);
        exit;
    }

    echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
    exit;
}

bad_request("Invalid action");
