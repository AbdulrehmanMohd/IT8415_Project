<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Cart is empty";
    exit();
}

$user_id = 1; // temporary (we will link real user later)

$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}

// 1. Insert order
mysqli_query($conn, "INSERT INTO dbproj_orders (user_id, total) VALUES ($user_id, $total)");

$order_id = mysqli_insert_id($conn);

// 2. Insert order items
foreach ($_SESSION['cart'] as $id => $item) {

    $name = $item['name'];
    $price = $item['price'];
    $qty = $item['qty'];

    mysqli_query($conn, "
        INSERT INTO dbproj_order_items (order_id, product_id, quantity, price)
        VALUES ($order_id, $id, $qty, $price)
    ");
}

// 3. Clear cart
unset($_SESSION['cart']);

echo "Order placed successfully!";
echo "<br><a href='products.php'>Continue Shopping</a>";
?>