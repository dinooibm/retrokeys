<?php
session_start();
require 'Customer_conx.php';

// Initialize login attempts session variable if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Check if the user has exceeded the maximum login attempts
if ($_SESSION['login_attempts'] >= 3) {
    // Check if the user has been locked out for more than 30 seconds
    if (isset($_SESSION['lockout_time']) && time() - $_SESSION['lockout_time'] < 30) {
        $remainingTime = 30 - (time() - $_SESSION['lockout_time']);
        echo "<h3 style='color: red;'>You have been temporarily locked out. Please try again in $remainingTime seconds.</h3>";
        echo "<script>
                var countdown = $remainingTime;
                var timer = setInterval(function() {
                    countdown--;
                    document.getElementById('countdown').textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(timer);
                        document.getElementById('cEmail').disabled = false;
                        document.getElementById('cPassword').disabled = false;
                        document.getElementById('submitBtn').disabled = false;
                        document.getElementById('loginMessage').innerHTML = '';
                    }
                }, 1000);
                document.getElementById('cEmail').disabled = true;
                document.getElementById('cPassword').disabled = true;
                document.getElementById('submitBtn').disabled = true;
              </script>";
        exit();
    } else {
        // Reset login attempts and lockout time
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['cEmail'];
    $password = $_POST['cPassword'];

    $sqlFetch = "SELECT * FROM `users` WHERE `cEmail` = ?";
    $stmt = $conn->prepare($sqlFetch);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();

    if (!$result) {
        die("Error in fetching result: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_pass = $row['cPass'];

        if (password_verify($password, $hashed_pass)) {
            $_SESSION['cID'] = $row['cID'];
            $_SESSION['cName'] = $row['cName'];
            $userType = $row['userType']; // Assuming 'userType' is the column name for user type in the database

            // Insert login event into audit log
            $userId = $row['cID'];
            $eventType = 'login';
            $sqlInsertLog = "INSERT INTO login_audit (user_id, event_type) VALUES (?, ?)";
            $stmtInsertLog = $conn->prepare($sqlInsertLog);
            if ($stmtInsertLog) {
                $stmtInsertLog->bind_param("is", $userId, $eventType);
                $stmtInsertLog->execute();
                $stmtInsertLog->close();
            } else {
                die("Prepare failed: " . $conn->error);
            }

            // Redirect based on userType
            if ($userType == 0) {
                header("Location: landing.php");
            } elseif ($userType == 1) {
                header("Location: adminPage.php");
            } else {
                // Handle unknown userType
                // Redirect to an error page or handle it as per your requirement
                header("Location: loginerr.php");
            }
            exit();
        } else {
            // Increment login attempts upon failed login
            $_SESSION['login_attempts']++;

            // Set lockout time if login attempts exceed limit
            if ($_SESSION['login_attempts'] >= 3) {
                $_SESSION['lockout_time'] = time();
            }

            header("Location: loginerr.php");
            exit();
        }
    } else {
        // Increment login attempts upon failed login
        $_SESSION['login_attempts']++;

        // Set lockout time if login attempts exceed limit
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['lockout_time'] = time();
        }

        header("Location: loginerr.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>
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
  text-align: center;
  margin-bottom: 30px;
}
input[type="email"],
input[type="password"],
button[type="submit"] {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-sizing: border-box;
}
button[type="submit"] {
  background-color: #94dc06;
  color: #fff;
  border: none;
  padding: 12px 20px;
  border-radius: 5px;
  cursor: pointer;
}
button[type="submit"]:hover {
  background-color: #529e52;
}
.login-link {
  text-align: center;
  margin-top: 15px;
}
.login-link a {
  color: #91ca64;
  text-decoration: none;
}
</style>
</head>
<body>

  <div class="container">
    <div class="logo">
      <img src="logo.png" alt="Your Logo">
    </div>
    <h2>Login</h2>
    <form action="login.php" method="post">
    <h3 style="color: red;">Credentials do not match or does not exist!</h3>
    <?php if ($_SESSION['login_attempts'] >= 3): ?>
        <h3 style="color: red;" id="loginMessage">You have been temporarily locked out. Please try again in <span id="countdown">30</span> seconds.</h3>
        <script>
            var countdown = 30;
            var timer = setInterval(function() {
                countdown--;
                document.getElementById('countdown').textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timer);
                    document.getElementById('cEmail').disabled = false;
                    document.getElementById('cPassword').disabled = false;
                    document.getElementById('submitBtn').disabled = false;
                    document.getElementById('loginMessage').innerHTML = '';
                }
            }, 1000);
            document.getElementById('cEmail').disabled = true;
            document.getElementById('cPassword').disabled = true;
            document.getElementById('submitBtn').disabled = true;
        </script>
    <?php endif; ?>
      <input type="email" name="cEmail" placeholder="Email" required>
      <input type="password" name="cPassword" placeholder="Password" required>
      <button type="submit" id="submitBtn">Login</button>
    </form>

    <div class="login-link">
      Don't have an account? <a href="register.php">Register</a>
    </div>
  </div>
</body>
</html>
