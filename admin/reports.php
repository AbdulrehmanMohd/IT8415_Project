<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// ======================
// REPORT DATA
// ======================
$topProducts = mysqli_query($conn, "
    SELECT p.name, SUM(oi.quantity) AS total_sold
    FROM dbproj_order_items oi
    JOIN dbproj_products p ON oi.product_id = p.product_id
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");

$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM dbproj_orders
"));

$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(total) AS revenue FROM dbproj_orders
"));
?>

<!-- PAGE TITLE -->
<h2 class="mb-4">📊 Admin Reports</h2>

<!-- SUMMARY CARDS -->
<div class="row mb-4">

    <div class="col-md-4">
        <div class="card p-3 text-center">
            <h5>Total Orders</h5>
            <h2><?php echo $totalOrders['total']; ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 text-center">
            <h5>Total Revenue</h5>
            <h2>$<?php echo $totalRevenue['revenue'] ?? 0; ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 text-center">
            <h5>Top Products</h5>
            <h2>5</h2>
        </div>
    </div>

</div>

<!-- TOP PRODUCTS TABLE -->
<div class="card p-4">

    <h4 class="mb-3">🏆 Top Selling Products</h4>

    <table class="table table-dark table-striped table-hover">

        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Sold</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($row = mysqli_fetch_assoc($topProducts)) { ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['total_sold']; ?></td>
            </tr>
        <?php } ?>
        </tbody>

    </table>

</div>

<!-- BACK BUTTON -->
<div class="mt-3">
    <a href="dashboard.php" class="btn btn-outline-light">
        ← Back to Dashboard
    </a>
</div>

<?php include("../includes/footer.php"); ?>