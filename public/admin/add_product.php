<?php
$page_title = "Add Product";

require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/admin/header.php';

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
        <h2 class="mb-0">Add Product</h2>
        <a class="btn btn-outline-secondary" href="/e-pharma/public/admin/products.php">← Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div id="prodAlert" class="alert d-none" role="alert"></div>

            <form id="addProductForm" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input class="form-control" id="name" name="name">
                        <div id="name_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                        <input class="form-control" id="price" name="price">
                        <div id="price_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stock</label>
                        <input class="form-control" id="stock" name="stock" value="0">
                        <div id="stock_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="category_slug" name="category_slug">
                            <?php foreach ($categories as $slug => $label): ?>
                                <option value="<?php echo htmlspecialchars($slug); ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
<!--
                    <div class="col-md-6">
                        <label class="form-label">Image (path/URL)</label>
                        <input class="form-control" id="image" name="image">
                        <div class="form-text">Shembull: /e-pharma/public/uploads/xxx.jpg</div>
                    </div>-->
                    <div class="col-md-6">
                        <label class="form-label">Image</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*">
                        <div id="image_msg" class="form-text text-danger"></div>
                    </div>


                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Active</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" selected>Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Create</button>
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

  $("#addProductForm").on("submit", function(e){
    e.preventDefault();

    $("#name_msg,#price_msg,#stock_msg,#image_msg").text("");
    $("#prodAlert").addClass("d-none");

    const name = $("#name").val().trim();
    const price = $("#price").val().trim();
    const stock = $("#stock").val().trim();
    const category_slug = $("#category_slug").val();
    const description = $("#description").val().trim();
    const is_active = $("#is_active").val();
    const file = $("#image")[0].files[0] || null;

    let err = 0;

    if(name.length < 2){ $("#name_msg").text("Name is too short"); err++; }

    const priceNum = parseFloat(price);
    if(isNaN(priceNum) || priceNum <= 0){ $("#price_msg").text("Invalid price"); err++; }

    const stockNum = parseInt(stock, 10);
    if(isNaN(stockNum) || stockNum < 0){ $("#stock_msg").text("Invalid stock"); err++; }

    if(err > 0) return;

    let fd = new FormData();
    fd.append("action", "add_product");
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
