<?php
session_start();
// import the composer dependencies
require_once '../../vendor/autoload.php';

// Import the database connection class
require_once '../connection/conn.php';
// Create a database instance
$db = new DBConnection();
// Create the database connection
$conn = $db->conn;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Check if the date parameter is provided in the URL
if (!isset($_GET['date'])) {
    die("date parameter is required.");
}

// Parse the year and month from the date parameter
$date = $_GET['date'];
list($year, $month) = explode('-', $date);
$year = (int) $year;
$month = (int) $month;

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Retrieve company_id from the session
$company_id = $_SESSION['company_id'];

// Prepare the SQL query
$sql = "
    SELECT 
        bd.bank_name AS `Bank Name`, 
        bd.account_number AS `Account No.`, 
        bd.account_name AS `Account Name`, 
        bs.transaction_date AS `Date`,
        bs.transaction_description AS `Details`,
        CASE 
            WHEN bs.d_or_w = 'Deposit' THEN bs.amount
            ELSE 0
        END AS `Deposits`,
        CASE 
            WHEN bs.d_or_w = 'Withdrawal' THEN bs.amount
            ELSE 0
        END AS `Withdrawals`,
        bs.balance AS `Balance`
    FROM 
        bank_statement_tbl bs
    JOIN 
        bank_account_tbl ba ON bs.bank_id = ba.bank_id
    JOIN 
        bank_details_tbl bd ON ba.bank_details_id = bd.bank_details_id
    WHERE 
        YEAR(bs.date_created) = ? AND
        MONTH(bs.date_created) = ? AND
        bs.company_id = ?
    ORDER BY 
        bs.date_created ASC
";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind the year, month, and company_id parameters
$stmt->bind_param("iii", $year, $month, $company_id);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Fetch the data
$bankStatements = [];
while ($row = $result->fetch_assoc()) {
    $bankStatements[] = $row;
}

// fetch the company details from the database
$sql = "SELECT * FROM company_tbl WHERE company_id = $company_id";
$result = $conn->query($sql);
$company_row = $result->fetch_assoc();

// Close the statement
$stmt->close();

// Set default font and size
$spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

// Set header values
$sheet->setCellValue('B2', 'Bank Statement of ' . $bankStatements[0]['Account Name']);
$sheet->setCellValue('B4', 'Account number: ' . $bankStatements[0]['Account No.']);
$sheet->setCellValue('B5', 'Account name: ' . $bankStatements[0]['Account Name']);
$sheet->setCellValue('D4', 'Statement Period: from ' . sprintf('%02d', $month) . '/01/' . $year . ' to ' . sprintf('%02d', $month) . '/' . date('t', strtotime("$year-$month-01")) . '/' . $year);
$sheet->setCellValue('D5', 'Name: ' . $company_row['company_name']);
$sheet->setCellValue('D6', 'Business name: ' . $company_row['company_name']);
$sheet->setCellValue('D7', 'Address: ' . $company_row['company_registered_address']);
$sheet->setCellValue('D8', 'Phone number: ' . $company_row['phone_no']);

// Set activity summary headers
$sheet->setCellValue('B10', 'Activity Summary');
$sheet->setCellValue('B12', 'Opening Balance:');
$sheet->setCellValue('B13', 'Total Deposits:');
$sheet->setCellValue('B14', 'Total Withdrawals:');
$sheet->setCellValue('B15', 'Closing Balance:');

// Set transaction history headers
$sheet->setCellValue('B17', 'Transaction History');
$sheet->setCellValue('B19', 'Date');
$sheet->setCellValue('C19', 'Details');
$sheet->setCellValue('D19', 'Deposits');
$sheet->setCellValue('E19', 'Withdrawals');
$sheet->setCellValue('F19', 'Balance');

// Style for headers
$headerStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D3D3D3']]
];

// Apply header styles
$sheet->getStyle('B19:F19')->applyFromArray($headerStyle);

// Initialize variables
$totalDeposits = 0;
$totalWithdrawals = 0;
$openingBalance = 0;
$closingBalance = 0;

// Check if there are bank statements
if (!empty($bankStatements)) {
    $openingBalance = $bankStatements[0]['Balance'];
    $closingBalance = end($bankStatements)['Balance'];

    foreach ($bankStatements as $statement) {
        $totalDeposits += $statement['Deposits'];
        $totalWithdrawals += $statement['Withdrawals'];
    }
}

// Populate data
$rowNumber = 20;
foreach ($bankStatements as $statement) {
    $sheet->setCellValue('B' . $rowNumber, $statement['Date']);
    $sheet->setCellValue('C' . $rowNumber, $statement['Details']);
    $sheet->setCellValue('D' . $rowNumber, $statement['Deposits']);
    $sheet->setCellValue('E' . $rowNumber, $statement['Withdrawals']);
    $sheet->setCellValue('F' . $rowNumber, $statement['Balance']);
    $rowNumber++;
}

// Set activity summary values
$sheet->setCellValue('C12', $openingBalance);
$sheet->setCellValue('C13', $totalDeposits);
$sheet->setCellValue('C14', $totalWithdrawals);
$sheet->setCellValue('C15', $closingBalance);

// Auto size columns for clarity
foreach (range('B', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set the content type and headers for downloading the file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="bank_statement.xlsx"');
header('Cache-Control: max-age=0');

// Write the spreadsheet to php://output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Terminate the script after sending the file
exit();
?>