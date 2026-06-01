<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'seller') {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "SELECT p.*, c.category_name FROM dbproj_products p
    LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
    WHERE p.seller_id = ? ORDER BY p.created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $seller_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$products = [];
while ($r = mysqli_fetch_assoc($result)) $products[] = $r;
?>

<h4>Seller Dashboard — <?php echo htmlspecialchars($_SESSION['username']); ?></h4>
<hr>

<div class="mb-3">
    <a href="add_product.php" class="btn btn-primary">+ Add New Product</a>
</div>

<h6>My Products (<?php echo count($products); ?>)</h6>

<?php if (empty($products)) { ?>
    <div class="alert alert-info">You have no products yet. Click "Add New Product" to get started.</div>
<?php } ?>

<div class="row">
<?php foreach ($products as $row) { ?>
<div class="col-md-4 mb-4">
    <div class="card h-100">
        <img src="<?php echo htmlspecialchars($row['image'] ?? 'https://placehold.co/300x200?text=No+Image'); ?>"
             class="product-img" alt="">
        <div class="card-body">
            <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></span>
            <h6 class="mt-1"><?php echo htmlspecialchars($row['name']); ?></h6>
            <p class="text-muted small mb-1"><?php echo htmlspecialchars(substr($row['description'],0,60)); ?>...</p>
            <strong>$<?php echo number_format($row['price'], 2); ?></strong>
            <p class="text-muted small mt-1">Stock: <?php echo $row['stock']; ?> | Views: <?php echo $row['views']; ?></p>
            <div class="d-flex gap-2 mt-2">
                <a href="edit_product.php?id=<?php echo $row['product_id']; ?>"
                   class="btn btn-sm btn-outline-primary w-50">Edit</a>
                <a href="../products_details.php?id=<?php echo $row['product_id']; ?>"
                   class="btn btn-sm btn-outline-secondary w-50">View</a>
            </div>
        </div>
    </div>
</div>
<?php } ?>
</div>

<?php include("../includes/footer.php"); ?>