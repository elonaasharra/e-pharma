<?php
include_once __DIR__ . '/../../includes/login/header.php';

require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

// Total users
$r1 = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users");
$row1 = $r1 ? mysqli_fetch_assoc($r1) : ["total_users" => 0];

// Total admins (role_id = 2)
$r2 = mysqli_query($conn, "SELECT COUNT(*) AS total_admins FROM users WHERE role_id = 2");
$row2 = $r2 ? mysqli_fetch_assoc($r2) : ["total_admins" => 0];

// Verified / not verified
$r3 = mysqli_query($conn, "SELECT COUNT(*) AS verified_users FROM users WHERE is_verified = 1");
$row3 = $r3 ? mysqli_fetch_assoc($r3) : ["verified_users" => 0];

$r4 = mysqli_query($conn, "SELECT COUNT(*) AS not_verified_users FROM users WHERE is_verified = 0");
$row4 = $r4 ? mysqli_fetch_assoc($r4) : ["not_verified_users" => 0];

// Last 5 users
$r5 = mysqli_query($conn, "
    SELECT id, name, surname, email, role_id, is_verified, created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT 5
");
if (!$r5) {
    die("DB error: " . mysqli_error($conn));
}

$total_users = (int)$row1["total_users"];
$total_admins = (int)$row2["total_admins"];
$verified_users = (int)$row3["verified_users"];
$not_verified_users = (int)$row4["not_verified_users"];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
</head>
<body>

<h2>Admin Dashboard</h2>

<p>
    <a href="/e-pharma/public/admin/users.php">Manage Users</a> |
    <a href="/e-pharma/public/logout.php">Logout</a>
</p>

<hr>

<h3>Statistics</h3>
<ul>
    <li><b>Total users:</b> <?php echo $total_users; ?></li>
    <li><b>Total admins:</b> <?php echo $total_admins; ?></li>
    <li><b>Verified users:</b> <?php echo $verified_users; ?></li>
    <li><b>Not verified users:</b> <?php echo $not_verified_users; ?></li>
</ul>

<hr>

<h3>Last 5 registered users</h3>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>ID</th><th>Name</th><th>Surname</th><th>Email</th><th>Role</th><th>Verified</th><th>Created at</th>
    </tr>

    <?php while($u = mysqli_fetch_assoc($r5)): ?>
        <tr>
            <td><?php echo (int)$u["id"]; ?></td>
            <td><?php echo htmlspecialchars($u["name"]); ?></td>
            <td><?php echo htmlspecialchars($u["surname"]); ?></td>
            <td><?php echo htmlspecialchars($u["email"]); ?></td>
            <td><?php echo (int)$u["role_id"]; ?></td>
            <td><?php echo (int)$u["is_verified"]; ?></td>
            <td><?php echo htmlspecialchars($u["created_at"]); ?></td>
        </tr>
    <?php endwhile; ?>
</table>
<?php
include_once __DIR__ . '/../../includes/login/footer.php';
