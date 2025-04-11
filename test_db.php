<?php
require_once 'db_connect.php';

// Test database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connection successful\n";

// Check if receipts table exists
$result = $conn->query("SHOW TABLES LIKE 'receipts'");
if ($result->num_rows > 0) {
    echo "Receipts table exists\n";
    
    // Count receipts
    $count = $conn->query("SELECT COUNT(*) as count FROM receipts")->fetch_assoc()['count'];
    echo "Found $count receipts in database\n";
    
    // Get first receipt ID if any exist
    if ($count > 0) {
        $id = $conn->query("SELECT id FROM receipts LIMIT 1")->fetch_assoc()['id'];
        echo "Sample receipt ID: $id\n";
    }
} else {
    echo "Receipts table does not exist\n";
}

$conn->close();
?>
