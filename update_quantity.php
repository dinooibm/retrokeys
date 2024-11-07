<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['index']) && isset($_GET['quantity'])) {
    $index = $_GET['index'];
    $quantity = $_GET['quantity'];

    // Update quantity in the cart session
    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;
    }

    // Recalculate total amount
    $totalAmount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

    // Return the updated total amount
    echo $totalAmount;
} else {
    // Invalid request
    http_response_code(400);
    echo "Invalid request";
}
?>
