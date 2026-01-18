<?php
$page_title = "Edit User";

require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/admin/header.php';

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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Edit User #<?php echo (int)$user["id"]; ?></h2>
        <a class="btn btn-outline-secondary" href="/e-pharma/public/admin/users.php">← Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="editUserForm" novalidate>
                <input type="hidden" id="user_id" name="user_id" value="<?php echo (int)$user["id"]; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                value="<?php echo htmlspecialchars($user["name"]); ?>"
                        >
                        <div id="name_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Surname</label>
                        <input
                                type="text"
                                class="form-control"
                                id="surname"
                                name="surname"
                                value="<?php echo htmlspecialchars($user["surname"]); ?>"
                        >
                        <div id="surname_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($user["email"]); ?>"
                        >
                        <div id="email_msg" class="form-text text-danger"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id">
                            <?php while ($r = mysqli_fetch_assoc($roles)): ?>
                                <option value="<?php echo (int)$r["id"]; ?>" <?php echo ((int)$r["id"] === (int)$user["role_id"]) ? "selected" : ""; ?>>
                                    <?php echo htmlspecialchars($r["name"]); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">New Password (optional)</label>
                        <input
                                type="password"
                                class="form-control"
                                id="new_password"
                                name="new_password"
                                placeholder="Leave empty to keep current"
                        >
                        <div id="pass_msg" class="form-text text-danger"></div>
                        <div class="form-text">Min 8 characters if you set a new one.</div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-light" href="/e-pharma/public/admin/users.php">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?php
$page_scripts = '
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
'?>

<?php
include_once __DIR__ . '/../../includes/admin/footer.php';
