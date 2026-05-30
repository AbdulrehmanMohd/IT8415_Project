<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id']; // FIXED: real user from session

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}

// Insert order (prepared statement)
$stmt = mysqli_prepare($conn, "INSERT INTO dbproj_orders (user_id, total) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "id", $user_id, $total);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($conn);

// Insert order items
foreach ($_SESSION['cart'] as $pid => $item) {
    $qty   = $item['qty'];
    $price = $item['price'];
    $stmt2 = mysqli_prepare($conn, "INSERT INTO dbproj_order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, "iiid", $order_id, $pid, $qty, $price);
    mysqli_stmt_execute($stmt2);
}

// Clear cart
unset($_SESSION['cart']);

include("includes/header.php");
?>

<div class="text-center py-5">
    <div class="card p-5 mx-auto" style="max-width:500px;">
        <i class="bi bi-check-circle text-success" style="font-size:4rem;"></i>
        <h2 class="mt-3">Order Placed!</h2>
        <p class="text-muted">Your order #<?php echo $order_id; ?> has been confirmed.</p>
        <p>Total paid: <strong style="color:#e94560;">$<?php echo number_format($total, 2); ?></strong></p>
        <a href="my_orders.php" class="btn btn-primary me-2">View My Orders</a>
        <a href="products.php" class="btn btn-outline-light">Continue Shopping</a>
    </div>
</div>

<?php include("includes/footer.php"); ?>