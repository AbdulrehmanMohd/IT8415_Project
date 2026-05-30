<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<h1 class="mb-1"><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
<p class="text-muted mb-4">Live statistics — auto-refreshes every 5 seconds</p>

<!-- STAT CARDS -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <i class="bi bi-people fs-2" style="color:#e94560;"></i>
            <h5 class="mt-2">Users</h5>
            <h2 id="users">...</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <i class="bi bi-box-seam fs-2" style="color:#e94560;"></i>
            <h5 class="mt-2">Products</h5>
            <h2 id="products">...</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <i class="bi bi-cart-check fs-2" style="color:#e94560;"></i>
            <h5 class="mt-2">Orders</h5>
            <h2 id="orders">...</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <i class="bi bi-currency-dollar fs-2" style="color:#e94560;"></i>
            <h5 class="mt-2">Revenue</h5>
            <h2 id="revenue">...</h2>
        </div>
    </div>
</div>

<!-- QUICK LINKS -->
<div class="row">
    <div class="col-md-3 mb-3">
        <a href="manage_users.php" class="card p-3 text-center text-white" style="text-decoration:none;">
            <i class="bi bi-people fs-3"></i>
            <p class="mt-2 mb-0">Manage Users</p>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="manage_products.php" class="card p-3 text-center text-white" style="text-decoration:none;">
            <i class="bi bi-box-seam fs-3"></i>
            <p class="mt-2 mb-0">Manage Products</p>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="add_product.php" class="card p-3 text-center text-white" style="text-decoration:none;">
            <i class="bi bi-plus-circle fs-3"></i>
            <p class="mt-2 mb-0">Add Product</p>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="reports.php" class="card p-3 text-center text-white" style="text-decoration:none;">
            <i class="bi bi-graph-up fs-3"></i>
            <p class="mt-2 mb-0">Reports</p>
        </a>
    </div>
</div>

<script>
function loadStats() {
    fetch('../ajax/dashboard_stats.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById('users').innerText    = data.users;
            document.getElementById('products').innerText = data.products;
            document.getElementById('orders').innerText   = data.orders;
            document.getElementById('revenue').innerText  = '$' + parseFloat(data.revenue).toFixed(2);
        });
}
loadStats();
setInterval(loadStats, 5000);
</script>

<?php include("../includes/footer.php"); ?>