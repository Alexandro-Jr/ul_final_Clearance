<?php
require_once 'db_connect.php';

// Check if receipt ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php?error=No ID provided");
    exit();
}

$receipt_id = $_GET['id'];

// Verify receipt exists
$stmt = $conn->prepare("SELECT id FROM receipts WHERE id = ?");
$stmt->bind_param("i", $receipt_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?error=Receipt not found");
    exit();
}

// Delete the record
$delete_stmt = $conn->prepare("DELETE FROM receipts WHERE id = ?");
$delete_stmt->bind_param("i", $receipt_id);

if ($delete_stmt->execute()) {
    header("Location: index.php?success=Record deleted successfully");
} else {
    header("Location: index.php?error=Error deleting record");
}
exit();
?>
