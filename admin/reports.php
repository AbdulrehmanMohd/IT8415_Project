<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Date filter
$date_from = $_POST['date_from'] ?? date('Y-01-01');
$date_to   = $_POST['date_to']   ?? date('Y-m-d');
$filter_user = trim($_POST['filter_user'] ?? '');

// REPORT 1: Top products via stored procedure
$stmt = mysqli_prepare($conn, "CALL GetTopProducts(?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$topProducts = mysqli_stmt_get_result($stmt);

// REPORT 2: Products by user
$userProducts = null;
if (!empty($filter_user)) {
    $stmt2 = mysqli_prepare($conn, "SELECT p.*, u.username FROM dbproj_products p
        JOIN dbproj_users u ON p.seller_id = u.user_id
        WHERE u.username LIKE ?");
    $like = '%' . $filter_user . '%';
    mysqli_stmt_bind_param($stmt2, "s", $like);
    mysqli_stmt_execute($stmt2);
    $userProducts = mysqli_stmt_get_result($stmt2);
}

// Summary
$totalOrders  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbproj_orders"))['total'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS revenue FROM dbproj_orders"))['revenue'] ?? 0;
?>

<h2 class="mb-4"><i class="bi bi-graph-up"></i> Admin Reports</h2>
<a href="dashboard.php" class="btn btn-outline-light btn-sm mb-3">← Back</a>

<!-- SUMMARY -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <h5>Total Orders</h5>
            <h2 style="color:#e94560;"><?php echo $totalOrders; ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <h5>Total Revenue</h5>
            <h2 style="color:#e94560;">$<?php echo number_format($totalRevenue, 2); ?></h2>
        </div>
    </div>
</div>

<!-- REPORT 1: TOP PRODUCTS (Stored Procedure) -->
<div class="card p-4 mb-4">
    <h4 class="mb-3">🏆 Report 1: Top Selling Products</h4>
    <form method="POST" class="row g-2 mb-3">
        <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" name="date_from" class="form-control bg-dark text-white border-secondary"
                value="<?php echo $date_from; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" name="date_to" class="form-control bg-dark text-white border-secondary"
                value="<?php echo $date_to; ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Generate</button>
        </div>
    </form>

    <table class="table table-dark table-striped">
        <thead><tr><th>Product</th><th>Total Sold</th><th>Revenue</th></tr></thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($topProducts)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo $row['total_sold']; ?></td>
                <td>$<?php echo number_format($row['revenue'], 2); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <small class="text-muted">Uses stored procedure <code>GetTopProducts</code></small>
</div>

<!-- REPORT 2: BY SELLER/CREATOR -->
<div class="card p-4">
    <h4 class="mb-3">👤 Report 2: Products by Seller</h4>
    <form method="POST" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="filter_user" class="form-control bg-dark text-white border-secondary"
                placeholder="Seller username..." value="<?php echo htmlspecialchars($filter_user); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <?php if ($userProducts !== null) { ?>
    <table class="table table-dark table-striped">
        <thead><tr><th>Product</th><th>Price</th><th>Stock</th><th>Seller</th></tr></thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($userProducts)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>$<?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo $row['stock']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</div>

<?php include("../includes/footer.php"); ?>