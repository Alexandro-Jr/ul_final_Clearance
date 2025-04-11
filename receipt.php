<?php
require_once 'db_connect.php';

// Check if receipt ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    die("Receipt ID is required");
}
$receipt_id = $_GET['id'];

// Get receipt data from database
$stmt = $conn->prepare("SELECT * FROM receipts WHERE id = ?");
$stmt->bind_param("i", $receipt_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    die("Receipt not found");
}

$receipt = $result->fetch_assoc();

// Validate receipt data
$required_fields = ['id', 'date', 'student_id', 'student_name', 'amount', 'payment_method', 'category', 'description'];
$missing_fields = array_diff_key(array_flip($required_fields), $receipt);
if (!empty($missing_fields)) {
    http_response_code(400);
    die("Missing required fields: " . implode(', ', array_keys($missing_fields)));
}

// Validate receipt amount
if (!is_numeric($receipt['amount'])) {
    http_response_code(400);
    die("Invalid amount: Must be numeric value");
}
if ($receipt['amount'] <= 0) {
    http_response_code(400);
    die("Invalid amount: Must be positive value");
}

// Validate receipt date
if (!preg_match('/\d{4}-\d{2}-\d{2}/', $receipt['date'])) {
    http_response_code(400);
    die("Invalid date format: Must be YYYY-MM-DD");
}

// Validate student ID and name
if (!preg_match('/^[a-zA-Z0-9]+$/', $receipt['student_id'])) {
    http_response_code(400);
    die("Invalid student ID: Only alphanumeric characters allowed");
}
if (!preg_match('/^[a-zA-Z ]+$/', $receipt['student_name'])) {
    http_response_code(400);
    die("Invalid student name: Only letters and spaces allowed");
}

// Validate payment method and category
$allowed_payment_methods = ['cash', 'bank', 'online'];
$allowed_categories = ['income', 'expens'];
if (!in_array($receipt['payment_method'], $allowed_payment_methods)) {
    http_response_code(400);
    die("Invalid payment method: Must be one of: " . implode(', ', $allowed_payment_methods));
}
if (!in_array($receipt['category'], $allowed_categories)) {
    http_response_code(400);
    die("Invalid category: Must be one of: " . implode(', ', $allowed_categories));
}

// Validate description
if (!preg_match('/^[a-zA-Z0-9\s.,]+$/', $receipt['description'])) {
    http_response_code(400);
    die("Invalid description: Only letters, numbers, spaces, commas and periods allowed");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="logo-left">
                <img src="logo.png" alt="University Logo">
            </div>
            <div class="header-center">
                <h2>University of Liberia</h2>
                <h3>Business and Finance Office</h3>
                <h3>CAPITOL HILL, MONROVIA, LIBERIA</h3>
                <h3>FINAL FINANCIAL CLEARANCE</h3>
            </div>
            <div class="logo-right">
                <img src="UL.jpg" alt="University Seal">
            </div>
        </div>

        <div class="receipt-details">
            <div class="receipt-row-pair">
                <div class="receipt-row">
                    <div class="receipt-label">Receipt ID:</div>
                    <div class="receipt-value"><?php echo $receipt['id']; ?></div>
                </div>
                <div class="receipt-row">
                    <div class="receipt-label">Date:</div>
                    <div class="receipt-value"><?php echo $receipt['date']; ?></div>
                </div>
            </div>
            <div class="receipt-row-pair">
                <div class="receipt-row">
                    <div class="receipt-label">Student ID:</div>
                    <div class="receipt-value"><?php echo $receipt['student_id']; ?></div>
                </div>
                <div class="receipt-row">
                    <div class="receipt-label">Student Name:</div>
                    <div class="receipt-value"><?php echo $receipt['student_name']; ?></div>
                </div>
            </div>
            <div class="receipt-row-pair">
                <div class="receipt-row amount-row">
                    <div class="receipt-label">Amount:</div>
                    <div class="receipt-value">$<?php echo number_format($receipt['amount'], 2); ?></div>
                </div>
                <div class="receipt-row">
                    <div class="receipt-label">Payment Method:</div>
                    <div class="receipt-value"><?php echo ucfirst($receipt['payment_method']); ?></div>
                </div>
            </div>
            <div class="receipt-row-pair">
                <div class="receipt-row">
                    <div class="receipt-label">Category:</div>
                    <div class="receipt-value"><?php echo ucfirst($receipt['category']); ?></div>
                </div>
                <div class="receipt-row">
                    <div class="receipt-label">Description:</div>
                    <div class="receipt-value"><?php echo $receipt['description']; ?></div>
                </div>
            </div>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <div class="logo-right">
                <img src="sign1.png" alt="signature">
            </div>
            <div class="signature-line"></div>
                <p>Accountant Supervisor</p>
            </div>
            <div class="signature-box">
            <div class="logo-right">
                <img src="sign1.png" alt="signature">
            </div>
                <div class="signature-line"></div>
                <p>Chief Accountant</p>
            </div>
            <div class="signature-box">
            <div class="logo-right">
                <img src="sign1.png" alt="signature">
            </div>
                <div class="signature-line"></div>
                <p>VPFFA/UL</p>
            </div>
        </div>

        <div class="receipt-footer">
            <p>This is to certify that the above person has paid the fees as indicated above.</p>
            <p>Generated on: <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>

        <div class="action-buttons no-print">
            <button class="print-btn" onclick="window.print()">Print Receipt</button>
            <button class="pdf-btn" onclick="generatePDF()">Save as PDF</button>
            <button class="close-btn" onclick="window.close()">Close</button>
        </div>
    </div>

    <script>
    function generatePDF() {
        const url = new URL(window.location.href);
        url.searchParams.set('pdf', '1');
        window.open(url.toString(), '_blank');
    }
    </script>
</body>
</html>
