<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">

    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f0f0;
        }

        /* Header Styles */
        .header {
            background-color: #1b3a57;
            color: #fff;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 999;
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

        .menu button {
            color: #fff;
            background-color: #1b3a57;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .menu button:hover {
            background-color: #335f8a;
        }
        
        .cart-icon {
            margin-left: auto;
        }

        /* Container Styles */
        .container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
        }

        .content {
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #1b3a57;
            margin-bottom: 20px;
        }

        p {
            color: #333;
            margin-bottom: 20px;
        }

        a {
            color: #1b3a57;
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: #335f8a;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <ul class="menu">
            <li><button onclick="window.location.href='landing.php'">Home</button></li>
            <li><button onclick="window.location.href='products.php'">Products</button></li>
            <li><button onclick="window.location.href='profile.php'">Profile</button></li>
            <li><button onclick="window.location.href='logoutPage.php'">Logout</button></li>
            <li class="cart-icon"><button onclick="window.location.href='cart.php'"><i class="material-icons">shopping_cart</i></button></li>
        </ul>
    </div>
    
    <!-- Thank You Message -->
    <div class="container">
        <div class="content">
            <h1>Thank You for Your Order!</h1>
            <p>Your order has been successfully placed.</p>
            <p>We'll send you an email confirmation shortly.</p>
            <p><a href="landing.php">Continue Shopping</a></p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
