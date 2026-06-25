<?php
// enable all the session for this php file
session_start();

require_once "../connection/conn.php";      // include the database connection class
require_once "../includes/Functions.php";   // include some functions for this program

// creating the database connection object
$db = new DBConnection();
$conn = $db->conn;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // begin the transaction
    $conn->begin_transaction();
    try {
        // getting user inputs from the frontend
        if (!isset($_SESSION['company_id'])) {
            throw new Exception("Company ID is not set in the session!");
        }
        $company_id = $_SESSION['company_id'];                  // Retrieve company ID from session

        // Retrieve data from the form
        $transaction_dates = $_POST['dates'];                   // Retrieve all the transaction dates from the frontend
        $transaction_descriptions = $_POST['descriptions'];     // Retrieve all the transaction descriprions
        $sub_account_ids = $_POST['sub_account_ids'];           // Retrieve all the sub account ids
        $dOrWs = $_POST['dOrWs'];                               // Retrieve if all the transaction were deposit or withdrawal
        $amounts = $_POST['amounts'];                           // Retrieve all the transaction amounts
        $balances = $_POST['balances'];                         // Retrieve the balances

        // get the bank account id from the hidden field in the frontend
        $bank_id = $_POST['bank_id'];

        // check for each field has some invalid characters
        for ($i = 0; $i < count($transaction_dates); $i++) {
            // get the single value from the arrays
            $transaction_date = $transaction_dates[$i];
            $transaction_description = $transaction_descriptions[$i];
            $sub_account_id = $sub_account_ids[$i];
            $dOrW = $dOrWs[$i];
            $amount = $amounts[$i];
            $balance = $balances[$i];

            // check if the transaction date is valid or not
            $d = DateTime::createFromFormat('Y-m-d', $transaction_date);
            if (!$d || $d->format('Y-m-d') !== $transaction_date) {
                throw new Exception("Invalid transaction date format. Please use 'YYYY-MM-DD'.");
            }

            // check if the transaction description has some invalid characters
            if (filter_var($transaction_description, FILTER_SANITIZE_STRING) != $transaction_description) {
                throw new Exception("Invalid characters in the transaction description!");
            }

            // check if the sub account id is a number
            if (!is_numeric($sub_account_id)) {
                throw new Exception("Invalid sub account ID!");
            }

            // check if the transaction type is either deposit or withdrawal
            if ($dOrW != 'Deposit' && $dOrW != 'Withdrawal') {
                throw new Exception("Invalid transaction type!");
            }

            // check if the amount is a number and greater than zero
            if (!is_numeric($amount) || $amount <= 0) {
                throw new Exception("Invalid amount!");
            }

            // check if the balance is a number
            if (!is_numeric($balance)) {
                throw new Exception("Invalid balance!");
            }

            // save all the transaction details in the bank_statement_tbl
            $sql = "INSERT INTO bank_statement_tbl (transaction_date, transaction_description, sub_account_id, d_or_w, amount, balance, company_id, bank_id) VALUES (?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisddii", $transaction_date, $transaction_description, $sub_account_id, $dOrW, $amount, $balance, $company_id, $bank_id);
            $stmt->execute();
            $stmt->close();
        }

        // Commit the transaction
        $conn->commit();
        header("Location: ../../pages/Company/Company.php?page=add-bank-statement&success=Transaction added successfully!");
        exit;
    } catch (Exception $e) {
        // Rollback the transaction if something went wrong
        $conn->rollback();
        $em = "An error occurred: " . $e->getMessage();
        header("Location: ../../pages/Company/Company.php?page=add-bank-statement&error=$em");
        exit;
    }
}
