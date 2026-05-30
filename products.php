<?php include("includes/header.php"); ?>
<?php include("config/db.php"); ?>

<h2 class="mb-4">Products</h2>

<div class="row">

<?php
$result = mysqli_query($conn, "SELECT * FROM dbproj_products");

while ($row = mysqli_fetch_assoc($result)) {
?>

<div class="col-md-4 mb-3">
    <div class="card p-3">
        <h5><?php echo $row['name']; ?></h5>
        <p>Price: $<?php echo $row['price']; ?></p>
        <p>Stock: <?php echo $row['stock']; ?></p>

        <a class="btn btn-primary"
           href="add_to_cart.php?id=<?php echo $row['product_id']; ?>">
           Add to Cart
        </a>
    </div>
</div>

<?php } ?>

</div>

<?php include("includes/footer.php"); ?>