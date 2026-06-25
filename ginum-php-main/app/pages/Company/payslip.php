<?php
// session_start();
if (isset($_GET['id']) && isset($_GET['year']) && isset($_GET['month']) && isset($_GET['payroll_id'])) {
    // fecth the parameters from the url
    $year = $_GET['year'];
    $month = $_GET['month'];
    $employee_id = $_GET['id'];
    $payroll_id = $_GET['payroll_id'];

    $company_id = $_SESSION['company_id'];

    // get the company details from the database using company id
    $sql = "SELECT * FROM company_tbl WHERE company_id='" . $company_id . "'";
    $result = $conn->query($sql);
    $company_row = $result->fetch_assoc();
    $result->close();

    // get the employee details from the database using employee id and the payroll_id
    $sql = "SELECT * FROM employee_tbl et INNER JOIN payroll_tbl pt ON (et.employee_id=pt.employee_id) WHERE et.company_id=? AND et.employee_id=? AND pt.payroll_id=? AND pt.month=? AND pt.year=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $company_id, $employee_id, $payroll_id, $month, $year);
    $stmt->execute();
    $result1 = $stmt->get_result();
    $employee_row = $result1->fetch_assoc();
    $stmt->close();

    // get the customized date from the payslip created date
    // eg: Jul 02, 2022 - 12:30 am -> M d, Y - g:i a
    $dateObj = new DateTimeImmutable($employee_row['date_added']);
    $custom_datetime = $dateObj->format("M d, Y - g:i a");

    // to calculate the total earning, total deduction and the net pay
    $total_earning = (float) $employee_row['basic_salary'] + (float) $employee_row['allowance'] + (float) $employee_row['ot_pay'] + (float) $employee_row['bonus'];
    $total_deduction = (float) $employee_row['epf_8'] + (float) $employee_row['appit'] + (float) $employee_row['loan'] + (float) $employee_row['other_deductions'];
    ?>

    <style>
        .payslip-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .payslip-header,
        .payslip-footer {
            text-align: center;
            margin-bottom: 30px;
        }

        .payslip-header h2 {
            text-transform: uppercase;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        .payslip-header h3 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }

        .payslip-details {
            margin-bottom: 30px;
        }

        .payslip-details .row div {
            font-size: 14px;
        }

        .table th,
        .table td {
            font-size: 14px;
            vertical-align: middle;
        }

        .table td:first-child {
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
        }

        .authorized-signatory {
            text-align: right;
            font-size: 14px;
        }

        .print-btn {
            text-align: center;
            margin-top: 20px;
        }
    </style>

    <div class="payslip-container">
        <div class="payslip-header">
            <h2>Payslip for the Month of <?= DateTime::createFromFormat("!m", $month)->format("F") ?>     <?= $year ?></h2>
            <h3>Payslip No.: <?= $payroll_id ?></h3>
        </div>
        <div class="payslip-details">
            <div class="row">
                <div class="col-md-6">
                    <p><?= $company_row['company_name'] ?><br>
                        <?= $company_row['company_registered_address'] ?><br>
                        <a href="mailto:<?= $company_row['email'] ?>"><?= $company_row['email'] ?></a>
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Payment Date:<br><?= $custom_datetime ?></p>
                    <p>Payment
                        To:<br><strong><?= $employee_row['first_name'] . " " . $employee_row['last_name'] ?></strong><br>
                        <?= $employee_row['address'] ?>
                        <br>
                    </p>
                </div>
            </div>
        </div>
        <div class="payslip-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Earnings</h4>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>Basic</td>
                                <td class="text-end">
                                    <?= $_SESSION['currency_code'] . " " . $employee_row['basic_salary'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>HRA</td>
                                <td class="text-end">
                                    <?= $_SESSION['currency_code'] . " " . $employee_row['allowance'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>DA</td>
                                <td class="text-end"><?= $_SESSION['currency_code'] . " " . $employee_row['ot_pay'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Special Allowance</td>
                                <td class="text-end"><?= $_SESSION['currency_code'] . " " . $employee_row['bonus'] ?>
                                </td>
                            </tr>
                            <tr class="total-row">
                                <td>Total Earnings</td>
                                <td class="text-end">
                                    <?= $_SESSION['currency_code'] . " " . $total_earning ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>Deductions</h4>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>EPF</td>
                                <td class="text-end">
                                    <?= $_SESSION['currency_code'] . " " . $employee_row['epf_8'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>APPIT</td>
                                <td class="text-end">
                                    <?= $_SESSION['currency_code'] . " " . $employee_row['appit'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Loans</td>
                                <td class="text-end">
                                    <?= $_SESSION['currency_code'] . " " . $employee_row['loan'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Other</td>
                                <td class="text-end">
                                    <?= $_SESSION['currency_code'] . " " . $employee_row['other_deductions'] ?>
                                </td>
                            </tr>
                            <tr class="total-row">
                                <td>Total Deductions</td>
                                <td class="text-end">
                                    <?= $_SESSION['currency_code'] . " " . $total_deduction ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h4>Net Pay : <?= $_SESSION['currency_code'] . " " . $total_earning - $total_deduction ?></h4>
                </div>
                <div class="col-md-6 text-end authorized-signatory">
                    <p>For <?= $company_row['company_name'] ?><br>Authorized Signatory</p>
                </div>
            </div>
        </div>
        <div class="print-btn">
            <a class="btn btn-primary"
                href="../../backend/data/generate-payslip.php?year=<?= $year ?>&month=<?= $month ?>&id=<?= $employee_id ?>&payroll_id=<?= $payroll_id ?>">Print
                this receipt</a>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // title
            $(document).prop('title', 'Payslip');
            // navbar
            $("#dashboard").removeClass("active");
            $("#payrolls").addClass("active");
        })
    </script>

    <?php
}
?>