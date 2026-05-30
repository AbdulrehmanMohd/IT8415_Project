<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM dbproj_orders WHERE user_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

include("includes/header.php");
?>

<h2 class="mb-4"><i class="bi bi-box-seam"></i> My Orders</h2>

<?php if (mysqli_num_rows($result) == 0) { ?>
    <div class="alert alert-info">You have no orders yet. <a href="products.php">Start shopping!</a></div>
<?php } ?>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<div class="card p-4 mb-3">
    <div class="row">
        <div class="col-md-3">
            <strong>Order #<?php echo $row['order_id']; ?></strong>
        </div>
        <div class="col-md-3">
            Total: <strong style="color:#e94560;">$<?php echo number_format($row['total'], 2); ?></strong>
        </div>
        <div class="col-md-3">
            Status: <span class="badge bg-<?php echo $row['status']=='pending' ? 'warning' : 'success'; ?>">
                <?php echo htmlspecialchars($row['status']); ?>
            </span>
        </div>
        <div class="col-md-3 text-muted small">
            <?php echo $row['created_at']; ?>
        </div>
    </div>
</div>
<?php } ?>

<a href="products.php" class="btn btn-outline-light mt-2">← Back to Shopping</a>

<?php include("includes/footer.php"); ?>