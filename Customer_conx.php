<?php
$servername = 'localhost';
$username = 'GA_Customer';
$password = '456';
$database = 'greenantz_db';

$conn = mysqli_connect($servername, $username, $password, $database);

if (mysqli_connect_error()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit(); // Exit the script if connection fails
}
?>
