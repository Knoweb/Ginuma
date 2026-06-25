<?php
session_start();
require_once '../../vendor/autoload.php';
require_once '../connection/conn.php';

$db = new DBConnection();
$conn = $db->conn;

if (!isset($_SESSION['company_id'])) {
    die("Company ID is not set in the session.");
}

if (isset($_GET['invoice_id'])) {
    $invoice_no = $_GET['invoice_id'];

    // Get the company details from the database
    $sql = "SELECT * FROM company_tbl WHERE company_id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $_SESSION['company_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            die("No company found with the provided company ID.");
        }
        $company = $result->fetch_assoc();
        $company_name = $company['company_name'];
        $company_registered_address = $company['company_registered_address'];
        $email = $company['email'];
        $stmt->close();
    } else {
        die("Error preparing the company query: " . $conn->error);
    }

    // Get the sales receipt details
    $sql = "SELECT srt.*, itmt.item_name, itmt.unit FROM sales_receipt_tbl srt 
            INNER JOIN item_tbl itmt ON srt.item_id=itmt.item_id 
            WHERE srt.invoice_no=? AND srt.company_id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $invoice_no, $_SESSION['company_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            die("No sales receipt found with the provided invoice ID.");
        }
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        die("Error preparing the sales receipt query: " . $conn->error);
    }

    $payment_method = $rows[0]['payment_method'];
    $status = $payment_method === 'Cash' ? "Paid" : "UnPaid";

    // Create new PDF document
    $pdf = new TCPDF();
    $pdf->AddPage();

    // Set title
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 10, $company_name, 0, 1, 'C');

    // Company details
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $company_registered_address, 0, 1, 'C');
    $pdf->Cell(0, 10, $email, 0, 1, 'C');

    // Invoice details
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Invoice: ' . $invoice_no, 0, 1, 'R');
    $pdf->Cell(0, 10, 'Date: ' . $rows[0]['date_made'], 0, 1, 'R');
    $pdf->Cell(0, 10, 'Status: ' . $status, 0, 1, 'R');

    // Bill to
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Billed To: ', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $rows[0]['customer_name'], 0, 1);

    // Order summary
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(10, 10, 'No.', 1);
    $pdf->Cell(80, 10, 'Item', 1);
    $pdf->Cell(30, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Quantity', 1);
    $pdf->Cell(40, 10, 'Total', 1, 1);

    $pdf->SetFont('helvetica', '', 12);
    $sub_total = 0;
    $total_tax = 0;
    $total_discount = 0;
    foreach ($rows as $key => $row) {
        $total = $row['sold_price'] * $row['qty'];
        $sub_total += $total;
        $total_tax += $row['tax'];
        $total_discount += $row['discount'];

        $pdf->Cell(10, 10, $key + 1, 1);
        $pdf->Cell(80, 10, $row['item_name'], 1);
        $pdf->Cell(30, 10, $_SESSION['currency_code'] . ' ' . number_format($row['sold_price'], 2), 1);
        $pdf->Cell(30, 10, $row['qty'] . ' ' . $row['unit'], 1);
        $pdf->Cell(40, 10, $_SESSION['currency_code'] . ' ' . number_format($total, 2), 1, 1);
    }

    // Totals
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(150, 10, 'Sub Total', 1);
    $pdf->Cell(40, 10, $_SESSION['currency_code'] . ' ' . number_format($sub_total, 2), 1, 1);
    $pdf->Cell(150, 10, 'Discount', 1);
    $pdf->Cell(40, 10, '- ' . $_SESSION['currency_code'] . ' ' . number_format($total_discount, 2), 1, 1);
    $pdf->Cell(150, 10, 'Tax', 1);
    $pdf->Cell(40, 10, $_SESSION['currency_code'] . ' ' . number_format($total_tax, 2), 1, 1);
    $pdf->Cell(150, 10, 'Total', 1);
    $pdf->Cell(40, 10, $_SESSION['currency_code'] . ' ' . number_format(($sub_total + $total_tax) - $total_discount, 2), 1, 1);

    // Output PDF
    $filename = "Invoice-" . $rows[0]['customer_name'] . "-" . date("Y-m-d h:i:s A") . ".pdf";
    $pdf->Output($filename, 'I');
} else {
    die("Invoice ID is not set.");
}
?>