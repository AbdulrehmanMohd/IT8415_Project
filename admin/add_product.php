<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// GET CATEGORIES
$categories = mysqli_query($conn, "SELECT * FROM dbproj_categories");

if (isset($_POST['add'])) {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    $sql = "INSERT INTO dbproj_products (name, price, stock, category_id)
            VALUES ('$name', '$price', '$stock', '$category')";

    mysqli_query($conn, $sql);

    echo "Product added successfully!";
}
?>

<h2>Add Product</h2>

<form method="POST">

    <input type="text" name="name" placeholder="Product Name" required><br><br>

    <input type="number" name="price" placeholder="Price" required><br><br>

    <input type="number" name="stock" placeholder="Stock" required><br><br>

    <!-- CATEGORY DROPDOWN -->
    <select name="category" required>
        <option value="">Select Category</option>

        <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
            <option value="<?php echo $row['category_id']; ?>">
                <?php echo $row['category_name']; ?>
            </option>
        <?php } ?>

    </select>

    <br><br>

    <button type="submit" name="add">Add Product</button>

</form>