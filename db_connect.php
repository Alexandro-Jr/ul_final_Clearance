<?php
$host = "localhost";
$user = "root"; // or your username
$pass = "";     // or your password
$db   = "clearance_db"; // make sure this matches
// -- Connect to the database
// -- Create connection
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>