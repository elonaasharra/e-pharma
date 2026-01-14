<?php
include_once __DIR__ . '/../../includes/login/header.php';

require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

$r = mysqli_query($conn, "
    SELECT u.id, u.name, u.surname, u.email, u.role_id, u.is_verified, u.created_at
    FROM users u
    ORDER BY u.id DESC
");

if (!$r) die("DB error: " . mysqli_error($conn));
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Users</title>
</head>
<body>

<h2>Users List</h2>
<p><a href="/e-pharma/public/admin/add_user.php">+ Add user</a></p>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th><th>Name</th><th>Surname</th><th>Email</th><th>Role</th><th>Verified</th><th>Actions</th>
    </tr>

    <?php while($u = mysqli_fetch_assoc($r)): ?>
        <tr id="row-<?php echo (int)$u['id']; ?>">
            <td><?php echo (int)$u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['name']); ?></td>
            <td><?php echo htmlspecialchars($u['surname']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo (int)$u['role_id']; ?></td>
            <td><?php echo (int)$u['is_verified']; ?></td>
            <td>
                <a href="/e-pharma/public/admin/edit_user.php?id=<?php echo (int)$u['id']; ?>">Edit</a>
                |
                <button class="btnDel" data-id="<?php echo (int)$u['id']; ?>">Delete</button>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

<p><a href="/e-pharma/public/logout.php">Logout</a></p>
<?php
include_once __DIR__ . '/../../includes/login/footer.php';

