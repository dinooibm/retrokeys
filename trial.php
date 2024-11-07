<?php
session_start();

// Check if the "Checkout" button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    // Redirect to checkout.php
    header("Location: checkout.php");
    exit();
}

// Sample cart data (replace this with your actual cart data retrieval)
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalAmount = 0;

// Calculate total amount
foreach ($cart as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Connect to the database 
$servername = 'localhost';
$username = 'GA_Customer';
$password = '456';
$dbname = 'greenantz_db';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the logged-in user ID is stored in the session
$userId = $_SESSION['user_id']; // Adjust this based on your actual session variable

// Fetch orders for the logged-in user from the transactions table
$sql = "SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

if ($result->num_rows > 0) {
    // Loop through each row of the result set
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Calculate total pending amount
$totalPendingAmount = 0;
foreach ($orders as $order) {
    if ($order['status'] === 'pending') {
        $totalPendingAmount += $order['total_price'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .status-pending {
            color: orange;
        }

        .status-shipped {
            color: blue;
        }

        .status-delivered {
            color: green;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <header>
        <h1>Your Orders</h1>
        <a href="logout.php">Logout</a>
    </header>
    <main>
        <section>
            <h2>Current Cart</h2>
            <?php if (count($cart) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['price']); ?></td>
                                <td><?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total</th>
                            <th><?php echo $totalAmount; ?></th>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </section>
        <section>
            <h2>Order History</h2>
            <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['transaction_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['transaction_date']); ?></td>
                                <td>
                                    <?php
                                        // Display order status, e.g., Pending, Shipped, Delivered
                                        $status = htmlspecialchars($order['status']);
                                        echo "<span class='status-$status'>$status</span>";
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <form method="post" action="">
            <input type="submit" name="checkout" value="Checkout">
        </form>
    </footer>
</body>
</html>
