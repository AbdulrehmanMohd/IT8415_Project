<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$date_from   = $_POST['date_from']   ?? date('Y-01-01');
$date_to     = $_POST['date_to']     ?? date('Y-m-d');
$filter_user = trim($_POST['filter_user'] ?? '');

$stmt = mysqli_prepare($conn, "CALL GetTopProducts(?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$topProducts = mysqli_stmt_get_result($stmt);

$topProductsData = [];
while ($r = mysqli_fetch_assoc($topProducts)) {
    $topProductsData[] = $r;
}

mysqli_stmt_free_result($stmt);
while (mysqli_more_results($conn)) {
    mysqli_next_result($conn);
}

$totalOrders  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbproj_orders"))['total'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS revenue FROM dbproj_orders"))['revenue'] ?? 0;

$userProductsData = [];
if (!empty($filter_user)) {
    $stmt2 = mysqli_prepare($conn, "SELECT p.name, p.price, p.stock, u.username
        FROM dbproj_products p
        JOIN dbproj_users u ON p.seller_id = u.user_id
        WHERE u.username LIKE ?");
    $like = '%' . $filter_user . '%';
    mysqli_stmt_bind_param($stmt2, "s", $like);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    while ($r = mysqli_fetch_assoc($res2)) {
        $userProductsData[] = $r;
    }
}
?>

<h4>Admin Reports</h4>
<hr>
<a href="dashboard.php" class="btn btn-outline-secondary btn-sm mb-3">← Back to Dashboard</a>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <h6>Total Orders</h6>
            <h3><?php echo $totalOrders; ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <h6>Total Revenue</h6>
            <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
        </div>
    </div>
</div>

<div class="card p-4 mb-4">
    <h5>Report 1: Top Selling Products (by date range)</h5>
    <p class="text-muted small">Uses stored procedure <code>GetTopProducts</code></p>

    <form method="POST" class="row g-2 mb-3">
        <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" name="date_from" class="form-control"
                value="<?php echo htmlspecialchars($date_from); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" name="date_to" class="form-control"
                value="<?php echo htmlspecialchars($date_to); ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Generate</button>
        </div>
    </form>

    <?php if (!empty($topProductsData)) { ?>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr><th>Product</th><th>Total Sold</th><th>Revenue</th></tr>
        </thead>
        <tbody>
        <?php foreach ($topProductsData as $row) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo $row['total_sold']; ?></td>
                <td>$<?php echo number_format($row['revenue'], 2); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
        <p class="text-muted">No sales data found for this date range.</p>
    <?php } ?>
</div>

<div class="card p-4">
    <h5>Report 2: Products by Seller</h5>

    <form method="POST" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="filter_user" class="form-control"
                placeholder="Enter seller username..."
                value="<?php echo htmlspecialchars($filter_user); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <?php if (!empty($filter_user)) { ?>
        <?php if (!empty($userProductsData)) { ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr><th>Product</th><th>Price</th><th>Stock</th><th>Seller</th></tr>
            </thead>
            <tbody>
            <?php foreach ($userProductsData as $row) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <p class="text-muted">No products found for seller "<?php echo htmlspecialchars($filter_user); ?>".</p>
        <?php } ?>
    <?php } ?>
</div>

<?php include("../includes/footer.php"); ?>