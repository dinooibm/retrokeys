<?php
session_start();
require 'Customer_conx.php';

// Insert logout event into audit log
if (isset($_SESSION['cID'])) {
    $userId = $_SESSION['cID'];
    $eventType = 'logout';
    $sqlInsertLog = "INSERT INTO login_audit (user_id, event_type) VALUES (?, ?)";
    $stmtInsertLog = $conn->prepare($sqlInsertLog);
    if ($stmtInsertLog) {
        $stmtInsertLog->bind_param("is", $userId, $eventType);
        $stmtInsertLog->execute();
        $stmtInsertLog->close();
    } else {
        die("Prepare failed: " . $conn->error);
    }
}

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
