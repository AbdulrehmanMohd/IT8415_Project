<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<h4>Admin Dashboard</h4>
<p class="text-muted">Live stats — updates every 5 seconds</p>
<hr>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Users</div>
            <h3 id="users" class="mt-1">...</h3>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Products</div>
            <h3 id="products" class="mt-1">...</h3>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Orders</div>
            <h3 id="orders" class="mt-1">...</h3>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Revenue</div>
            <h3 id="revenue" class="mt-1">...</h3>
        </div>
    </div>
</div>
<h5>Quick Links</h5>
<hr>
<div class="row">
    <div class="col-md-3 mb-3">
        <a href="manage_users.php" class="card p-4 text-center d-block"
           style="text-decoration:none; color:#1a2456; border: 2px solid #1a2456;">
            <div style="font-size:2rem;">👥</div>
            <div class="mt-2 fw-bold">Manage Users</div>
            <div class="text-muted small">View, edit, delete users</div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="manage_products.php" class="card p-4 text-center d-block"
           style="text-decoration:none; color:#1a2456; border: 2px solid #1a2456;">
            <div style="font-size:2rem;">📦</div>
            <div class="mt-2 fw-bold">Manage Products</div>
            <div class="text-muted small">View, remove products</div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="add_product.php" class="card p-4 text-center d-block"
           style="text-decoration:none; color:#1a2456; border: 2px solid #1a2456;">
            <div style="font-size:2rem;">➕</div>
            <div class="mt-2 fw-bold">Add Product</div>
            <div class="text-muted small">Create a new listing</div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="reports.php" class="card p-4 text-center d-block"
           style="text-decoration:none; color:#1a2456; border: 2px solid #1a2456;">
            <div style="font-size:2rem;">📊</div>
            <div class="mt-2 fw-bold">Reports</div>
            <div class="text-muted small">Sales & product reports</div>
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
        })
        .catch(() => {});
}
loadStats();
setInterval(loadStats, 5000);
</script>

<?php include("../includes/footer.php"); ?>