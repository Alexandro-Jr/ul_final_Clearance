<?php
require_once 'db_connect.php';

// Check if receipt ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php?error=No ID provided");
    exit();
}

$receipt_id = $_GET['id'];

// Get receipt data
$stmt = $conn->prepare("SELECT * FROM receipts WHERE id = ?");
$stmt->bind_param("i", $receipt_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?error=Receipt not found");
    exit();
}

$receipt = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $student_name = trim($_POST['student_name']);
    $amount = floatval($_POST['amount']);
    $category = $_POST['category'];
    $payment_method = $_POST['payment_method'];
    $description = trim($_POST['description']);

    // Update record
    $update_stmt = $conn->prepare("UPDATE receipts SET 
        student_name = ?, 
        amount = ?, 
        category = ?, 
        payment_method = ?, 
        description = ? 
        WHERE id = ?");
    $update_stmt->bind_param("sdsssi", 
        $student_name, 
        $amount, 
        $category, 
        $payment_method, 
        $description, 
        $receipt_id);
    
    if ($update_stmt->execute()) {
        header("Location: index.php?success=Record updated successfully");
        exit();
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Receipt</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <div class="logo" style="text-align: center; margin-top: 20px;">
                <a href="index.html" class="logo-link">
                    <img src="logo.png" alt="Logo" class="logo-img" width="120px" height="120px">
                </a>
            </div>
            <h2>Finance App</h2>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="payment.php">Record Payments</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <header>
                <h1>Edit Receipt #<?php echo htmlspecialchars($receipt['id']); ?></h1>
            </header>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label for="student_name">Student Name:</label>
                    <input type="text" id="student_name" name="student_name" 
                           value="<?php echo htmlspecialchars($receipt['student_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="amount">Amount ($):</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0"
                           value="<?php echo htmlspecialchars($receipt['amount']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="income" <?php echo $receipt['category'] === 'income' ? 'selected' : ''; ?>>Income</option>
                        <option value="expens" <?php echo $receipt['category'] === 'expens' ? 'selected' : ''; ?>>Expense</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_method">Payment Method:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="cash" <?php echo $receipt['payment_method'] === 'cash' ? 'selected' : ''; ?>>Cash</option>
                        <option value="bank" <?php echo $receipt['payment_method'] === 'bank' ? 'selected' : ''; ?>>Bank</option>
                        <option value="online" <?php echo $receipt['payment_method'] === 'online' ? 'selected' : ''; ?>>Online</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($receipt['description']); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <a href="index.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
