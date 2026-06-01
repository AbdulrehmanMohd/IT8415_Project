<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'seller') {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];
$product_id = (int)($_GET['id'] ?? 0);

// Fetch product — only if it belongs to this seller
$stmt = mysqli_prepare($conn, "SELECT * FROM dbproj_products WHERE product_id = ? AND seller_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $product_id, $seller_id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$product) {
    echo "<div class='alert alert-danger'>Product not found or you do not have permission to edit it.</div>";
    include("../includes/footer.php");
    exit();
}

$categories = mysqli_query($conn, "SELECT * FROM dbproj_categories ORDER BY category_name");
$success = "";
$error   = "";

if (isset($_POST['update'])) {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = (float)$_POST['price'];
    $stock       = (int)$_POST['stock'];
    $category    = (int)$_POST['category'];
    $image       = trim($_POST['image']);

    if (strlen($name) < 2) {
        $error = "Product name is too short.";
    } elseif ($price <= 0) {
        $error = "Price must be greater than 0.";
    } elseif ($stock < 0) {
        $error = "Stock cannot be negative.";
    } else {
        $stmt2 = mysqli_prepare($conn, "UPDATE dbproj_products
            SET name=?, description=?, price=?, stock=?, category_id=?, image=?
            WHERE product_id=? AND seller_id=?");
        mysqli_stmt_bind_param($stmt2, "ssdisiii", $name, $description, $price, $stock, $category, $image, $product_id, $seller_id);
        mysqli_stmt_execute($stmt2);
        $success = "Product updated successfully!";

        // Refresh product data
        $stmt3 = mysqli_prepare($conn, "SELECT * FROM dbproj_products WHERE product_id = ?");
        mysqli_stmt_bind_param($stmt3, "i", $product_id);
        mysqli_stmt_execute($stmt3);
        $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt3));
    }
}
?>

<h4>Edit Product</h4>
<hr>
<a href="dashboard.php" class="btn btn-outline-secondary btn-sm mb-3">← Back to Dashboard</a>

<?php if ($error)   echo "<div class='alert alert-danger'>$error</div>"; ?>
<?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

<div class="card p-4" style="max-width:600px;">
    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" id="name" name="name" class="form-control"
                value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price ($)</label>
            <input type="number" id="price" name="price" step="0.01" class="form-control"
                value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" id="stock" name="stock" class="form-control"
                value="<?php echo $product['stock']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                <option value="">Select Category</option>
                <?php
                mysqli_data_seek($categories, 0);
                while ($c = mysqli_fetch_assoc($categories)) {
                    $sel = $c['category_id'] == $product['category_id'] ? 'selected' : '';
                    echo "<option value='{$c['category_id']}' $sel>" . htmlspecialchars($c['category_name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <input type="text" name="image" class="form-control"
                value="<?php echo htmlspecialchars($product['image'] ?? ''); ?>"
                placeholder="https://...">
            <?php if (!empty($product['image'])) { ?>
                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                     class="mt-2 img-thumbnail" style="max-height:120px;">
            <?php } ?>
        </div>

        <button type="submit" name="update" class="btn btn-primary w-100">Save Changes</button>
    </form>
</div>

<script src="../assets/js/validate.js"></script>
<?php include("../includes/footer.php"); ?>