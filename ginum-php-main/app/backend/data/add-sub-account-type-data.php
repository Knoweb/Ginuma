<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Import necessary files
    require_once '../includes/Functions.php';
    require_once '../connection/conn.php';

    // Establish a database connection
    $db = new DBConnection();
    $conn = $db->conn;

    $conn->begin_transaction();
    try {
        // Get user inputs from the frontend
        $account_id = $_POST['acc_id'];
        $sub_account_id = $_POST['sub_account_id'];
        $sub_account_type_names = $_POST['sub_account_type_names'];
        $sub_account_type_amounts = $_POST['sub_account_type_amounts'];

        // Validate and sanitize user inputs
        if (empty($sub_account_id)) {
            throw new Exception("Sub Account ID is required");
        }
        for ($i = 0; $i < count($sub_account_type_names); $i++) {
            $sub_account_type_names[$i] = htmlspecialchars($sub_account_type_names[$i], ENT_QUOTES, 'UTF-8');
            if (empty($sub_account_type_names[$i])) {
                throw new Exception("Sub Account Type Name is required");
            }
            if (empty($sub_account_type_amounts[$i]) || !is_numeric($sub_account_type_amounts[$i])) {
                throw new Exception("Sub Account Type Amount is required and must be a valid number");
            }
        }

        // Determine the transaction type based on account ID
        $transaction_type_map = [
            1 => "Assets Addition",
            2 => "Liabilities Addition",
            3 => "Equity Addition",
            4 => "Income Addition",
            5 => "Expenses Addition",
        ];
        $transaction_type = $transaction_type_map[$account_id] ?? "Unknown Transaction Type";

        // Insert data into the database
        for ($i = 0; $i < count($sub_account_type_names); $i++) {
            $sql = "INSERT INTO sub_account_type_tbl (sub_account_id, type_name, company_id, initial_value, date_added) VALUES (?, ?, ?, ?, now())";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("isid", $sub_account_id, $sub_account_type_names[$i], $_SESSION['company_id'], $sub_account_type_amounts[$i]);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Failed to prepare the SQL statement.");
            }

            // Get the last inserted sub account type ID
            $last_id = $conn->insert_id;

            // Insert into company_sub_account_balance
            $sql = "INSERT INTO company_sub_account_balance (company_id, sub_account_type_id, balance) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("iid", $_SESSION['company_id'], $last_id, $sub_account_type_amounts[$i]);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Failed to prepare the SQL statement.");
            }

            // Log the transaction
            makeTransactionLog($conn, $_SESSION['company_id'], $account_id, $sub_account_id, $transaction_type, $sub_account_type_amounts[$i], "Initial Balance");
        }

        // Commit the transaction
        $conn->commit();

        // Send success message
        $sm = "Sub Account added successfully!";
        header("Location:../../pages/Company/Company.php?page=add-sub-account&success=$sm");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction and send error message
        $conn->rollback();
        $em = "Error: " . $e->getMessage();
        header("Location:../../pages/Company/Company.php?page=add-sub-account&error=$em");
        exit();
    }
}
