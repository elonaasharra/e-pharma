<?php
$page_title = "Add User";

require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/admin/header.php';

$roles = mysqli_query($conn, "SELECT id, name FROM roles ORDER BY id ASC");
if (!$roles) {
    die("DB error roles: " . mysqli_error($conn));
}
?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Add User</h2>
        <a class="btn btn-outline-secondary" href="/e-pharma/public/admin/users.php">← Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="addUserForm" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" id="name" type="text" placeholder="Enter name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Surname</label>
                        <input class="form-control" name="surname" id="surname" type="text" placeholder="Enter surname">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input class="form-control" name="email" id="email" type="email" placeholder="example@mail.com">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input class="form-control" name="password" id="password" type="password" placeholder="Min 8 characters">
                        <div class="form-text">Choose a strong password.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role_id" id="role_id">
                            <?php while($r = mysqli_fetch_assoc($roles)): ?>
                                <option value="<?php echo (int)$r['id']; ?>">
                                    <?php echo htmlspecialchars($r['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="/e-pharma/public/admin/users.php" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>

 <?php   $page_scripts = '
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
    '?>

<?php
include_once __DIR__ . '/../../includes/admin/footer.php';
