<?php
require_once __DIR__ . '/../session.php';

/* Nëse s’është i loguar → login */
if (!isset($_SESSION["user_id"])) {
    header("Location: /e-pharma/public/login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Pharma</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">

    <!-- Local CSS (i njëjti stil si no_login) -->
    <link rel="stylesheet" href="/e-pharma/public/assets/css/register.css">
    <link rel="stylesheet" href="/e-pharma/public/assets/css/header_nologin.css">
    <link rel="stylesheet" href="/e-pharma/public/assets/css/footer_nologin.css">
</head>
<body>

<nav class="py-2 bg-light border-bottom">
    <div class="container d-flex flex-wrap">
        <ul class="nav me-auto">
            <li class="nav-item"><a href="/e-pharma/public/index.php" class="nav-link link-dark px-2 active">Home</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Products</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">About</a></li>

            <?php if ((int)($_SESSION["role_id"] ?? 0) === 2): ?>
                <li class="nav-item"><a href="/e-pharma/public/admin/users.php" class="nav-link link-dark px-2">Admin</a></li>
            <?php endif; ?>
        </ul>

        <ul class="nav">
            <li class="nav-item">
                <a href="/e-pharma/public/user/profile.php" class="nav-link link-dark px-2">
                    My Profile
                </a>
            </li>
            <li class="nav-item">
                <a href="/e-pharma/public/logout.php" class="nav-link link-dark px-2">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<header class="py-3 mb-4 border-bottom">
    <div class="container d-flex flex-wrap justify-content-center">
        <a href="/e-pharma/public/index.php"
           class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto text-dark text-decoration-none">
            <span class="fs-4">E-Pharma</span>
        </a>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0">
            <input type="search" class="form-control" placeholder="Search..." aria-label="Search">
        </form>
    </div>
</header>
