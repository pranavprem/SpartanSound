<?php
$servername = "localhost";
$username = "spartanuser";
$password = "wubalubadubdub";
$dbname = "spartansound";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
/*if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
 echo "Connected successfully";*/
?>