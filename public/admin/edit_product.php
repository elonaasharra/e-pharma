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

            <!-- ✅ Mesazhet ketu (jo popup localhost) -->
            <div id="prodAlert" class="alert d-none" role="alert"></div>

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

                    <!-- ✅ Foto aktuale + upload i ri opsional -->
                    <div class="col-md-6">
                        <label class="form-label">Current image</label>
                        <?php if (!empty($product["image"])): ?>
                            <div class="mb-2">
                                <img src="<?php echo htmlspecialchars($product["image"]); ?>" style="max-width:120px; height:auto; border-radius:8px;">
                            </div>
                        <?php else: ?>
                            <div class="form-text">No image</div>
                        <?php endif; ?>

                        <label class="form-label mt-2">Change image (optional)</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*">
                        <div id="image_msg" class="form-text text-danger"></div>
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

  function showProdAlert(msg, type){
    $("#prodAlert")
      .removeClass("d-none alert-success alert-danger alert-warning alert-info")
      .addClass("alert-" + type)
      .text(msg);
  }

  $("#editProductForm").on("submit", function(e){
    e.preventDefault();

    $("#name_msg,#price_msg,#stock_msg,#cat_msg,#desc_msg,#image_msg").text("");
    $("#prodAlert").addClass("d-none");

    const id = $("#product_id").val();
    const name = $("#name").val().trim();
    const price = $("#price").val().trim();
    const stock = $("#stock").val().trim();
    const category_slug = $("#category_slug").val();
    const description = $("#description").val().trim();
    const is_active = $("#is_active").val();
    const file = $("#image")[0].files[0] || null; // ✅ opsionale

    let err = 0;

    if(name.length < 2){ $("#name_msg").text("Name is too short"); err++; }

    const priceNum = parseFloat(price);
    if(isNaN(priceNum) || priceNum <= 0){ $("#price_msg").text("Invalid price"); err++; }

    const stockNum = parseInt(stock, 10);
    if(isNaN(stockNum) || stockNum < 0){ $("#stock_msg").text("Invalid stock"); err++; }

    if(!category_slug){ $("#cat_msg").text("Select category"); err++; }

    if(err > 0) return;

    // ✅ FormData (me file nese ekziston)
    let fd = new FormData();
    fd.append("action", "update_product");
    fd.append("id", id);
    fd.append("name", name);
    fd.append("price", price);
    fd.append("stock", stock);
    fd.append("category_slug", category_slug);
    fd.append("description", description);
    fd.append("is_active", is_active);
    if (file) fd.append("image", file);

    $.ajax({
      type: "POST",
      url: "/e-pharma/public/ajax/ajax_admin_product.php",
      data: fd,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function(res){
        showProdAlert(res.message, res.status === "success" ? "success" : "danger");
        if(res.status === "success"){
          setTimeout(function(){
            window.location.href = "/e-pharma/public/admin/products.php";
          }, 900);
        }
      },
      error: function(xhr){
        console.log(xhr.responseText);
        showProdAlert("Server error", "danger");
      }
    });
  });
});
</script>
';
include_once __DIR__ . '/../../includes/admin/footer.php';
