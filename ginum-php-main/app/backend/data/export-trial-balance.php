<?php
session_start();
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

require '../../vendor/autoload.php';

function formatDateWithSuffix($timestamp)
{
    $day = date('j', $timestamp);
    $month = date('F', $timestamp);
    $year = date('Y', $timestamp);

    if ($day % 10 == 1 && $day != 11) {
        $suffix = 'st';
    } elseif ($day % 10 == 2 && $day != 12) {
        $suffix = 'nd';
    } elseif ($day % 10 == 3 && $day != 13) {
        $suffix = 'rd';
    } else {
        $suffix = 'th';
    }

    return $day . $suffix . ' ' . $month . ', ' . $year;
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

$endDateTimestamp = strtotime($endDate);
$formattedDate = formatDateWithSuffix($endDateTimestamp);

$sql = "
            SELECT 
                sa.sub_account_name AS actual_sub_account_name,
                at.account_name AS account_name,
                COALESCE(tl.balance, 0) + COALESCE(csb.balance, 0) AS balance,
                COALESCE(st_balance.total_sub_account_type_balance, 0) AS total_sub_account_type_balance
            FROM 
                sub_account_tbl sa
            INNER JOIN 
                account_tbl at ON sa.account_id = at.account_id
            LEFT JOIN 
                (
                    SELECT 
                        sub_account_id,
                        SUM(CASE 
                            WHEN transaction_type IN ('Assets Addition', 'Item Addition', 'Expenses Addition') THEN amount 
                            WHEN transaction_type IN ('Inventory Deduction', 'Assets Deduction', 'Expenses Deduction') THEN -amount 
                            WHEN transaction_type IN ('Liability Addition', 'Equity Addition', 'Retained Earnings Addition') THEN -amount 
                            WHEN transaction_type IN ('Liability Deduction', 'Equity Deduction', 'Retained Earnings Deduction') THEN amount 
                            ELSE 0 
                        END) AS balance
                    FROM 
                        transaction_log_tbl
                    WHERE 
                        company_id = ?
                        AND transaction_date BETWEEN ? AND ?
                    GROUP BY 
                        sub_account_id
                ) AS tl ON sa.sub_account_id = tl.sub_account_id
            LEFT JOIN 
                company_sub_account_balance csb ON sa.sub_account_id = csb.sub_account_id AND csb.company_id = ?
            LEFT JOIN 
                (
                    SELECT 
                        sa.sub_account_id,
                        SUM(COALESCE(csab.balance, 0)) AS total_sub_account_type_balance
                    FROM 
                        sub_account_tbl sa
                    LEFT JOIN 
                        sub_account_type_tbl sat ON sa.sub_account_id = sat.sub_account_id
                    LEFT JOIN 
                        company_sub_account_balance csab ON (csab.sub_account_id = sa.sub_account_id 
                        AND csab.sub_account_type_id IS NULL 
                        AND csab.company_id = ?) 
                        OR (csab.sub_account_type_id = sat.sub_account_type_id 
                        AND csab.company_id = ?)
                    GROUP BY 
                        sa.sub_account_id
                ) AS st_balance ON sa.sub_account_id = st_balance.sub_account_id
            WHERE 
                at.account_name IN ('Assets', 'Liabilities', 'Equity', 'Income', 'Expenses')
            ORDER BY 
                at.account_name ASC, sa.sub_account_name ASC;
        ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issiii", $_SESSION['company_id'], $startDate, $endDate, $_SESSION['company_id'], $_SESSION['company_id'], $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Trial Balance Sheet');

// Add company name at the top
$sheet->setCellValue('A1', $_SESSION['company_name']);
$sheet->getStyle('A1')->getFont()->setBold(true);
$sheet->getStyle('A1')->getFont()->setSize(18);
$sheet->getStyle('A1')->getFont()->getColor()->setARGB('FF0000'); // red font color
$sheet->mergeCells('A1:D1');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Center align the company name

// Add report title and date
$val = "Trial Balance as on $formattedDate";
$sheet->setCellValue('A2', $val);
$sheet->mergeCells('A2:D2');
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A2')->getFont()->setBold(true);
$sheet->getStyle('A2')->getFont()->setSize(14);

// Set header row
$debit_header = "Debit (" . $_SESSION['currency_code'] . ")";
$credit_header = "Credit (" . $_SESSION['currency_code'] . ")";
$sheet->setCellValue('A3', 'Sr. No.');
$sheet->setCellValue('B3', 'Particulars');
$sheet->setCellValue('C3', $debit_header);
$sheet->setCellValue('D3', $credit_header);
$sheet->getStyle('A3:D3')->getFont()->setBold(true);
$sheet->getStyle('A3:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A3:D3')->getFill()->setFillType(Fill::FILL_SOLID);
$sheet->getStyle('A3:D3')->getFill()->getStartColor()->setARGB('CCCCCC'); // light gray background

// Set borders for header row
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A3:D3')->applyFromArray($styleArray);

$rowIndex = 4;
$totalDebit = 0;
$totalCredit = 0;

if ($result->num_rows > 0) {
    $srNo = 1;
    while ($row = $result->fetch_assoc()) {
        $debit = 0;
        $credit = 0;

        // Determine debit and credit based on account type and balance
        if ($row['account_name'] == 'Assets' || $row['account_name'] == 'Expenses') {
            if ($row['balance'] > 0) {
                $debit = $row['balance'];
            } elseif ($row['balance'] < 0) {
                $credit = abs($row['balance']);
            }
        } elseif ($row['account_name'] == 'Liabilities' || $row['account_name'] == 'Equity' || $row['account_name'] == 'Income') {
            if ($row['balance'] > 0) {
                $credit = $row['balance'];
            } elseif ($row['balance'] < 0) {
                $debit = abs($row['balance']);
            }
        }

        $totalDebit += $debit;
        $totalCredit += $credit;

        $sheet->setCellValue('A' . $rowIndex, $srNo);
        $sheet->setCellValue('B' . $rowIndex, $row['sub_account_name']);
        $sheet->setCellValue('C' . $rowIndex, $debit);
        $sheet->setCellValue('D' . $rowIndex, $credit);
        $rowIndex++;
        $srNo++;
    }

    // Add separator between accounts and total
    $sheet->insertNewRowBefore($rowIndex, 1);
    $sheet->getStyle('A' . $rowIndex . ':D' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A' . $rowIndex . ':D' . $rowIndex)->getFill()->getStartColor()->setARGB('AAAAAA'); // dark gray separator

    // Set borders for separator row
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];
    $sheet->getStyle('A' . $rowIndex . ':D' . $rowIndex)->applyFromArray($styleArray);

    $rowIndex++;

    $sheet->setCellValue('A' . $rowIndex, '');
    $sheet->setCellValue('B' . $rowIndex, 'Total');
    $sheet->setCellValue('C' . $rowIndex, number_format($totalDebit, 2));
    $sheet->setCellValue('D' . $rowIndex, number_format($totalCredit, 2));
    $sheet->getStyle('A' . $rowIndex . ':D' . $rowIndex)->getFont()->setBold(true);
    $sheet->getStyle('A' . $rowIndex . ':D' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Set borders for total row
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];
    $sheet->getStyle('A' . $rowIndex . ':D' . $rowIndex)->applyFromArray($styleArray);
}

// Auto size columns for each worksheet
foreach (range('A', 'D') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$filename = 'trial_balance_sheet_' . date('Y-m-d') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>