<?php
$page_title = "Delete User";

require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/admin/header.php';

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    die("Invalid user id");
}

// merr user-in për ta shfaqur në konfirmim
$stmt = mysqli_prepare($conn, "SELECT id, name, surname, email, role_id FROM users WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$user) {
    die("User not found");
}
?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0 text-danger">Delete User</h2>
        <a class="btn btn-outline-secondary" href="/e-pharma/public/admin/users.php">← Back</a>
    </div>

    <div class="alert alert-warning">
        <strong>Warning!</strong> This action cannot be undone.
    </div>

    <div class="card shadow-sm border-danger">
        <div class="card-body">
            <p class="mb-3">Are you sure you want to delete this user?</p>

            <ul class="list-group mb-4">
                <li class="list-group-item"><b>ID:</b> <?php echo (int)$user["id"]; ?></li>
                <li class="list-group-item"><b>Name:</b> <?php echo htmlspecialchars($user["name"]); ?></li>
                <li class="list-group-item"><b>Surname:</b> <?php echo htmlspecialchars($user["surname"]); ?></li>
                <li class="list-group-item"><b>Email:</b> <?php echo htmlspecialchars($user["email"]); ?></li>
                <li class="list-group-item"><b>Role ID:</b> <?php echo (int)$user["role_id"]; ?></li>
            </ul>

            <div class="d-flex gap-2">
                <button id="btnDelete" class="btn btn-danger">Yes, delete</button>
                <a href="/e-pharma/public/admin/users.php" class="btn btn-light">Cancel</a>
            </div>
        </div>
    </div>
<?php
$page_scripts = '
    <script>
        $("#btnDelete").on("click", function(){
            if(!confirm("This action cannot be undone. Continue?")) return;

            $.ajax({
                type: "POST",
                url: "/e-pharma/public/ajax/ajax_admin_user.php",
                dataType: "json",
                data: {
                    action: "delete_user",
                    id: <?php echo (int)$user["id"]; ?>
                },
                success: function(res){
                    alert(res.message);
                    if(res.status === "success"){
                        window.location.href = "/e-pharma/public/admin/users.php";
                    }
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert("Server error");
                }
            });
        });
    </script>
'?>

<?php
include_once __DIR__ . '/../../includes/admin/footer.php';
