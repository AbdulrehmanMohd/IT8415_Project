<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<h1>Admin Dashboard</h1>
<p>Live Stats (Auto Updating)</p>

<div class="row">

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h5>Users</h5>
            <h2 id="users">0</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h5>Products</h5>
            <h2 id="products">0</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h5>Orders</h5>
            <h2 id="orders">0</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h5>Revenue</h5>
            <h2 id="revenue">$0</h2>
        </div>
    </div>

</div>

<br>

<script>
function loadStats() {
    fetch('../ajax/dashboard_stats.php')
        .then(res => res.json())
        .then(data => {

            document.getElementById('users').innerText = data.users;
            document.getElementById('products').innerText = data.products;
            document.getElementById('orders').innerText = data.orders;
            document.getElementById('revenue').innerText = "$" + data.revenue;

        });
}

// load immediately
loadStats();

// refresh every 5 seconds (LIVE DASHBOARD)
setInterval(loadStats, 5000);
</script>

<?php include("../includes/footer.php"); ?>