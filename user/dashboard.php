<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'seller') {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

$my_products = mysqli_prepare($conn, "SELECT p.*, c.category_name FROM dbproj_products p
    LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
    WHERE p.seller_id = ? ORDER BY p.created_at DESC");
mysqli_stmt_bind_param($my_products, "i", $seller_id);
mysqli_stmt_execute($my_products);
$result = mysqli_stmt_get_result($my_products);
?>

<h2 class="mb-4"><i class="bi bi-person-workspace"></i> Seller Dashboard</h2>

<div class="mb-3">
    <a href="add_product.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New Product</a>
</div>

<h5>My Products (<?php echo mysqli_num_rows($result); ?>)</h5>

<div class="row">
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<div class="col-md-4 mb-4">
    <div class="card h-100">
        <img src="<?php echo htmlspecialchars($row['image'] ?? 'https://placehold.co/300x200?text=No+Image'); ?>"
             class="product-img" alt="">
        <div class="p-3">
            <span class="badge bg-secondary"><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></span>
            <h6 class="mt-2"><?php echo htmlspecialchars($row['name']); ?></h6>
            <p style="color:#e94560;">$<?php echo number_format($row['price'], 2); ?></p>
            <p class="text-muted small">Stock: <?php echo $row['stock']; ?> | Views: <?php echo $row['views']; ?></p>
            <a href="../products_details.php?id=<?php echo $row['product_id']; ?>"
               class="btn btn-sm btn-outline-light w-100">View</a>
        </div>
    </div>
</div>
<?php } ?>
</div>

<?php include("../includes/footer.php"); ?>