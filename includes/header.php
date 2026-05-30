<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ShopSphere</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #121212;
            color: white;
        }

        .navbar {
            background-color: #1f1f1f;
        }

        a {
            text-decoration: none;
        }

        .card {
            background-color: #1e1e1e;
            color: white;
            border: 1px solid #333;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark px-3">

    <a class="navbar-brand text-white" href="/shopsphere/index.php">
        ShopSphere
    </a>

    <div class="ms-auto">

        <a class="text-white me-3" href="/shopsphere/products.php">Products</a>
        <a class="text-white me-3" href="/shopsphere/cart.php">Cart</a>

        <?php if (isset($_SESSION['user_id'])) { ?>


            <?php if ($_SESSION['role'] == 'admin') { ?>
                <a class="text-white me-3" href="/shopsphere/admin/dashboard.php">Admin</a>
            <?php } ?>

            <a class="text-white me-3" href="/shopsphere/logout.php">Logout</a>

        <?php } else { ?>

            <a class="text-white me-3" href="/shopsphere/login.php">Login</a>
            <a class="text-white me-3" href="/shopsphere/register.php">Register</a>

        <?php } ?>

    </div>
</nav>

<div class="container mt-4">