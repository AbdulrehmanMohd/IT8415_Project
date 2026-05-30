<?php
include("../config/db.php");

header('Content-Type: application/json');

// Fetch stats
$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbproj_users"));
$products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbproj_products"));
$orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbproj_orders"));
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS total FROM dbproj_orders"));

// Return JSON response
echo json_encode([
    "users" => $users['total'],
    "products" => $products['total'],
    "orders" => $orders['total'],
    "revenue" => $revenue['total'] ?? 0
]);
?>