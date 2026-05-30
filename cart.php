<?php
session_start();
include("config/db.php");

// Handle remove
if (isset($_GET['remove'])) {
    $rid = (int)$_GET['remove'];
    unset($_SESSION['cart'][$rid]);
    header("Location: cart.php");
    exit();
}
?>

<?php include("includes/header.php"); ?>

<h2 class="mb-4"><i class="bi bi-cart3"></i> Your Cart</h2>

<div class="card p-4">
<?php
$total = 0;

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $pid => $item) {
        $subtotal = $item['price'] * $item['qty'];
        $total += $subtotal;
?>
    <div class="d-flex justify-content-between align-items-center border-bottom py-3">
        <div>
            <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
            <small class="text-muted">Qty: <?php echo $item['qty']; ?> × $<?php echo number_format($item['price'], 2); ?></small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span>$<?php echo number_format($subtotal, 2); ?></span>
            <a href="cart.php?remove=<?php echo $pid; ?>" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash"></i>
            </a>
        </div>
    </div>
<?php } ?>

    <div class="d-flex justify-content-between mt-3">
        <h4>Total:</h4>
        <h4 style="color:#e94560;">$<?php echo number_format($total, 2); ?></h4>
    </div>

    <?php if (isset($_SESSION['user_id'])) { ?>
        <a href="checkout.php" class="btn btn-success mt-3 w-100">Proceed to Checkout</a>
    <?php } else { ?>
        <a href="login.php" class="btn btn-primary mt-3 w-100">Login to Checkout</a>
    <?php } ?>

<?php } else { ?>
    <p class="text-center text-muted py-4">Your cart is empty. <a href="products.php">Shop now</a></p>
<?php } ?>
</div>

<?php include("includes/footer.php"); ?>