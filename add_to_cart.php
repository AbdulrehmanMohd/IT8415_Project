<?php
session_start();
include("config/db.php");

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = (int)$_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM dbproj_products WHERE product_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$product || $product['stock'] < 1) {
    header("Location: products.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['qty']++;
} else {
    $_SESSION['cart'][$id] = [
        "name"  => $product['name'],
        "price" => $product['price'],
        "qty"   => 1
    ];
}

header("Location: cart.php");
exit();
?>