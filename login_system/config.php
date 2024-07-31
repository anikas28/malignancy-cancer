<?php
$servername = "localhost";
$username = "root"; // replace with your database username if needed
$password = ""; // replace with your database password if needed
$dbname = "user_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
