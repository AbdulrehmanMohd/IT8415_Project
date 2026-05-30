<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM dbproj_products WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $pid);
    mysqli_stmt_execute($stmt);
    $delete_msg = "Product has been removed by administrator.";
    header("Location: manage_products.php?msg=deleted");
    exit();
}

$products = mysqli_query($conn, "SELECT p.*, c.category_name FROM dbproj_products p
    LEFT JOIN dbproj_categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC");
?>

<h2 class="mb-4"><i class="bi bi-box-seam"></i> Manage Products</h2>
<a href="dashboard.php" class="btn btn-outline-light btn-sm mb-3">← Back</a>
<a href="add_product.php" class="btn btn-primary btn-sm mb-3 ms-2">+ Add Product</a>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') { ?>
    <div class="alert alert-warning">Product has been removed by the administrator.</div>
<?php } ?>

<div class="card p-4">
<table class="table table-dark table-striped table-hover">
    <thead>
        <tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Views</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php while ($p = mysqli_fetch_assoc($products)) { ?>
        <tr>
            <td><?php echo $p['product_id']; ?></td>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></td>
            <td>$<?php echo number_format($p['price'], 2); ?></td>
            <td><?php echo $p['stock']; ?></td>
            <td><?php echo $p['views']; ?></td>
            <td>
                <a href="../products_details.php?id=<?php echo $p['product_id']; ?>"
                   class="btn btn-sm btn-outline-info">View</a>
                <a href="manage_products.php?delete=<?php echo $p['product_id']; ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Remove this product?')">
                   <i class="bi bi-trash"></i>
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</div>

<?php include("../includes/footer.php"); ?>