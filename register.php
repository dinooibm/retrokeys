<?php
session_start();
require 'Customer_conx.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['cName'];
    $email = $_POST['cEmail'];
    $password = $_POST['cPassword'];
    $contactNumber = $_POST['cConNum'];
    $address = $_POST['cAdd'];

    // Check if email or phone number already exists
    $checkEmailQuery = "SELECT * FROM `users` WHERE `cEmail` = '$email'";
    $checkPhoneNumberQuery = "SELECT * FROM `users` WHERE `cConNum` = '$contactNumber'";

    $resultEmail = mysqli_query($conn, $checkEmailQuery);
    $resultPhoneNumber = mysqli_query($conn, $checkPhoneNumberQuery);

    if ($resultEmail && $resultPhoneNumber) {
        if (mysqli_num_rows($resultEmail) > 0) {
            $_SESSION['error_message'] = "Email already exists!";
        } elseif (mysqli_num_rows($resultPhoneNumber) > 0) {
            $_SESSION['error_message'] = "Phone number already exists!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO `users` (`userType`, `cName`, `cEmail`, `cPass`, `cPic`, `cConNum`, `cAdd`) 
                    VALUES (0, '$fullName', '$email', '$hashedPassword', '', '$contactNumber', '$address')";
            if (mysqli_query($conn, $sql)) {
                $userId = mysqli_insert_id($conn); // Get the ID of the newly inserted user

                // Store cID in session
                $_SESSION['cID'] = $userId;

                $eventType = "new user registration";

                // Insert record into login_audit table
                $auditSql = "INSERT INTO `login_audit` (`user_id`, `event_type`) VALUES ('$userId', '$eventType')";
                mysqli_query($conn, $auditSql);

                $_SESSION['email'] = $email;
                $_SESSION['fullName'] = $fullName;
                header("Location: verify_user.php");
                exit(); 
            } else {
                $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
            }
        }
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    header("Location: register.php"); // Redirect to the registration page to display the error message
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Page</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
  }
  .container {
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
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
    color: #0275d8;
    text-align: center;
    margin-bottom: 30px;
  }
  input[type="text"],
  input[type="email"],
  input[type="password"],
  input[type="tel"],
  button[type="submit"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
  }
  button[type="submit"] {
    background-color: #0275d8;
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
  }
  button[type="submit"]:hover {
    background-color: #025aa5;
  }
  .login-link {
    text-align: center;
    margin-top: 15px;
  }
  .login-link a {
    color: #0275d8;
    text-decoration: none;
  }
  .error-message {
    color: red;
    font-weight: bold;
    margin-bottom: 15px;
  }
</style>
</head>
<body>
  <div class="container">
    <div class="logo">
      <img src="logoo.png" width="175px" height="175px" alt="Your Logo" class="img-fluid">
    </div>
    <h2>Registration</h2>
    <div class="error-message">
      <?php
      if (isset($_SESSION['error_message'])) {
          echo $_SESSION['error_message'];
          unset($_SESSION['error_message']); // Clear the message after displaying
      }
      ?>
    </div>
    <form action="register.php" method="post">
        <input type="text" name="cName" placeholder="Full Name" required>
        <input type="email" id="email" name="cEmail" placeholder="Email" required>
        <input type="password" id="password" name="cPassword" placeholder="Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$" title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character." required>
        <input type="tel" id="phoneNumber" name="cConNum" placeholder="Phone Number" required pattern="^\d+$" title="Please enter a valid phone number">
        <input type="text" name="cAdd" placeholder="Address" required>
        <button type="submit">Register</button>
    </form>
    <div class="login-link">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
