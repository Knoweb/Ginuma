<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once ("../includes/Functions.php");
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $debit_account_ids = $_POST['debit_acc_ids'];
    $credit_account_ids = $_POST['credit_acc_ids'];
    $amounts = $_POST['amounts'];
    $descriptions = $_POST['descriptions'];
    $company_id = $_SESSION['company_id'];

    $conn->begin_transaction();
    try {
        for ($i = 0; $i < count($debit_account_ids); $i++) {
            $debit_account_id = $debit_account_ids[$i];
            $credit_account_id = $credit_account_ids[$i];
            $amount = $amounts[$i];
            $description = filter_var($descriptions[$i], FILTER_SANITIZE_STRING);

            if (!validateMoneyAmount($amount)) {
                throw new Exception("Invalid characters in the amount field!");
            }

            if ($description != $descriptions[$i]) {
                throw new Exception("Invalid characters in the description field!");
            }

            $sql = "SELECT at.account_id, sat.sub_account_id, at.account_name 
                    FROM account_tbl at 
                    INNER JOIN sub_account_tbl sat ON at.account_id=sat.account_id 
                    WHERE sat.sub_account_id=?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Could not prepare statement");
            }

            $stmt->bind_param("i", $debit_account_id);
            $stmt->execute();
            $debit_acc_result = $stmt->get_result();
            $row1 = $debit_acc_result->fetch_assoc();
            $debit_main_account_id = $row1['account_id'];
            $debit_sub_account_id = $row1['sub_account_id'];
            $debit_acc_type = $row1['account_name'];
            $stmt->close();

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Could not prepare statement");
            }
            $stmt->bind_param("i", $credit_account_id);
            $stmt->execute();
            $credit_acc_result = $stmt->get_result();
            $row2 = $credit_acc_result->fetch_assoc();
            $credit_main_account_id = $row2['account_id'];
            $credit_sub_account_id = $row2['sub_account_id'];
            $credit_acc_type = $row2['account_name'];
            $stmt->close();

            // Update debit account balance
            if ($debit_acc_type == 'Assets' || $debit_acc_type == 'Expenses') {
                updateAccountBalance($conn, $debit_sub_account_id, $amount, $company_id);
            } elseif ($debit_acc_type == 'Liabilities' || $debit_acc_type == 'Equity' || $debit_acc_type == 'Income') {
                updateAccountBalance($conn, $debit_sub_account_id, -$amount, $company_id);
            } else {
                throw new Exception("Invalid debit account type");
            }

            // Update credit account balance
            if ($credit_acc_type == 'Assets' || $credit_acc_type == 'Expenses') {
                updateAccountBalance($conn, $credit_sub_account_id, -$amount, $company_id);
            } elseif ($credit_acc_type == 'Liabilities' || $credit_acc_type == 'Equity' || $credit_acc_type == 'Income') {
                updateAccountBalance($conn, $credit_sub_account_id, $amount, $company_id);
            } else {
                throw new Exception("Invalid credit account type");
            }

            // Log transactions
            $debit_transaction_type = determineTransactionType($debit_acc_type, 'debit');
            $credit_transaction_type = determineTransactionType($credit_acc_type, 'credit');

            makeTransactionLog($conn, $company_id, $debit_main_account_id, $debit_sub_account_id, $debit_transaction_type, $amount, $description);
            makeTransactionLog($conn, $company_id, $credit_main_account_id, $credit_sub_account_id, $credit_transaction_type, $amount, $description);
        }

        $conn->commit();
        header("Location:../../pages/Company/Company.php?page=show-accounts&success=Transaction recorded successfully");
    } catch (Exception $e) {
        $conn->rollback();
        $em = "Error: " . $e->getMessage();
        header("Location:../../pages/Company/Company.php?page=show-accounts&error=$em");
        exit();
    }
}

function updateAccountBalance($conn, $sub_account_id, $amount, $company_id)
{
    $sql = "SELECT company_sub_account_balance_id 
            FROM company_sub_account_balance 
            WHERE sub_account_id = ? AND company_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare statement: " . $conn->error);
    }
    $stmt->bind_param("ii", $sub_account_id, $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();

    if ($exists) {
        $sql = "UPDATE company_sub_account_balance SET balance = balance + ? WHERE sub_account_id = ? AND company_id=?";
    } else {
        $sql = "INSERT INTO company_sub_account_balance (sub_account_id, balance, company_id) VALUES (?, ?, ?)";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare statement: " . $conn->error);
    }
    if ($exists) {
        $stmt->bind_param("dii", $amount, $sub_account_id, $company_id);
    } else {
        $stmt->bind_param("idi", $sub_account_id, $amount, $company_id);
    }
    $stmt->execute();
    $stmt->close();
}

function determineTransactionType($account_type, $entry_type)
{
    if ($entry_type == 'debit') {
        if ($account_type == 'Assets' || $account_type == 'Expenses') {
            return 'Addition';
        } elseif ($account_type == 'Liabilities' || $account_type == 'Equity' || $account_type == 'Income') {
            return 'Reduction';
        }
    } elseif ($entry_type == 'credit') {
        if ($account_type == 'Assets' || $account_type == 'Expenses') {
            return 'Reduction';
        } elseif ($account_type == 'Liabilities' || $account_type == 'Equity' || $account_type == 'Income') {
            return 'Addition';
        }
    }
    return 'Unknown Transaction Type';
}
?>