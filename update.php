<?php
session_start();
require 'Customer_conx.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['cID'])) {
    header("Location: login.php");
    exit();
}

// Fetch user information from the database
$userId = $_SESSION['cID'];
$sql = "SELECT * FROM `users` WHERE `cID` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Handle if user not found
    echo "User not found";
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a new profile picture is uploaded
    if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
        // Define upload directory
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        
        // Check file extension
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $extensions = array("jpg", "jpeg", "png", "gif");
        
        if (in_array($imageFileType, $extensions)) {
            // Move uploaded file to the target directory
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                // Update profile picture in the database
                $updatePicSql = "UPDATE `users` SET `cPic` = ? WHERE `cID` = ?";
                $stmt = $conn->prepare($updatePicSql);
                $stmt->bind_param("si", $target_file, $userId);
                $stmt->execute();
                // Update the user's profile picture in the session variable
                $_SESSION['cPic'] = $target_file;
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file format.";
        }
    }

    // Redirect to profile page
    header("Location: profile.php");
    exit();
}
?>
