<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/login/header.php';

$user_id = (int)$_SESSION["user_id"];

$r = mysqli_query(
        $conn,
        "SELECT name, surname, email, profile_photo
     FROM users
     WHERE id = $user_id
     LIMIT 1"
);

$user = mysqli_fetch_assoc($r);
if (!$user) {
    die("User not found");
}
?>

    <div class="container py-4 edit-profile-page">

        <div class="card shadow-sm edit-profile-card">
            <div class="card-body">

                <h3 class="mb-3">Edit Profile</h3>

                <form id="editProfileForm" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input
                                type="text"
                                class="form-control"
                                name="name"
                                id="name"
                                value="<?php echo htmlspecialchars($user["name"]); ?>"
                        >
                        <div id="name_message" class="text-danger small"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Surname</label>
                        <input
                                type="text"
                                class="form-control"
                                name="surname"
                                id="surname"
                                value="<?php echo htmlspecialchars($user["surname"]); ?>"
                        >
                        <div id="surname_message" class="text-danger small"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email (read-only)</label>
                        <input
                                type="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($user["email"]); ?>"
                                readonly
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profile photo</label>
                        <input
                                type="file"
                                class="form-control"
                                name="photo"
                                id="photo"
                                accept="image/*"
                        >
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="/e-pharma/public/user/profile.php" class="btn btn-outline-secondary">
                            Back to profile
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>

<?php
include_once __DIR__ . '/../../includes/login/footer.php';
