<?php
include("../includes/header.php");
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'seller') {
    header("Location: ../login.php");
    exit();
}

$categories = mysqli_query($conn, "SELECT * FROM dbproj_categories");
$success = "";
$error   = "";

if (isset($_POST['add'])) {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = (float)$_POST['price'];
    $stock       = (int)$_POST['stock'];
    $category    = (int)$_POST['category'];
    $image       = trim($_POST['image']);
    $seller_id   = $_SESSION['user_id'];

    if (strlen($name) < 2) {
        $error = "Product name is too short.";
    } elseif ($price <= 0) {
        $error = "Price must be greater than 0.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO dbproj_products
            (name, description, price, stock, category_id, image, seller_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssdissi", $name, $description, $price, $stock, $category, $image, $seller_id);
        mysqli_stmt_execute($stmt);
        $success = "Product published successfully!";
    }
}
?>

<h2 class="mb-4"><i class="bi bi-plus-circle"></i> Add New Product</h2>
<a href="dashboard.php" class="btn btn-outline-light btn-sm mb-3">← Back to Dashboard</a>

<div class="card p-4" style="max-width:600px;">

    <div id="js-error">
        <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    </div>

    <form method="POST" onsubmit="return validateProduct()">

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" id="name" name="name" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price ($)</label>
            <input type="number" id="price" name="price" step="0.01" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" id="stock" name="stock" class="form-control bg-dark text-white border-secondary" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select bg-dark text-white border-secondary" required>
                <option value="">Select Category</option>
                <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <input type="text" name="image" class="form-control bg-dark text-white border-secondary" placeholder="https://...">
        </div>

        <button type="submit" name="add" class="btn btn-primary w-100">Publish Product</button>
    </form>
</div>

<script src="../assets/js/validate.js"></script>
<?php include("../includes/footer.php"); ?>