<?php
$page_title = "Admin - Products";

require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/admin/header.php';

$r = mysqli_query($conn, "
    SELECT id, name, price, stock, category_slug, is_active, image
    FROM products
    ORDER BY id DESC
");
if (!$r) die("DB error: " . mysqli_error($conn));
?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Products</h2>
        <a class="btn btn-primary" href="/e-pharma/public/admin/add_product.php">+ Add product</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Active</th>
                <th style="width:180px;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while($p = mysqli_fetch_assoc($r)): ?>
                <tr id="row-<?php echo (int)$p['id']; ?>">
                    <td><?php echo (int)$p['id']; ?></td>

                    <td style="width:90px">
                        <?php if (!empty($p['image'])): ?>
                            <img
                                src="<?php echo htmlspecialchars($p['image']); ?>"
                                style="width:60px;height:60px;object-fit:cover;border-radius:8px;"
                                alt="product"
                            >
                        <?php else: ?>
                            <span class="text-muted small">No image</span>
                        <?php endif; ?>
                    </td>

                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['category_slug']); ?></td>
                    <td><?php echo number_format((float)$p['price'], 2); ?></td>
                    <td><?php echo (int)$p['stock']; ?></td>

                    <td>
                        <?php if ((int)$p['is_active'] === 1): ?>
                            <span class="badge bg-success">Yes</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">No</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a class="btn btn-sm btn-warning" href="/e-pharma/public/admin/edit_product.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
                        <button class="btn btn-sm btn-danger btnDel" data-id="<?php echo (int)$p['id']; ?>">Disable</button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<?php
$page_scripts = '
<script>
$(function(){
    $(".btnDel").on("click", function(){
        const id = $(this).data("id");
        if(!confirm("Disable product #" + id + " ?")) return;

        $.ajax({
            type: "POST",
            url: "/e-pharma/public/ajax/ajax_admin_product.php",
            dataType: "json",
            data: { action: "disable_product", id: id },
            success: function(res){
                alert(res.message);
                if(res.status === "success"){
                    $("#row-"+id).remove();
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
?>


<?php
include_once __DIR__ . '/../../includes/admin/footer.php';
