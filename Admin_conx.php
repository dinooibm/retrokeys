<?php
    $host='localhost';
    $username='GA_Admin';
    $password='';
    $database='greenantz_db';

    $conn = mysqli_connect($host,$username,$password,$database);
    
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL:". mysqli_connect_error();
    }
?>