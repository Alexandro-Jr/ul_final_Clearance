<?php  
require_once 'db_connect.php';
/**
 * My First PHP Page
 *
 * This is a simple PHP page that displays the current date and time.
 */

// Display a welcome message
// echo " University of Liberia Clearance Platform";

// Calculate dashboard metrics
$totalIncome = 0;
$totalExpenses = 0;

// Calculate total income (assuming income categories contain 'income' or similar)
$incomeQuery = "SELECT SUM(amount) AS total FROM receipts WHERE category LIKE '%income%'";
$incomeResult = $conn->query($incomeQuery);
if ($incomeResult && $incomeResult->num_rows > 0) {
    $row = $incomeResult->fetch_assoc();
    $totalIncome = $row['total'] ?? 0;
}

// Calculate total expenses (assuming expense categories contain 'expense' or similar)
$expenseQuery = "SELECT SUM(amount) AS total FROM receipts WHERE category LIKE '%expense%'";
$expenseResult = $conn->query($expenseQuery);
if ($expenseResult && $expenseResult->num_rows > 0) {
    $row = $expenseResult->fetch_assoc();
    $totalExpenses = $row['total'] ?? 0;
}

// Calculate balance
$balance = $totalIncome - $totalExpenses;

// Start the HTML document
?><!DOCTYPE html>
<html>
<head>
    <title>Financial Clearance System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<header>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Financial Clearance System </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</header>
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
                <li><a href="payment.php">Record Payments</a></li>
            </ul>
        </nav>
        <!-- link the receipt.php page to the index page -->
         

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Dashboard</h1>
            </header>

            <section class="dashboard-metrics">
                <div class="card">
                    <h3>Total Income</h3>
                    <p id="totalIncome">$<?php echo number_format($totalIncome, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Total Expenses</h3>
                    <p id="totalExpenses">$<?php echo number_format($totalExpenses, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Balance</h3>
                    <p id="balance">$<?php echo number_format($balance, 2); ?></p>
                </div>
            </section>
            <div class="recent-transactions">
    <h3>Recent Transactions</h3>
    <div class="buttons">
        <a href="payment.php" class="add-btn"><i class="fa fa-plus"></i> Add Payment</a>
    </div>
    
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Student ID</th>
          <th>Student Name</th>
          <th>Amount</th>
          <th>Category</th>
          <th>Payment Method</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT id, date, student_id, student_name, amount, category, payment_method 
                FROM receipts 
                ORDER BY date DESC 
                LIMIT 10";
        
        // Check connection first
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $result = $conn->query($sql);
        
        if ($result === false) {
            echo "<tr><td colspan='5'>Error: " . $conn->error . "</td></tr>";
        } elseif ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$row['student_id']}</td>
                    <td>{$row['student_name']}</td>
                    <td>$ {$row['amount']}</td>
                    <td>{$row['category']}</td>
                    <td>{$row['payment_method']}</td>
                    <td class='action-buttons'>
                      <a href='edit_receipt.php?id={$row['id']}' class='edit-btn'><i class='fa fa-edit'></i> Edit</a>
                      <a href='delete_receipt.php?id={$row['id']}' class='delete-btn' onclick='return confirmDelete()'><i class='fa fa-trash'></i> Delete</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='5'>No transactions found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
        </div>
    </div>
    <script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this record? This action cannot be undone.');
    }
    </script>
</body>
</html>
<?php 
// End of the PHP script
?>