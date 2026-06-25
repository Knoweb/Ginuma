<?php
session_start();
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

function getMainAccountDetails(mysqli $conn, int $sub_account_id): array
{
    $sql = "SELECT account_name FROM account_tbl at INNER JOIN sub_account_tbl sat ON (at.account_id=sat.account_id) WHERE sat.sub_account_id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare statement");
    }
    $stmt->bind_param("i", $sub_account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result->fetch_assoc();
}

function updateSubAccountBalance(mysqli $conn, int $sub_account_id, float $amount, int $company_id): bool
{
    $sql = "UPDATE company_sub_account_balance SET balance = balance + ? WHERE company_id = ? AND sub_account_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare update statement");
    }
    $stmt->bind_param("dii", $amount, $company_id, $sub_account_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to execute update: " . $stmt->error);
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transaction_id'])) {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $conn->begin_transaction();
    try {
        $transaction_id = $_POST['transaction_id'];
        $company_id = $_SESSION['company_id'];

        // Fetch the transaction details
        $sql = "SELECT credit_account, debit_account, amount FROM transaction_log_tbl WHERE transaction_id = ? AND company_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $transaction_id, $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Transaction not found");
        }
        $row = $result->fetch_assoc();
        $credit_account_id = $row['credit_account'];
        $debit_account_id = $row['debit_account'];
        $amount = $row['amount'];

        // Retrieve account details
        $credit_account_details = getMainAccountDetails($conn, $credit_account_id);
        $debit_account_details = getMainAccountDetails($conn, $debit_account_id);


        // echo json_encode(['status' => 'success', 'debit_account_details' => $debit_account_details, 'credit_account_details' => $credit_account_details]);
        // echo json_encode(['status' => 'success', 'debit_acc_name' => $debit_account_details['account_name'], 'credit_acc_name' => $credit_account_details['account_name']]);

        // Reverse the accounts: treat credit as debit and debit as credit
        // Reverse credit account effect (now treated as debit)
        switch ($credit_account_details['account_name']) {
            case 'Assets':
                if (!updateSubAccountBalance($conn, $credit_account_id, $amount, $company_id)) {
                    throw new Exception("Failed to update asset account balance.");
                }
                break;
            case 'Liabilities':
                if (!updateSubAccountBalance($conn, $credit_account_id, -$amount, $company_id)) {
                    throw new Exception("Failed to update liability account balance.");
                }
                break;
            case 'Equity':
                if (!updateSubAccountBalance($conn, $credit_account_id, -$amount, $company_id)) {
                    throw new Exception("Failed to update equity account balance.");
                }
                break;
            case 'Income':
                if (!updateSubAccountBalance($conn, $credit_account_id, -$amount, $company_id)) {
                    throw new Exception("Failed to update income account balance.");
                }
                break;
            case 'Expenses':
                if (!updateSubAccountBalance($conn, $credit_account_id, $amount, $company_id)) { // Expenses increase, so amount is added back
                    throw new Exception("Failed to update expense account balance.");
                }
                break;
            default:
                throw new Exception("Unknown account type for credit account.");
        }

        // Reverse debit account effect (now treated as credit)
        switch ($debit_account_details['account_name']) {
            case 'Assets':
                if (!updateSubAccountBalance($conn, $debit_account_id, $amount, $company_id)) { // Reversing an asset increases its balance
                    throw new Exception("Failed to update asset account balance.");
                }
                break;
            case 'Liabilities':
                if (!updateSubAccountBalance($conn, $debit_account_id, $amount, $company_id)) { // Reversing a liability increases its balance
                    throw new Exception("Failed to update liability account balance.");
                }
                break;
            case 'Equity':
                if (!updateSubAccountBalance($conn, $debit_account_id, $amount, $company_id)) { // Reversing equity increases its balance
                    throw new Exception("Failed to update equity account balance.");
                }
                break;
            case 'Income':
                if (!updateSubAccountBalance($conn, $debit_account_id, $amount, $company_id)) { // Income accounts need to increase
                    throw new Exception("Failed to update income account balance.");
                }
                break;
            case 'Expenses':
                if (!updateSubAccountBalance($conn, $debit_account_id, -$amount, $company_id)) { // Reversing expenses decreases the balance
                    throw new Exception("Failed to update expense account balance.");
                }
                break;
            default:
                throw new Exception("Unknown account type for debit account.");
        }

        // Delete the transaction log
        $sql = "DELETE FROM transaction_log_tbl WHERE transaction_id = ? AND company_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $transaction_id, $company_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete transaction log: " . $stmt->error);
        }

        // Commit the transaction
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Transaction reverted successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}
