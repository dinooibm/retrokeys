<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['index'])) {
    $index = $_GET['index'];

    // Remove item from the cart session
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
    }

    // Redirect back to the cart page
    header("Location: cart.php");
    exit();
} else {
    // Invalid request
    http_response_code(400);
    echo "Invalid request";
}
?>
