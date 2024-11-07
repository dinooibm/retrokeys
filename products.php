<?php
session_start(); // Start the session
require 'admin_conx.php'; // Include the database connection file

// Function to add an item to the cart with a default quantity of 50
function addToCart($productId, $productName, $price, $quantity = 50) {
  // Initialize the cart session if it doesn't exist
  if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = [];
  }

  // Check if the product already exists in the cart
  foreach ($_SESSION['cart'] as &$item) {
      if ($item['product_id'] === $productId) {
          // If the product exists, increment the quantity
          $item['quantity'] += $quantity;
          return;
      }
  }

  // If the product doesn't exist, add it to the cart with the specified quantity
  $_SESSION['cart'][] = [
      'product_id' => $productId,
      'name' => $productName,
      'price' => $price,
      'quantity' => $quantity
  ];
}

// Check if the "Add to Cart" button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addToCart'])) {
  // Retrieve the product details from the form
  $productId = $_POST['productId'];
  $productName = $_POST['productName'];
  $price = $_POST['price'];

  $quantity = 1;

  // Add the product to the cart with a quantity of 50
  addToCart($productId, $productName, $price, $quantity);

  // Redirect back to the products page
  header("Location: products.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RETRO PRODUCTS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
</head>
<body class="bg-light text-dark">

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="landing.php"><img src="logoo.png" width="100px" height="100px"></a>
	
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
          <a class="nav-link" href="cart.php">
            <i class="material-icons">shopping_cart</i>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- Main Content -->
<div class="container mt-5 pt-5">

<br><br>
<br><br>
  <div class="row">
  
    <?php
    // Fetch data from products table
    $sqlFetchProducts = "SELECT * FROM products";
    $resultProducts = mysqli_query($conn, $sqlFetchProducts);
    if (mysqli_num_rows($resultProducts) > 0) {
      while($rowProduct = mysqli_fetch_assoc($resultProducts)) {
        echo "<div class='col-md-6 col-lg-4 mb-4'>";
        echo "<div class='card h-100 shadow-sm'>";
        echo "<img src='" . $rowProduct['image_path'] . "' class='card-img-top' alt='" . $rowProduct['product_name'] . "'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>" . htmlspecialchars($rowProduct['product_name']) . "</h5>";
        echo "<p class='card-text text-muted'>₱" . htmlspecialchars($rowProduct['price']) . " per piece</p>";
        echo "<p class='card-text'><small>Limited Stocks!</small></p>";
        echo "</div>";
        echo "<div class='card-footer bg-transparent border-top-0'>";
        echo "<form action='products.php' method='post'>";
        echo "<input type='hidden' name='productId' value='" . htmlspecialchars($rowProduct['product_id']) . "'>";
        echo "<input type='hidden' name='productName' value='" . htmlspecialchars($rowProduct['product_name']) . "'>";
        echo "<input type='hidden' name='price' value='" . htmlspecialchars($rowProduct['price']) . "'>";
        echo "<button type='submit' name='addToCart' class='btn btn-primary w-100'>Add to Cart</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
      }
    } else {
      echo "<p class='text-center'>No products found</p>";
    }
    ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
