<?php
include_once __DIR__ . '/../../includes/login/header.php';

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

$user_id = (int)$_SESSION["user_id"];

$r = mysqli_query($conn, "SELECT name, surname, email, profile_photo FROM users WHERE id=".$user_id." LIMIT 1");
$user = mysqli_fetch_assoc($r);
if (!$user) { die("User not found"); }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Edit Profile</title></head>
<body>

<h3>Edit Profile</h3>

<form id="editProfileForm" enctype="multipart/form-data">
    <div>
        <label>Name</label><br>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user["name"]); ?>">
        <span id="name_message" style="color:red;"></span>
    </div><br>

    <div>
        <label>Surname</label><br>
        <input type="text" name="surname" id="surname" value="<?php echo htmlspecialchars($user["surname"]); ?>">
        <span id="surname_message" style="color:red;"></span>
    </div><br>

    <div>
        <label>Email (read-only)</label><br>
        <input type="email" value="<?php echo htmlspecialchars($user["email"]); ?>" readonly>
    </div><br>

    <div>
        <label>Profile photo</label><br>
        <input type="file" name="photo" id="photo" accept="image/*">
    </div><br>

    <button type="submit">Save</button>
</form>

<p><a href="/e-pharma/public/user/profile.php">Back to profile</a></p>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(function () {
        $("#editProfileForm").on("submit", function(e){
            e.preventDefault();

            var name = $("#name").val().trim();
            var surname = $("#surname").val().trim();
            var alpha = /^[a-zA-Z]{2,40}$/;
            var error = 0;

            $("#name_message").text(""); $("#surname_message").text("");

            if (!alpha.test(name)) { $("#name_message").text("Invalid name"); error++; }
            if (!alpha.test(surname)) { $("#surname_message").text("Invalid surname"); error++; }
            if (error>0) return;

            var data = new FormData(this);
            data.append("action","update_profile");

            $.ajax({
                type: "POST",
                url: "/e-pharma/public/ajax/ajax_update_profile.php",
                data: data,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(res){
                    alert(res.message);
                    // if(res.location){ window.location.href = res.location; }
                },
                error: function(){
                    alert("AJAX error");
                }
                // success: function(res){
                //     try {
                //         if (typeof res === "string") res = JSON.parse(res);
                //     } catch (e) {
                //         console.log("Not JSON response:", res);
                //         alert("Server returned non-JSON. Check console.");
                //         return;
                //     }
                //
                //     alert(res.message);
                //     if(res.location){ window.location.href = res.location; }
                // },
                // error: function(xhr){
                //     console.log(xhr.responseText);
                //     alert("AJAX error");
                // }

            });
        });
    });
</script>

<?php
include_once __DIR__ . '/../../includes/login/footer.php';
