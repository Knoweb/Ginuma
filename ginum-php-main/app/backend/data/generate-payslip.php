<?php
require_once '../../vendor/autoload.php';
require_once '../../vendor/tecnickcom/tcpdf/tcpdf.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['id']) && isset($_GET['year']) && isset($_GET['month']) && isset($_GET['payroll_id'])) {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $year = $_GET['year'];
    $month = $_GET['month'];
    $employee_id = $_GET['id'];
    $payroll_id = $_GET['payroll_id'];

    $company_id = $_SESSION['company_id'];

    $sql = "SELECT * FROM company_tbl WHERE company_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $company_row = $result->fetch_assoc();
    $stmt->close();

    if (!$company_row) {
        die('Company data not found.');
    }

    $sql = "SELECT * FROM employee_tbl et INNER JOIN payroll_tbl pt ON (et.employee_id=pt.employee_id) WHERE et.company_id=? AND et.employee_id=? AND pt.payroll_id=? AND pt.month=? AND pt.year=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $company_id, $employee_id, $payroll_id, $month, $year);
    $stmt->execute();
    $result1 = $stmt->get_result();
    $employee_row = $result1->fetch_assoc();
    $stmt->close();

    if (!$employee_row) {
        die('Employee data not found.');
    }

    $dateObj = new DateTimeImmutable($employee_row['date_added']);
    $custom_datetime = $dateObj->format("M d, Y - g:i a");

    $total_earning = (float) $employee_row['basic_salary'] + (float) $employee_row['allowance'] + (float) $employee_row['ot_pay'] + (float) $employee_row['bonus'];
    $total_deduction = (float) $employee_row['epf_8'] + (float) $employee_row['appit'] + (float) $employee_row['loan'] + (float) $employee_row['other_deductions'];
    $net_pay = $total_earning - $total_deduction;

    $pdf = new TCPDF();
    $pdf->AddPage();

    $html = '
    <style>
        .payslip-container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 5px; /* Adjusted padding */
        }

        .payslip-header {
            text-align: center;
            margin-bottom: 2px; /* Reduced margin */
            background-color: #007bff;
            color: #fff;
            padding: 2px; /* Reduced padding */
            border-radius: 5px;
        }

        .payslip-header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .payslip-header h3 {
            margin: 0;
            font-size: 14px;
            font-weight: normal;
        }

        .payslip-details {
            margin-bottom: 2px; /* Reduced margin */
            font-size: 12px; /* Reduced font size */
        }

        .payslip-details .row {
            width: 100%;
            display: flex;
            margin-bottom: 2px; /* Reduced margin */
        }

        .payslip-details .col-md-6 {
            flex: 1;
            padding: 0 2px; /* Reduced padding */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px; /* Reduced margin */
        }

        .table th,
        .table td {
            padding: 2px; /* Reduced padding */
            font-size: 12px; /* Reduced font size */
            vertical-align: middle;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .table td {
            background-color: #fff;
        }

        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .authorized-signatory {
            text-align: right;
            font-size: 12px; /* Reduced font size */
            margin-top: 5px; /* Reduced margin */
        }

        .print-btn {
            text-align: center;
            margin-top: 5px; /* Reduced margin */
        }
    </style>    

    <div class="payslip-container">
        <div class="payslip-header">
            <h2>Payslip for the Month of ' . htmlspecialchars(DateTime::createFromFormat("!m", $month)->format("F")) . ' ' . htmlspecialchars($year) . '</h2>
            <h3>Payslip No.: ' . htmlspecialchars($payroll_id) . '</h3>
        </div>
        <div class="payslip-details">
            <div class="row">
                <div class="col-md-6">
                    <p>' . htmlspecialchars($company_row['company_name']) . '<br>
                        ' . htmlspecialchars($company_row['company_registered_address']) . '<br>
                        <a href="mailto:' . htmlspecialchars($company_row['email']) . '">' . htmlspecialchars($company_row['email']) . '</a>
                    </p>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <p>Payment Date:<br>' . htmlspecialchars($custom_datetime) . '</p>
                    <p>Payment To:<br><strong>' . htmlspecialchars($employee_row['first_name'] . " " . $employee_row['last_name']) . '</strong><br>
                        ' . htmlspecialchars($employee_row['address']) . '<br>
                    </p>
                </div>
            </div>
        </div>
        <div class="payslip-body">
            <table class="table">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <h4>Earnings</h4>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Basic</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($employee_row['basic_salary']) . '</td>
                                </tr>
                                <tr>
                                    <td>Allowance</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($employee_row['allowance']) . '</td>
                                </tr>
                                <tr>
                                    <td>Over Time</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($employee_row['ot_pay']) . '</td>
                                </tr>
                                <tr>
                                    <td>Bonus</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($employee_row['bonus']) . '</td>
                                </tr>
                                <tr class="total-row">
                                    <td>Total Earnings</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($total_earning) . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <h4>Deductions</h4>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>EPF</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($employee_row['epf_8']) . '</td>
                                </tr>
                                <tr>
                                    <td>APPIT</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($employee_row['appit']) . '</td>
                                </tr>
                                <tr>
                                    <td>Loans</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($employee_row['loan']) . '</td>
                                </tr>
                                <tr>
                                    <td>Other</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($employee_row['other_deductions']) . '</td>
                                </tr>
                                <tr class="total-row">
                                    <td>Total Deductions</td>
                                    <td class="text-end">' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($total_deduction) . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <h4>Net Pay: ' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($net_pay) . '</h4>
                </div>
                <div class="col-md-6 text-end authorized-signatory">
                    <p>For ' . htmlspecialchars($company_row['company_name']) . '<br>Authorized Signatory</p>
                </div>
            </div>
        </div>
    </div>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('payslip.pdf', 'D');
} else {
    echo "No Data";
}
?>