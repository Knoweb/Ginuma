<?php
session_start();
require_once '../connection/conn.php';
include("../includes/Functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $income_id = (int)$_POST['id'];
    $company_id = $_SESSION['company_id'];
    
    $db = new DBConnection();
    $conn = $db->conn;
    $conn->begin_transaction();

    try {
        // Fetch income details
        $sql = "SELECT it.amount, it.sub_account_id, tlt.credit_account, tlt.sub_account_id AS transfer_sub_account_id
                FROM income_tbl it
                LEFT JOIN transaction_log_tbl tlt
                ON it.sub_account_id = tlt.debit_account
                WHERE it.income_id = ? AND it.company_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $income_id, $company_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Income record not found.");
        }

        $income = $result->fetch_assoc();
        $amount = $income['amount'];
        $income_sub_account_id = $income['sub_account_id'];
        $credit_acc_id = $income['credit_account'];
        $transfer_sub_account_id = $income['transfer_sub_account_id'];

        // Reverse balances
        $sql = "UPDATE company_sub_account_balance 
                SET balance = balance - ? 
                WHERE company_id = ? AND sub_account_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $amount, $company_id, $income_sub_account_id);
        $stmt->execute();

        $sql = "UPDATE company_sub_account_balance 
                SET balance = balance + ? 
                WHERE company_id = ? AND sub_account_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $amount, $company_id, $transfer_sub_account_id);
        $stmt->execute();

        // Delete income record
        $sql = "DELETE FROM income_tbl WHERE income_id = ? AND company_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $income_id, $company_id);
        $stmt->execute();

        $description = "Deleted income ID $income_id";
        makeTransactionLog($conn, $company_id, $income_sub_account_id, $credit_acc_id, $transfer_sub_account_id, "Income Deletion", -$amount, $description);

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Income record deleted successfully."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
