<?php
$servername = "localhost";
$username = "root"; 
$password = "";    
$dbname = "dbretailer";

// Create connection
$conn_retailer = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn_retailer->connect_error) {
  die("Connection failed: " . $conn_retailer->connect_error);
}
?>
