<?php
session_start();
include("config/db.php");
?>

<?php include("includes/header.php"); ?>

<h2>Your Cart</h2>

<div class="card p-4">

<?php
$total = 0;

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $item) {
        ?>

        <div class="d-flex justify-content-between border-bottom py-2">
            <div>
                <strong><?php echo $item['name']; ?></strong><br>
                <small>Qty: <?php echo $item['qty']; ?></small>
            </div>

            <div>
                $<?php echo $item['price'] * $item['qty']; ?>
            </div>
        </div>

        <?php
        $total += $item['price'] * $item['qty'];
    }

    ?>

    <hr>

    <h4>Total: $<?php echo $total; ?></h4>

    <a href="checkout.php" class="btn btn-success mt-3">
        Checkout
    </a>

<?php
} else {
    echo "<p>Your cart is empty</p>";
}
?>

</div>

<?php include("includes/footer.php"); ?>