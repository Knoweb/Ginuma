<?php
session_start();
require_once '../connection/conn.php';
include("../includes/Functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $expense_id = (int)$_POST['id'];
    $company_id = $_SESSION['company_id'];

    $conn = new DBConnection();
    $db = $conn->conn;
    $db->begin_transaction();

    try {
        // Fetch expense details
        $sql = "SELECT et.amount, et.sub_account_id, tlt.debit_account, tlt.credit_account
                FROM expense_tbl et
                LEFT JOIN transaction_log_tbl tlt
                ON et.sub_account_id = tlt.credit_account
                WHERE et.expense_id = ? AND et.company_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $expense_id, $company_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Expense record not found.");
        }

        $expense = $result->fetch_assoc();
        $amount = $expense['amount'];
        $expense_sub_account_id = $expense['sub_account_id'];
        $debit_account = $expense['debit_account'];
        $credit_account = $expense['credit_account'];

        // Reverse balances
        $sql = "UPDATE company_sub_account_balance 
                SET balance = balance - ? 
                WHERE company_id = ? AND sub_account_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("dii", $amount, $company_id, $expense_sub_account_id);
        $stmt->execute();

        $sql = "UPDATE company_sub_account_balance 
                SET balance = balance + ? 
                WHERE company_id = ? AND sub_account_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("dii", $amount, $company_id, $credit_account);
        $stmt->execute();

        // Delete expense record
        $sql = "DELETE FROM expense_tbl WHERE expense_id = ? AND company_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $expense_id, $company_id);
        $stmt->execute();

        // Log the reverse transaction
        $description = "Deleted expense ID $expense_id";
        if (!makeTransactionLog($db, $company_id, $debit_account, $credit_account, $expense_sub_account_id, "Expense Deletion", -$amount, $description)) {
            throw new Exception("Transaction logging failed.");
        }

        $db->commit();
        echo json_encode(["success" => true, "message" => "Expense record deleted successfully."]);
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
