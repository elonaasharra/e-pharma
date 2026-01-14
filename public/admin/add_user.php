<?php

require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

$roles = mysqli_query($conn, "SELECT id, name FROM roles ORDER BY id ASC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add User</title></head>
<body>

<h2>Add User</h2>

<form id="addUserForm">
    <div>
        <label>Name</label><br>
        <input name="name" id="name">
    </div><br>

    <div>
        <label>Surname</label><br>
        <input name="surname" id="surname">
    </div><br>

    <div>
        <label>Email</label><br>
        <input name="email" id="email" type="email">
    </div><br>

    <div>
        <label>Password</label><br>
        <input name="password" id="password" type="password">
    </div><br>

    <div>
        <label>Role</label><br>
        <select name="role_id" id="role_id">
            <?php while($r = mysqli_fetch_assoc($roles)): ?>
                <option value="<?php echo (int)$r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
            <?php endwhile; ?>
        </select>
    </div><br>

    <button type="submit">Create</button>
</form>

<p><a href="/e-pharma/public/admin/users.php">Back</a></p>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $("#addUserForm").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "/e-pharma/public/ajax/ajax_admin_user.php",
            dataType: "json",
            data: {
                action: "add_user",
                name: $("#name").val().trim(),
                surname: $("#surname").val().trim(),
                email: $("#email").val().trim(),
                password: $("#password").val(),
                role_id: $("#role_id").val()
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
