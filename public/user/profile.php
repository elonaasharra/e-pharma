<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/login/header.php';
//nuk lejon adminin te hap profilin e userit
if ((int)($_SESSION["role_id"] ?? 0) === 2) {
    header("Location: /e-pharma/public/admin/dashboard.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];

$q = "SELECT name, surname, email, role_id, profile_photo
      FROM users
      WHERE id = $user_id
      LIMIT 1";
$r = mysqli_query($conn, $q);

if (!$r) {
    die("DB error: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($r);
if (!$user) {
    die("User not found");
}
?>

    <div class="container py-4 profile-page">

        <div class="card shadow-sm profile-card">
            <div class="card-body">

                <div class="d-flex flex-column flex-md-row gap-4 align-items-start">

                    <div class="profile-photo-wrap">
                        <?php if (!empty($user["profile_photo"])): ?>
                            <img
                                    class="profile-photo"
                                    src="/e-pharma/public/uploads/<?php echo htmlspecialchars($user["profile_photo"]); ?>"
                                    alt="Profile Photo"
                            >
                        <?php else: ?>
                            <div class="profile-photo-placeholder">
                                No photo
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-grow-1">
                        <h3 class="mb-3">My Profile</h3>

                        <div class="profile-lines">
                            <p><b>Name:</b> <?php echo htmlspecialchars($user["name"]); ?></p>
                            <p><b>Surname:</b> <?php echo htmlspecialchars($user["surname"]); ?></p>
                            <p><b>Email:</b> <?php echo htmlspecialchars($user["email"]); ?></p>
                            <p><b>Role ID:</b> <?php echo (int)$user["role_id"]; ?></p>
                        </div>

                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            <a class="btn btn-primary" href="/e-pharma/public/user/edit_profile.php">Edit Profile</a>
                            <a class="btn btn-outline-danger" href="/e-pharma/public/logout.php">Logout</a>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

<?php
include_once __DIR__ . '/../../includes/login/footer.php';
