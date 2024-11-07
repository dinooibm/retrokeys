<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for the e-wallet payment method selection
    if (isset($_POST['paymentMethod']) && $_POST['paymentMethod'] === 'ewallet') {
        $secretKey = 'sk_test_h2UodtjsDxPfPviA3A1bZAkL';

        // Retrieve cart items and total amount from session or database
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $totalAmount = 0;
        
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // Convert total amount to centavos (PHP uses smallest currency unit in API)
        $amountInCentavos = intval($totalAmount * 100);

        // Prepare PayMongo request payload
        $checkoutData = [
            'data' => [
                'attributes' => [
                    'line_items' => [
                        [
                            'name' => 'Total Order',
                            'amount' => $amountInCentavos,
                            'currency' => 'PHP',
                            'quantity' => 1
                        ]
                    ],  
                    'payment_method_types' => ['gcash'],
                    'cancel_url' => 'http://localhost/ESPINO/cart.php?payment=cancel',
                    'success_url' => 'http://localhost/ESPINO/thank_you.php',
                ]
            ]
        ];

        // Initialize cURL request
        $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($secretKey . ':')
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($checkoutData));

        // Execute the request and handle response
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $responseArray = json_decode($response, true);
            if (isset($responseArray['data']['attributes']['checkout_url'])) {
                $checkoutUrl = $responseArray['data']['attributes']['checkout_url'];
                header("Location: $checkoutUrl");
                exit();
            } else {
                echo "Error: " . htmlspecialchars($responseArray['errors'][0]['detail'] ?? 'Unknown error occurred');
                exit();
            }
        } else {
            echo "Failed to initialize payment. Please try again.";
            exit();
        }
    } else {
        echo "Invalid payment method selected.";
    }
} else {
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
  }
  .header {
    background-color: #0275d8;
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
  .content {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 5px;
  }
  .cart-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .btn-primary {
    background-color: #0275d8;
    border: none;
  }
  .btn-primary:hover {
    background-color: #025aa5;
  }
</style>
</head>
<body>
<div class="header">
  <ul class="menu list-unstyled d-flex mb-0">
    <li><button onclick="window.location.href='landing.php'" class="btn btn-primary">Home</button></li>
    <li><button onclick="window.location.href='products.php'" class="btn btn-primary">Products</button></li>
    <li><button onclick="window.location.href='profile.php'" class="btn btn-primary">Profile</button></li>
    <li><button onclick="window.location.href='logoutPage.php'" class="btn btn-primary">Logout</button></li>
    <li class="cart-icon"><button onclick="window.location.href='cart.php'" class="btn btn-primary"><i class="material-icons">shopping_cart</i></button></li>
  </ul>
</div>

<div class="container mt-5 pt-5">
  <div class="content bg-light border rounded p-4 shadow">
    <h1 class="text-primary">Checkout</h1>
    <?php if (count($cart) > 0): ?>
      <?php foreach ($cart as $item): ?>
        <div class="cart-item">
          <div class="name"><?php echo $item['name']; ?></div>
          <div class="quantity"><?php echo $item['quantity']; ?></div>
          <div class="price"><?php echo '₱' . number_format($item['price'] * $item['quantity'], 2); ?></div>
        </div>
      <?php endforeach; ?>
      <p>Total Amount: <?php echo '₱' . number_format($totalAmount, 2); ?></p>
      <form method="post">  
        <h2>Delivery or Pickup</h2>
        <input type="radio" name="deliveryOrPickup" id="delivery" value="delivery" required>
        <label for="delivery">Delivery (door to door delivery!)</label><br>
        <input type="radio" name="deliveryOrPickup" id="pickup" value="pickup" required>
        <label for="pickup">Physical Store</label>
        <br><br>
        <h2>Payment Method</h2>
        <input type="radio" name="paymentMethod" id="cash" value="cash" required>
        <label for="cash">Cash</label><br>
        <input type="radio" name="paymentMethod" id="gcash" value="gcash" required>
        <label for="gcash">GCash</label><br>
        <input type="radio" name="paymentMethod" id="paypal" value="paypal" required>
        <label for="paypal">Paypal</label>
        <br><br>
        <button type="submit" name="confirmOrder" class="btn btn-primary w-100 mt-3">Confirm Order</button>
        <button type="submit" name="cancelOrder" class="btn btn-secondary w-100 mt-3">Cancel Order</button>
      </form>
    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Define toggleBankDetails function if needed
function toggleBankDetails() {
    // Show/hide additional fields based on payment method selection
}
</script>
</body>
</html>
