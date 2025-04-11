<?php 
/**
 * Payment Processing Page
 */
require_once 'db_connect.php';

// Display a welcome message
// echo " University of Liberia Clearance Platform";

// Start the HTML document
?><!DOCTYPE html>
<html>
<head>
    <title>Make Payment</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo" style="text-align: center; margin-top: 20px;">
                <a href="index.html" class="logo-link">
                    <img src="logo.png" alt="Logo" class="logo-img" width="120px" height="120px">
                </a>
            </div>
            <h2>Finance App</h2>
        
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="payment.php"> Record Payment</a></li>
            </ul>
        </nav>
        <!-- link the receipt.php page to the index page -->


        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Record a Payment</h1>
            </header>
            <?php
            // Check if the form has been submitted
            if (isset($_POST['submit'])) {
                // Validate the form data
                $student_id = filter_var($_POST['student_id']);
                $student_name = filter_var($_POST['student_name']);
                $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);
                $date = filter_var($_POST['date']);
                $category = filter_var($_POST['category']);
                $payment_method = filter_var($_POST['payment_method']);
                $description = filter_var($_POST['description']);

                // Check if the file has been uploaded
                if ($_FILES['receipt_file']['name']) {
                    $file_tmp = $_FILES['receipt_file']['tmp_name'];
                    $file_name = time() . '_' . basename($_FILES['receipt_file']['name']);
                    move_uploaded_file($file_tmp, "uploads/$file_name");
                } else {
                    $file_name = '';
                }

                // Insert into DB
                $stmt = $conn->prepare("INSERT INTO receipts (student_id, student_name, amount, date, category, payment_method, description, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdsssss", $student_id, $student_name, $amount, $date, $category, $payment_method, $description, $file_name);
                
                if ($stmt->execute()) {
                    $receipt_id = $stmt->insert_id;
                    echo "<p style='color: green;'>Payment added successfully! 
                          <a href='receipt.php?id=$receipt_id' target='_blank'>View Receipt</a>
                          <button onclick=\"window.open('receipt.php?id=$receipt_id','_blank').print();\">Print Receipt</button></p>";
                } else {
                    echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
                }
            }
            ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Student ID:</label>
                        <input type="text" name="student_id" required>
                    </div>
                    <div class="form-group">
                        <label>Student Name:</label>
                        <input type="text" name="student_name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Amount:</label>
                        <input type="number" step="0.01" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" name="date" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category:</label>
                        <select name="category" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment Method:</label>
                        <select name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank">Bank Deposit</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Upload Receipt:</label>
                    <input type="file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <button type="submit" name="submit">Submit Payment</button>
            </form>          
        </div>
    </div>
</body>
<footer>
    <p>&copy; 2025 University of Liberia Clearance Platform</p>
</footer>
</html>
<?php 
// End of the PHP script
?>