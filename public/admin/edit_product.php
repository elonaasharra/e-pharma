<?php
$page_title = "Edit Product";

require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/admin/header.php';

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: /e-pharma/public/admin/products.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, name, description, price, stock, category_slug, image, is_active FROM products WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$product) {
    die("Product not found");
}

// kategorite fikse qe ke
$categories = [
    'dermokozmetike' => 'Dermo Cosmetic',
    'baby'           => 'Mom & kids',
    'suplemente'     => 'Suplemenete',
    'skincare'       => 'Skincare',
    'haircare'       => 'Haircare',
    'oralcare'       => 'Oral care',
    'higjiene'       => 'Higjiene',
];
?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Edit Product #<?php echo (int)$product["id"]; ?></h2>
        <a class="btn btn-outline-secondary" href="/e-pharma/public/admin/products.php">← Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="editProductForm" novalidate>
                <input type="hidden" id="product_id" name="product_id" value="<?php echo (int)$product["id"]; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product["name"]); ?>">
                        <div id="name_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                        <input class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product["price"]); ?>">
                        <div id="price_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stock</label>
                        <input class="form-control" id="stock" name="stock" value="<?php echo (int)$product["stock"]; ?>">
                        <div id="stock_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="category_slug" name="category_slug">
                            <?php foreach ($categories as $slug => $label): ?>
                                <option value="<?php echo htmlspecialchars($slug); ?>"
                                    <?php echo ($slug === $product["category_slug"]) ? "selected" : ""; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="cat_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Image (path/URL)</label>
                        <input class="form-control" id="image" name="image" value="<?php echo htmlspecialchars($product["image"] ?? ""); ?>">
                        <div class="form-text">Shembull: /e-pharma/public/uploads/xxx.jpg</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product["description"] ?? ""); ?></textarea>
                        <div id="desc_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Active</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" <?php echo ((int)$product["is_active"] === 1) ? "selected" : ""; ?>>Yes</option>
                            <option value="0" <?php echo ((int)$product["is_active"] === 0) ? "selected" : ""; ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save changes</button>
                    <a class="btn btn-light" href="/e-pharma/public/admin/products.php">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<?php
$page_scripts = '
<script>
$(function(){
  $("#editProductForm").on("submit", function(e){
    e.preventDefault();

    $("#name_msg,#price_msg,#stock_msg,#cat_msg,#desc_msg").text("");

    const id = $("#product_id").val();
    const name = $("#name").val().trim();
    const price = $("#price").val().trim();
    const stock = $("#stock").val().trim();
    const category_slug = $("#category_slug").val();
    const image = $("#image").val().trim();
    const description = $("#description").val().trim();
    const is_active = $("#is_active").val();

    let err = 0;

    if(name.length < 2){ $("#name_msg").text("Name is too short"); err++; }

    const priceNum = parseFloat(price);
    if(isNaN(priceNum) || priceNum <= 0){ $("#price_msg").text("Invalid price"); err++; }

    const stockNum = parseInt(stock, 10);
    if(isNaN(stockNum) || stockNum < 0){ $("#stock_msg").text("Invalid stock"); err++; }

    if(!category_slug){ $("#cat_msg").text("Select category"); err++; }

    if(err > 0) return;

    $.ajax({
      type: "POST",
      url: "/e-pharma/public/ajax/ajax_admin_product.php",
      dataType: "json",
      data: {
        action: "update_product",
        id: id,
        name: name,
        price: price,
        stock: stock,
        category_slug: category_slug,
        image: image,
        description: description,
        is_active: is_active
      },
      success: function(res){
        alert(res.message);
        if(res.status === "success"){
          window.location.href = "/e-pharma/public/admin/products.php";
        }
      },
      error: function(xhr){
        console.log(xhr.responseText);
        alert("Server error");
      }
    });
  });
});
</script>
';
include_once __DIR__ . '/../../includes/admin/footer.php';
