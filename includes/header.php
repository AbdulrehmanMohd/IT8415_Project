<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopSphere</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Bahrain Polytechnic Colors */
        :root {
            --bp-navy:  #1a2456;
            --bp-gold:  #c9a84c;
            --bp-light: #f4f6fb;
            --bp-accent: #e94560;
        }

        body {
            background-color: var(--bp-light);
            
            color: #1a1a1a;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: var(--bp-navy) !important;
            border-bottom: 3px solid var(--bp-gold);
        }

        .navbar-brand {
            color: var(--bp-gold) !important;
            font-weight: bold;
            font-size: 1.4rem;
        }

        .card {
            background-color: #ffffff;
            color: #1a1a1a;
            border: 1px solid #dde2ef;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        .card:hover {
            border-color: var(--bp-gold);
            transition: border-color 0.2s;
        }

        .btn-primary {
            background-color: var(--bp-navy);
            border-color: var(--bp-navy);
            color: white;
        }

        .btn-primary:hover {
            background-color: #111a3e;
            border-color: #111a3e;
        }

        .btn-outline-primary {
            color: var(--bp-navy);
            border-color: var(--bp-navy);
        }

        .btn-outline-primary:hover {
            background-color: var(--bp-navy);
            color: white;
        }

        .badge.bg-secondary {
            background-color: var(--bp-navy) !important;
        }

        .product-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .page-link {
            color: var(--bp-navy);
        }

        .page-item.active .page-link {
            background-color: var(--bp-navy);
            border-color: var(--bp-navy);
        }

        h4, h3, h2 {
            color: var(--bp-navy);
        }

        hr {
            border-color: var(--bp-gold);
            opacity: 0.4;
        }

        footer {
            background-color: var(--bp-navy);
            border-top: 3px solid var(--bp-gold);
            color: #ccc;
            padding: 15px;
            text-align: center;
            margin-top: 40px;
        }

        a { text-decoration: none; }

        /* Gold accent for stars */
        .star-gold { color: var(--bp-gold); }

        /* Top banner strip */
        .top-strip {
            background-color: var(--bp-gold);
            color: var(--bp-navy);
            font-size: 0.78rem;
            text-align: center;
            padding: 3px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Top gold strip -->
<div class="top-strip">IT8415 Database Programming 2 &nbsp;|&nbsp; Group Project &nbsp;|&nbsp; Bahrain Polytechnic</div>

<nav class="navbar navbar-expand-lg navbar-dark px-3">
    <a class="navbar-brand" href="/shopsphere/index.php">
        <i class="bi bi-cart4"></i> ShopSphere
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link text-white" href="/shopsphere/index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/shopsphere/products.php">Products</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="/shopsphere/about.php">About</a></li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link text-white" href="/shopsphere/cart.php">
                    <i class="bi bi-cart3"></i> Cart
                    <?php if (!empty($_SESSION['cart'])) { ?>
                        <span class="badge" style="background-color:var(--bp-gold); color:var(--bp-navy);">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    <?php } ?>
                </a>
            </li>

            <?php if (isset($_SESSION['user_id'])) { ?>
                <li class="nav-item">
                    <span class="nav-link" style="color:var(--bp-gold);">
                        Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </li>

                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <li class="nav-item"><a class="nav-link text-warning" href="/shopsphere/admin/dashboard.php">Admin Panel</a></li>
                <?php } elseif ($_SESSION['role'] == 'seller') { ?>
                    <li class="nav-item"><a class="nav-link text-info" href="/shopsphere/user/dashboard.php">My Products</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="/shopsphere/my_orders.php">My Orders</a></li>
                <?php } ?>

                <li class="nav-item"><a class="nav-link text-danger" href="/shopsphere/logout.php">Logout</a></li>
            <?php } else { ?>
                <li class="nav-item"><a class="nav-link text-white" href="/shopsphere/login.php">Login</a></li>
                <li class="nav-item">
                    <a class="nav-link fw-bold" style="color:var(--bp-gold);" href="/shopsphere/register.php">Register</a>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>

<div class="container mt-4">