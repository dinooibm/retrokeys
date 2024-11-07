<?php
session_start(); // Start the session
require 'admin_conx.php'; // Include the database connection file

// Check if product ID is provided in the URL
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product details from the database
    $sqlFetchProduct = "SELECT * FROM products WHERE product_id = '$product_id'";
    $resultProduct = mysqli_query($conn, $sqlFetchProduct);
    if (mysqli_num_rows($resultProduct) > 0) {
        $rowProduct = mysqli_fetch_assoc($resultProduct);
        $product_name = $rowProduct['product_name'];
        $price = $rowProduct['price'];
        $stock = $rowProduct['stock'];
    } else {
        echo "Product not found.";
        exit; // Stop further execution
    }
} else {
    echo "Product ID not provided.";
    exit; // Stop further execution
}

// Function to handle updating a product
if (isset($_POST['updateProduct'])) {
  $productName = $_POST['product_name'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];

  // Check if a new image file is uploaded
  if ($_FILES['image']['name']) {
      $image = $_FILES['image']['name'];
      $temp_name = $_FILES['image']['tmp_name'];
      $upload_dir = "pics/"; // Specify the directory where you want to store the uploaded images
      move_uploaded_file($temp_name, $upload_dir . $image); // Move the uploaded image to the specified directory
      $image_path = $upload_dir . $image; // Set the image path
  } else {
      // If no new image is uploaded, keep the existing image path
      $image_path = $rowProduct['image_path'];
  }

  // Update product in the database
  $sqlUpdateProduct = "UPDATE products SET product_name = '$productName', price = '$price', stock = '$stock', image_path = '$image_path' WHERE product_id = '$product_id'";
  if (mysqli_query($conn, $sqlUpdateProduct)) {
      echo "<script>alert('Product updated successfully');</script>";
      echo "<script>window.location.href='productTable.php';</script>"; // Redirect to productTable.php
      exit; // Ensure no further execution after redirect
  } else {
      echo "Error: " . mysqli_error($conn);
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
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
  .menu a {
    color: #fff;
    text-decoration: none;
    padding: 10px;
  }
  .container {
    max-width: 800px;
    margin: 100px auto;
    text-align: center;
  }
  .content {
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    margin-top: 50px;
  }
  h1 {
    color: #077a07;
  }
  p {
    color: #333;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
  }
  th {
    background-color: #077a07;
    color: #fff;
  }
  button {
    background-color: #077a07;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
  }
  button:hover {
    background-color: #529e52;
  }
</style>
</head>
<body>

<div class="header">
  <ul class="menu">
    <li><button onclick="window.location.href='dashboard.php'">Dashboard</button></li>
    <li><button onclick="window.location.href='adminPage.php'">User Logs</button></li>
    <li><button onclick="window.location.href='Transactions.php'">Transactions</button></li>
    <li><button onclick="window.location.href='productTable.php'">Products</button></li>
    <li><button onclick="window.location.href='income.php'">Income</button></li>
    <li><button onclick="window.location.href='logoutAdmin.php'">Logout</button></li>
  </ul>
</div>

<div class="container">
  <div class="content">
    <h2>Edit Product</h2>
      <form action="" method="post" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?php echo $product_name; ?>" required><br><br>
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo $price; ?>" required><br><br>
        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" value="<?php echo $stock; ?>" required><br><br>
        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image"><br><br> <!-- Input field for uploading a new image -->
        <button type="submit" name="updateProduct">Update Product</button>
      </form>

  </div>
</div>

</body>
</html>
