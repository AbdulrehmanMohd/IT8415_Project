<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM dbproj_orders WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
?>

<h2>My Orders</h2>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>

    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <p>Order ID: <?php echo $row['order_id']; ?></p>
        <p>Total: $<?php echo $row['total']; ?></p>
        <p>Status: <?php echo $row['status']; ?></p>
        <p>Date: <?php echo $row['created_at']; ?></p>
    </div>

<?php } ?>

<a href="products.php">Back to Shopping</a>