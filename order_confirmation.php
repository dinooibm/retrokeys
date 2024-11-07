<?php
session_start();
require 'Customer_conx.php';

// Check if the user is logged in
if (!isset($_SESSION['cID'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the logged-in user's ID from the session
$userId = $_SESSION['cID'];

// Fetch user information from the database
$sql = "SELECT cName, cEmail, cAdd FROM users WHERE cID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Redirect to login page if user is not found
    header("Location: login.php");
    exit();
}

// Check if the cart is empty
if (empty($_SESSION['cart'])) {
    // Redirect back to the cart page if the cart is empty
    header("Location: cart.php");
    exit();
}

// Retrieve cart data from session
$cart = $_SESSION['cart'];
$totalAmount = 0;

// Calculate total amount
foreach ($cart as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Retrieve delivery/pickup and payment method selections from the session
$deliveryOrPickup = isset($_SESSION['deliveryOrPickup']) ? $_SESSION['deliveryOrPickup'] : '';
$paymentMethod = isset($_SESSION['paymentMethod']) ? $_SESSION['paymentMethod'] : '';

// Handle form submission for order processing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Process the order based on the user's selections
    // For example, store the order details in the database
    
    // Insert transaction data into the transactions database
    foreach ($cart as $item) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];
        $unitPrice = $item['price'];
        $totalPrice = $unitPrice * $quantity;
        $date = date('Y-m-d H:i:s'); // Current timestamp

        // Insert into transactions table
        $sql = "INSERT INTO transactions (product_id, quantity, unit_price, total_price, transaction_date, user_id, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiidsi", $productId, $quantity, $unitPrice, $totalPrice, $date, $userId);
        $stmt->execute();


        // Update product stock
        $sql = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $productId);
        $stmt->execute();

        // Record income
        $income = $quantity * $unitPrice;
        $sql = "INSERT INTO income (product_id, quantity, total_income, sale_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iids", $productId, $quantity, $income, $date);
        $stmt->execute();
    }

    // Insert order data into the orders database
    $sql = "INSERT INTO orders (customer_id, total_amount, status, order_date) VALUES (?, ?, 'pending', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $userId, $totalAmount, $date);
    $stmt->execute();

    // Clear the cart and session data after processing the order
    $_SESSION['cart'] = [];
    unset($_SESSION['deliveryOrPickup']);
    unset($_SESSION['paymentMethod']);

    // After processing the order, you may redirect the user to a thank you page or order summary page
    header("Location: thank_you.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #077a07;
            color: #fff;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .menu {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .menu li {
            margin-right: 20px;
        }
        .menu li:last-child {
            margin-right: 0;
        }
        .menu button {
            color: #fff;
            background-color: #077a07;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .menu button:hover {
            background-color: #529e52;
        }

        .cart-icon {
            margin-left: 1200px;
        }

        .container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .content {
            padding: 20px;
        }
        h1 {
            color: #077a07;
            margin-bottom: 20px;
        }
        p {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .button {
            background-color: #077a07;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #529e52;
        }
    </style>
</head>
<body>
    <div class="header">
        <ul class="menu">
            <li><button onclick="window.location.href='landing.php'">Home</button></li>
            <li><button onclick="window.location.href='products.php'">Products</button></li>
            <li><button onclick="window.location.href='profile.php'">Profile</button></li>
            <li><button onclick="window.location.href='logoutPage.php'">Logout</button></li>
            <li class="cart-icon"><button onclick="window.location.href='cart.php'"><i class="material-icons">shopping_cart</i></button></li>
        </ul>
    </div>
    <div class="container">
        <div class="content">
            <h1>Order Confirmation</h1>
            <h2>Summary</h2>
            <p><strong>Name:</strong> <?php echo $user['cName']; ?></p>
            <p><strong>Email:</strong> <?php echo $user['cEmail']; ?></p>
            <p><strong>Address:</strong> <?php echo $user['cAdd']; ?></p>
            <h3>Items in Cart</h3>
            <ul>
                <?php foreach ($cart as $item): ?>
                    <li>
                        <span><?php echo $item['name']; ?> - Quantity: <?php echo $item['quantity']; ?></span>
                        <span>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($totalAmount, 2); ?></p>
            <h2>Delivery or Pickup</h2>
            <p><?php echo $deliveryOrPickup == 'delivery' ? 'Delivery (Will use Address in profile)' : 'Pickup'; ?></p>
            <h2>Payment Method</h2>
                <p>Cash</p>
            <form method="post">
                <button type="submit" name="submit" class="button">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
