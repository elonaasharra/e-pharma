<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../admin_auth.php'; // siguron vetem admin
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title ?? 'Admin Panel', ENT_QUOTES, 'UTF-8'); ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- (Opsionale) admin css -->
    <link rel="stylesheet" href="/e-pharma/public/assets/css/admin.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/e-pharma/public/admin/dashboard.php">Admin Panel</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/e-pharma/public/admin/dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/e-pharma/public/admin/users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/e-pharma/public/admin/add_user.php">Add User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/e-pharma/public/admin/products.php">Products</a>
                </li>

            </ul>

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/e-pharma/public/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
