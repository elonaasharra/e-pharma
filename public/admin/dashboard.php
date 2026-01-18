<?php
$page_title = "Admin Dashboard";

require_once __DIR__ . '/../../includes/db.php';
/** @var mysqli $conn */

include_once __DIR__ . '/../../includes/admin/header.php';

// -------------------- USERS STATS (existing functionality) --------------------

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

// -------------------- CHART DATA (READ ONLY) --------------------

$categoryMap = [
        'dermokozmetike' => 'Dermo Cosmetic',
        'baby'           => 'Mom & kids',
        'suplemente'     => 'Suplemenete',
        'skincare'       => 'Skincare',
        'haircare'       => 'Haircare',
        'oralcare'       => 'Oral care',
        'higjiene'       => 'Higjiene',
];

$soldMap = [
        'Dermo Cosmetic' => 0,
        'Mom & kids'     => 0,
        'Suplemenete'    => 0,
        'Skincare'       => 0,
        'Haircare'       => 0,
        'Oral care'      => 0,
        'Higjiene'       => 0,
];

$rc = mysqli_query($conn, "
    SELECT category_slug, SUM(sold_count) AS total_sold
    FROM products
    GROUP BY category_slug
    ORDER BY category_slug
");

if (!$rc) {
    die("DB error chart: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($rc)) {
    $slug = $row['category_slug'] ?? '';
    $niceName = $categoryMap[$slug] ?? $slug;
    $qty = (int)($row['total_sold'] ?? 0);
    $soldMap[$niceName] = $qty;
}

$chartLabels = array_keys($soldMap);
$chartData   = array_values($soldMap);
?>

    <h2 class="mb-3">Admin Dashboard</h2>

    <hr>

    <h4>Products sold by category</h4>
    <canvas id="myChart" height="120"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script>
        const chartLabels = <?php echo json_encode($chartLabels, JSON_UNESCAPED_UNICODE); ?>;
        const chartData   = <?php echo json_encode($chartData); ?>;

        new Chart(document.getElementById('myChart'), {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Total sold (qty)',
                    data: chartData
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true, precision: 0 }
                    }]
                }
            }
        });
    </script>

    <hr>

    <h4>Statistics</h4>
    <ul>
        <li><b>Total users:</b> <?php echo $total_users; ?></li>
        <li><b>Total admins:</b> <?php echo $total_admins; ?></li>
        <li><b>Verified users:</b> <?php echo $verified_users; ?></li>
        <li><b>Not verified users:</b> <?php echo $not_verified_users; ?></li>
    </ul>

    <hr>

    <h4>Last 5 registered users</h4>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Surname</th><th>Email</th><th>Role</th><th>Verified</th><th>Created at</th>
        </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>

<?php
include_once __DIR__ . '/../../includes/admin/footer.php';
