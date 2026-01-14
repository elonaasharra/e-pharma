<?php

require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

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
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delete User</title>
</head>
<body>

<h2>Delete User</h2>

<p>Are you sure you want to delete this user?</p>

<ul>
    <li><b>ID:</b> <?php echo (int)$user["id"]; ?></li>
    <li><b>Name:</b> <?php echo htmlspecialchars($user["name"]); ?></li>
    <li><b>Surname:</b> <?php echo htmlspecialchars($user["surname"]); ?></li>
    <li><b>Email:</b> <?php echo htmlspecialchars($user["email"]); ?></li>
    <li><b>Role ID:</b> <?php echo (int)$user["role_id"]; ?></li>
</ul>

<button id="btnDelete">Yes, delete</button>
<a href="/e-pharma/public/admin/users.php">Cancel</a>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

</body>
</html>
