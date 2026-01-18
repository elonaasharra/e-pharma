<?php
$page_title = "Admin - Users";
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/admin/header.php';

$r = mysqli_query($conn, "
    SELECT u.id, u.name, u.surname, u.email, u.role_id, u.is_verified, u.created_at
    FROM users u
    ORDER BY u.id DESC
");
if (!$r) die("DB error: " . mysqli_error($conn));
?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Users List</h2>
        <a class="btn btn-primary" href="/e-pharma/public/admin/add_user.php">+ Add user</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Surname</th><th>Email</th><th>Role</th><th>Verified</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while($u = mysqli_fetch_assoc($r)): ?>
                <tr id="row-<?php echo (int)$u['id']; ?>">
                    <td><?php echo (int)$u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['surname']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo (int)$u['role_id']; ?></td>
                    <td><?php echo (int)$u['is_verified']; ?></td>
                    <td>
                        <a class="btn btn-sm btn-warning" href="/e-pharma/public/admin/edit_user.php?id=<?php echo (int)$u['id']; ?>">Edit</a>
                        <button class="btn btn-sm btn-danger btnDel" data-id="<?php echo (int)$u['id']; ?>">Delete</button>
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
        if(!confirm("Delete user #" + id + " ?")) return;

        $.ajax({
            type: "POST",
            url: "/e-pharma/public/ajax/ajax_admin_user.php",
            dataType: "json",
            data: { action: "delete_user", id: id },
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
