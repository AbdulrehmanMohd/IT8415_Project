<?php
session_start();
include("config/db.php");

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM dbproj_products WHERE product_id=$id");
$product = mysqli_fetch_assoc($result);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['qty']++;
} else {
    $_SESSION['cart'][$id] = [
        "name" => $product['name'],
        "price" => $product['price'],
        "qty" => 1
    ];
}

header("Location: cart.php");
exit();
?>