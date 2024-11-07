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

// Assuming you have the logged-in user's ID stored in a session variable
$user_id = $_SESSION['cID'];

// Fetch orders for the logged-in user with JOIN to get product details
$sql = "SELECT t.transaction_id, t.transaction_date, t.status, t.total_price, p.product_name 
        FROM transactions t 
        JOIN products p ON t.product_id = p.product_id 
        WHERE t.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
  }
  .cart-icon {
    margin-left: 1200px;
  }
  .material-icons-outlined {
    font-family: 'Material Symbols Outlined';
    font-weight: 700;
    font-size: 24px;
  }
  .content {
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  .cart-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .cart-item .name {
    flex: 1;
    text-align: left;
  }
  .cart-item .price {
    flex: 1;
    text-align: right;
  }
  .order-history {
    margin-top: 20px;
    padding: 20px;
  }
  .status-pending {
    color: orange;
  }
  .status-shipped {
    color: blue;
  }
  .status-delivered {
    color: green;
  }
  .bg-darkblue {
      background-color: #00264d !important; /* Dark blue background */
  }
  .navbar-brand {
      font-weight: bold;
      color: #f0f0f0;
  }
  .nav-link {
      color: #f0f0f0 !important;
  }
  .badge {
      font-size: 0.75rem;
  }
</style>
</head>
<body>
  
<div class="header bg-dark text-white fixed-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-darkblue">
        <div class="container">
            <a class="navbar-brand" href="landing.php">Retro Keys</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="landing.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logoutPage.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php">
                            <i class="material-icons">shopping_cart</i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                3 <!-- Replace with dynamic cart count -->
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<div class="container mt-5 pt-5">
    <div class="content bg-light border rounded p-4 shadow">
        <h1 class="text-primary">Your Cart</h1>
        <?php if (count($cart) > 0): ?>
            <?php foreach ($cart as $key => $item): ?>
                <div class="cart-item">
                    <div class="name"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="quantity">
                        <input type="number" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" step="1" onchange="updateQuantity(<?php echo $key; ?>, this.value)" class="form-control form-control-sm" style="width: 70px;" onkeydown="return false">
                    </div>
                    <div class="price"><?php echo '₱' . number_format($item['price'] * $item['quantity'], 2); ?></div>
                    <div class="action">
                        <button onclick="removeFromCart(<?php echo $key; ?>)" class="btn btn-danger btn-sm">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <p>Total Amount: <?php echo '₱' . number_format($totalAmount, 2); ?></p>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
		<form method="post" action="checkout.php">
    <h3>Select Payment Method</h3>
      <label>
          <input type="radio" name="paymentMethod" value="ewallet" required> GCash
      </label>
          <button type="submit" name="checkout" class="btn btn-primary w-100 mt-3">Checkout</button>
        </form>
    </div>

    <div class="order-history bg-light border rounded p-4 shadow mt-4">
        <h1 class="text-primary">Order History</h1>
        <?php if (count($orders) > 0): ?>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Product Name</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['transaction_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['transaction_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td>
                                <?php
                                    $status = htmlspecialchars($order['status']);
                                    echo "<span class='status-$status'>$status</span>";
                                ?>
                            </td>
                            <td><?php echo '₱' . number_format($order['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
  function removeFromCart(index) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
      window.location.href = 'remove_from_cart.php?index=' + index;
    }
  }

  function updateQuantity(index, quantity) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", 'update_quantity.php?index=' + index + '&quantity=' + quantity, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        var totalAmount = xhr.responseText;
        document.querySelector('.total-amount').innerText = '₱' + totalAmount;
      }
    };
    xhr.send();
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
