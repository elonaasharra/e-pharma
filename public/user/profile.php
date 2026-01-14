<?php
include_once __DIR__ . '/../../includes/login/header.php';

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */
if (isset($_GET["id"])) {
    header("Location: /e-pharma/public/user/profile.php");
    exit;
}

$user_id = (int)$_SESSION["user_id"];

$q = "SELECT name, surname, email, role_id, profile_photo
      FROM users
      WHERE id = ".$user_id."
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
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Profile</title>
</head>
<body>

<h3>Profile (PRIVATE)</h3>

<p><b>Name:</b> <?php echo htmlspecialchars($user["name"]); ?></p>
<p><b>Surname:</b> <?php echo htmlspecialchars($user["surname"]); ?></p>
<p><b>Email:</b> <?php echo htmlspecialchars($user["email"]); ?></p>
<p><b>Role ID:</b> <?php echo (int)$user["role_id"]; ?></p>

<p><b>Profile photo:</b><br>
    <?php if (!empty($user["profile_photo"])): ?>
        <img src="/e-pharma/public/uploads/<?php echo htmlspecialchars($user["profile_photo"]); ?>"
             width="150" alt="Profile Photo">
    <?php else: ?>
        No photo
    <?php endif; ?>
</p>

<hr>

<p>
    <a href="/e-pharma/public/user/edit_profile.php">Edit Profile</a>
</p>

<p>
    <a href="/e-pharma/public/logout.php">Logout</a>
</p>

</body>
</html>
