<?php
session_start();

if (!isset($_SESSION['cID'])) {
    header("Location: login.php");
    exit();
}

require 'Customer_conx.php'; // Include your database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logout</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<style>
  /* Additional styles specific to the logout page */
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }
  .header {
    background-color: #0275d8; /* Blue color */
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
  .header .menu {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
  }
  .header .menu li {
    margin-right: 20px;
  }
  .header .menu li:last-child {
    margin-right: 0;
  }
  .header .menu a {
    color: #fff;
    text-decoration: none;
    padding: 10px;
  }
  .cart-icon {
    margin-left: auto; /* Move cart icon to the far right */
  }
  .container {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
  }
  .content {
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
  }
  .logo img {
    display: block;
    margin: 0 auto;
  }
  h2 {
    color: #0275d8; /* Blue color */
    margin-bottom: 20px;
  }
  p {
    margin-bottom: 20px;
  }
  button {
    background-color: #0275d8; /* Blue color */
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  button:hover {
    background-color: #025aa5;
  }
  .material-icons-outlined {
    font-family: 'Material Icons';
    font-weight: normal;
    font-style: normal;
    font-size: 24px; /* Preferred icon size */
    line-height: 1;
    display: inline-block;
  }
</style>
</head>

<body>
<div class="header">
  <ul class="menu mb-0">
    <li><button onclick="window.location.href='landing.php'" class="btn btn-link text-white">Home</button></li>
    <li><button onclick="window.location.href='products.php'" class="btn btn-link text-white">Products</button></li>
    <li><button onclick="window.location.href='profile.php'" class="btn btn-link text-white">Profile</button></li>
    <li><button onclick="window.location.href='logoutPage.php'" class="btn btn-link text-white">Logout</button></li>
    <li class="cart-icon"><button onclick="window.location.href='cart.php'" class="btn btn-link text-white"><i class="material-icons-outlined">shopping_cart</i></button></li>
  </ul>
</div>

<div class="container mt-5 pt-5">
  <div class="content shadow-sm">
    <div class="logo">
      <img src="logoo.png" alt="Your Logo" class="img-fluid" width="150px" height="150px">
    </div>
    <h2>Logout</h2>
    <p>Are you sure you want to logout?</p>
    <form action="logout.php" method="post">
      <button type="submit" class="btn btn-primary w-100">Logout</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
