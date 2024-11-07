<?php
session_start(); // Start the session
require 'admin_conx.php'; // Include the database connection file

// Function to handle adding a new product
if(isset($_POST['addProduct'])) {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // File upload
    $targetDir = "uploads/"; // Directory where the files will be uploaded
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowedTypes = array('jpg','png','jpeg','gif');
    if(in_array($fileType, $allowedTypes)){
        // Upload file to the server
        if(move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)){
            // Insert product into database with image path
            $sqlInsertProduct = "INSERT INTO products (product_name, price, stock, image_path) VALUES ('$productName', '$price', '$stock', '$targetFilePath')";
            if(mysqli_query($conn, $sqlInsertProduct)) {
                echo "<script>alert('Product added successfully');</script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }else{
            echo "Error uploading file.";
        }
    }else{
        echo "File format not supported.";
    }
}

// Function to handle deleting a product
if(isset($_POST['deleteProduct'])) {
    $product_id = $_POST['product_id'];

    // Delete product from database
    $sqlDeleteProduct = "DELETE FROM products WHERE product_id = '$product_id'";
    if(mysqli_query($conn, $sqlDeleteProduct)) {
        echo "<script>alert('Product deleted successfully');</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Function to handle updating a product
if(isset($_POST['updateProduct'])) {
    $product_id = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Update product in the database
    $sqlUpdateProduct = "UPDATE products SET product_name = '$productName', price = '$price', stock = '$stock' WHERE product_id = '$product_id'";
    if(mysqli_query($conn, $sqlUpdateProduct)) {
        echo "<script>alert('Product updated successfully');</script>";
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
<title>Product Table</title>
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
  }

  .content {
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    margin-top: 20px;
  }

  h2 {
    color: #077a07;
  }

  form {
    margin-bottom: 20px;
  }

  label {
    display: block;
    margin-bottom: 5px;
  }

  input[type="text"],
  input[type="number"],
  input[type="file"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
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

  img {
    max-width: 100px;
    max-height: 100px;
    border-radius: 5px;
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
    <!-- Form to add a new product -->
    <h2>Add Product</h2>
    <form action="" method="post" enctype="multipart/form-data">
      <label for="product_name">Product Name:</label>
      <input type="text" id="product_name" name="product_name" required><br><br>
      <label for="price">Price:</label>
      <input type="number" id="price" name="price" step="0.01" required><br><br>
      <label for="stock">Stock:</label>
      <input type="number" id="stock" name="stock" required><br><br>
      <label for="image">Product Image:</label>
      <input type="file" id="image" name="image" accept="image/*" required><br><br>
      <button type="submit" name="addProduct">Add Product</button>
    </form>
    
    <!-- Products Table -->
    <h2>Products</h2>
    <table>
      <thead>
        <tr>
          <th>Product ID</th>
          <th>Product Name</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Image</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Fetch data from products table
        $sqlFetchProducts = "SELECT product_id, product_name, price, stock, image_path FROM products";
        $resultProducts = mysqli_query($conn, $sqlFetchProducts);
        if (mysqli_num_rows($resultProducts) > 0) {
          while($rowProduct = mysqli_fetch_assoc($resultProducts)) {
            echo "<tr>";
            echo "<td>" . $rowProduct['product_id'] . "</td>";
            echo "<td>" . $rowProduct['product_name'] . "</td>";
            echo "<td>" . $rowProduct['price'] . "</td>";
            echo "<td>" . $rowProduct['stock'] . "</td>";
            echo "<td><img src='" . $rowProduct['image_path'] . "' width='100' height='100'></td>";
            echo "<td>";
            // Delete Button
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='product_id' value='" . $rowProduct['product_id'] . "'>";
            echo "<button type='submit' name='deleteProduct'>Delete</button>";
            echo "</form>";
            // Edit Button with redirect
            echo "<button onclick=\"window.location.href='productEdit.php?product_id=" . $rowProduct['product_id'] . "'\">Edit</button>";
            echo "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='6'>No products found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
