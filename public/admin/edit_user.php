<?php
include_once __DIR__ . '/../../includes/login/header.php';

require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid ID");

$stmt = mysqli_prepare($conn, "SELECT id, name, surname, email, role_id, profile_photo FROM users WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$user) die("User not found");

$roles = mysqli_query($conn, "SELECT id, name FROM roles ORDER BY id ASC");
if (!$roles) die("DB error roles: " . mysqli_error($conn));
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit User</title>
</head>
<body>

<h2>Edit User #<?php echo (int)$user["id"]; ?></h2>

<form id="editUserForm" novalidate>
    <input type="hidden" id="user_id" name="user_id" value="<?php echo (int)$user["id"]; ?>">

    <div>
        <label>Name</label><br>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user["name"]); ?>">
        <span id="name_msg" style="color:red;"></span>
    </div><br>

    <div>
        <label>Surname</label><br>
        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user["surname"]); ?>">
        <span id="surname_msg" style="color:red;"></span>
    </div><br>

    <div>
        <label>Email</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>">
        <span id="email_msg" style="color:red;"></span>
    </div><br>

    <div>
        <label>New Password (optional)</label><br>
        <input type="password" id="new_password" name="new_password" placeholder="Leave empty to keep current">
        <span id="pass_msg" style="color:red;"></span>
    </div><br>

    <div>
        <label>Role</label><br>
        <select id="role_id" name="role_id">
            <?php while ($r = mysqli_fetch_assoc($roles)): ?>
                <option value="<?php echo (int)$r["id"]; ?>" <?php echo ((int)$r["id"] === (int)$user["role_id"]) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($r["name"]); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div><br>

    <button type="submit">Save</button>
</form>

<p><a href="/e-pharma/public/admin/users.php">Back</a></p>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $("#editUserForm").on("submit", function(e){
        e.preventDefault();

        // clear messages
        $("#name_msg,#surname_msg,#email_msg,#pass_msg").text("");

        const id = $("#user_id").val();
        const name = $("#name").val().trim();
        const surname = $("#surname").val().trim();
        const email = $("#email").val().trim();
        const role_id = $("#role_id").val();
        const new_password = $("#new_password").val();

        const alpha = /^[a-zA-Z]{2,40}$/;
        let err = 0;

        if(!alpha.test(name)){ $("#name_msg").text("Invalid name"); err++; }
        if(!alpha.test(surname)){ $("#surname_msg").text("Invalid surname"); err++; }
        if(email.length < 5 || email.indexOf("@") === -1){ $("#email_msg").text("Invalid email"); err++; }

        if(new_password.length > 0 && new_password.length < 8){
            $("#pass_msg").text("New password must be at least 8 characters");
            err++;
        }

        if(err > 0) return;

        $.ajax({
            type: "POST",
            url: "/e-pharma/public/ajax/ajax_admin_user.php",
            dataType: "json",
            data: {
                action: "update_user",
                id: id,
                name: name,
                surname: surname,
                email: email,
                role_id: role_id,
                new_password: new_password
            },
            success: function(res){
                alert(res.message);
                if(res.status === "success"){
                    window.location.href = "/e-pharma/public/admin/users.php";
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);
                alert("Server error (check console)");
            }
        });
    });
</script>
<?php
include_once __DIR__ . '/../../includes/login/footer.php';


