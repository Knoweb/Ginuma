<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $conn->begin_transaction();
    try {
        // Get user inputs
        $month = $_POST['month'];
        $year = $_POST['year'];
        $employee_no = $_POST['emp_no'];
        $basic_salary = (float) $_POST['basic_salary'];
        $allowance = (float) $_POST['allowance'] ?: 0;
        $ot_pay = $_POST['ot_pay'] ? (float) $_POST['ot_pay'] : 0;
        $bonus = $_POST['bonus'] ? (float) $_POST['bonus'] : 0;
        $appit = $_POST['appit'] ? (float) $_POST['appit'] : 0;
        $loans = $_POST['loans'] ? (float) $_POST['loans'] : 0;
        $other = $_POST['other'] ? (float) $_POST['other'] : 0;
        $epf_8 = $_POST['epf_cal_8'] ? (float) $_POST['epf_cal_8'] : 0;
        $epf_12 = $_POST['epf_cal_12'] ? (float) $_POST['epf_cal_12'] : 0;
        $etf_3 = $_POST['etf_cal_3'] ? (float) $_POST['etf_cal_3'] : 0;

        // Validate inputs
        if (empty($_POST['month'])) {
            throw new Exception('Enter a valid month');
        }
        if (empty($basic_salary) || !is_numeric($basic_salary)) {
            throw new Exception('Enter a valid basic salary');
        }
        if (!is_numeric($allowance) || !is_numeric($ot_pay) || !is_numeric($bonus) || !is_numeric($appit) || !is_numeric($loans) || !is_numeric($other)) {
            throw new Exception('Invalid value!');
        }

        // Insert payroll details into the database
        $sql = "INSERT INTO payroll_tbl (company_id, employee_id, month, year, basic_salary, allowance, ot_pay, bonus, appit, loan, other_deductions, epf_8, epf_12, etf_3) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiidddddddddd", $_SESSION['company_id'], $employee_no, $month, $year, $basic_salary, $allowance, $ot_pay, $bonus, $appit, $loans, $other, $epf_8, $epf_12, $etf_3);
        $stmt->execute();
        $stmt->close();

        // Retrieve sub-account IDs for payable and expense accounts
        $payables = [
            'Salary' => [],
            'Salary Payable' => [],
            'EPF Payable' => [],
            'ETF Payable' => [],
            'APPIT Payable' => [],
            'EPF' => [],
            'ETF' => [],
            'APPIT' => []
        ];

        foreach ($payables as $name => &$payable) {
            $sql = "SELECT sat.sub_account_id, sat.account_id 
                    FROM sub_account_tbl sat 
                    INNER JOIN account_tbl at ON sat.account_id = at.account_id 
                    WHERE sat.sub_account_name=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $payable['sub_account_id'] = $row['sub_account_id'];
                $payable['account_id'] = $row['account_id'];
            } else {
                // Debugging output
                throw new Exception("No sub-account found for: $name");
            }
            $stmt->close();
        }

        // Function to update balance
        function updateBalance(mysqli $conn, float $amount, int $sub_account_id, int $company_id, string $description = ''): void
        {
            $sql = "UPDATE company_sub_account_balance SET balance = balance + ? WHERE sub_account_id = ? AND company_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("dii", $amount, $sub_account_id, $company_id);
            if (!$stmt->execute()) {
                throw new Exception("Error updating $description balance $stmt->error");
            }
            $stmt->close();
        }

        // Check and insert sub-account IDs into company_sub_account_balance if not exists
        foreach ($payables as $payable) {
            if (isset($payable['sub_account_id'])) {
                $sub_account_id = $payable['sub_account_id'];
                $sql = "SELECT COUNT(*) as count FROM company_sub_account_balance WHERE sub_account_id = ? AND company_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $sub_account_id, $_SESSION['company_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row['count'] == 0) {
                    $insert_sql = "INSERT INTO company_sub_account_balance (sub_account_id, balance, company_id) VALUES (?, 0, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param("ii", $sub_account_id, $_SESSION['company_id']);
                    $insert_stmt->execute();
                }
                $stmt->close();
            }
        }

        // Accounting Entries for Payable Accounts
        $b = $epf_12;
        if (isset($payables['EPF Payable']['sub_account_id'])) {
            updateBalance($conn, $b, $payables['EPF Payable']['sub_account_id'], $_SESSION['company_id'], 'EPF Payable');
        }

        if (isset($payables['ETF Payable']['sub_account_id'])) {
            updateBalance($conn, $etf_3, $payables['ETF Payable']['sub_account_id'], $_SESSION['company_id'], 'ETF Payable');
        }

        if (isset($payables['APPIT Payable']['sub_account_id'])) {
            updateBalance($conn, $appit, $payables['APPIT Payable']['sub_account_id'], $_SESSION['company_id'], 'APPIT Payable');
        }

        $net_salary = $basic_salary + $allowance + $ot_pay + $bonus - ($appit + $loans + $other);
        if (isset($payables['Salary Payable']['sub_account_id'])) {
            updateBalance($conn, $net_salary, $payables['Salary Payable']['sub_account_id'], $_SESSION['company_id'], 'Salary Payable');
        }

        // Update the expenses
        if (isset($payables['EPF']['sub_account_id'])) {
            updateBalance($conn, $b, $payables['EPF']['sub_account_id'], $_SESSION['company_id'], 'EPF');
        }

        if (isset($payables['ETF']['sub_account_id'])) {
            updateBalance($conn, $etf_3, $payables['ETF']['sub_account_id'], $_SESSION['company_id'], 'ETF');
        }

        if (isset($payables['APPIT']['sub_account_id'])) {
            updateBalance($conn, $appit, $payables['APPIT']['sub_account_id'], $_SESSION['company_id'], 'APPIT');
        }

        if (isset($payables['Salary']['sub_account_id'])) {
            updateBalance($conn, $net_salary, $payables['Salary']['sub_account_id'], $_SESSION['company_id'], 'Salary');
        }

        // Commit the transaction
        $conn->commit();
        $sm = "Payroll and transactions have been recorded successfully";
        header("Location: ../../pages/Company/Company.php?page=employee-payroll&success=$sm");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $em = "Error" . $e->getMessage() . "<br> In Line: " . $e->getLine();
        header("Location: ../../pages/Company/Company.php?page=employee-payroll&error=$em");
        exit;
    }
}
