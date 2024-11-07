<?php
session_start();

if (!isset($_SESSION['cID'])) {
    header("Location: login.php");
    exit();
}

require 'Customer_conx.php'; // Include your database connection file

// Prepare the SQL query

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logout</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
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
    max-width: 600px;
    margin: 0 auto; /* Change margin-top to 0 */
    text-align: center;
  }
  .content {
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
  }
  .logo {
    text-align: center;
    margin-bottom: 20px;
  }

  .logo img {
    display: block;
    margin: 0 auto;
  }

  h2 {
    color: #077a07;
    margin-bottom: 20px;
  }

  p {
    margin-bottom: 20px;
  }

  .submit {
    background-color: #dc3545;
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
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
    <li><button onclick="window.location.href='productsTable.php'">Products</button></li>
    <li><button onclick="window.location.href='income.php'">Income</button></li>
    <li><button onclick="window.location.href='logoutAdmin.php'">Logout</button></li>
  </ul>
</div>


<div class="container">
  <div class="content">
    <div class="logo">
      <img src="logo.png" alt="Your Logo">
    </div>
    <h2>Logout</h2>
    <p>Are you sure you want to logout?</p>
    <form action="logout.php" method="post">
      <button class="submit" type="submit">Logout</button>
    </form>
  </div>
</div>
</body>
</html>
