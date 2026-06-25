<?php
// start the sessions in the background
session_start();

// check the request method is post or not
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // import the DBConnection class into the program
    require_once '../connection/conn.php';
    // create a new instance of the DBConnection class to connect to the database
    $db = new DBConnection();
    // get the connection object from the DBConnection class
    $conn = $db->conn;
//
    // begin the transaction
    $conn->begin_transaction();
    try {
        // get the user inputs from the frontend
        $bank_name = $_POST['bank_name'];                       // get the bank name
        $branch = $_POST['branch'];                             // get the branch name
        $account_number = $_POST['account_number'];             // get the account number
        $account_name = $_POST['account_name'];                 // get the account name
        $opening_balance = (float) $_POST['opening_balance'];   // get the opening balance of the bank account

        // sanitize and validate all the user inputs
        // check the bank name has invalid characters
        if (filter_var($bank_name, FILTER_SANITIZE_STRING) != $bank_name) {
            // throw an exception if the bank name has invalid characters
            throw new Exception("Invalid characters in the bank name!");
        }
        // check the branch name has invalid characters
        if (filter_var($branch, FILTER_SANITIZE_STRING) != $branch) {
            // throw an exception if the branch name has invalid characters
            throw new Exception("Invalid characters in the branch name!");
        }
        // check the account name has invalid characters
        if (filter_var($account_name, FILTER_SANITIZE_STRING) != $account_name) {
            // throw an exception if the account name has invalid characters
            throw new Exception("Invalid characters in the account name!");
        }
        // check the opening balance is a valid number and also not equal to zero
        if (!is_numeric($opening_balance) || $opening_balance <= 0) {
            // throw an exception if the opening balance is not a number or is less than or equal to zero
            throw new Exception("Invalid opening balance or opening balance is not a number!");
        }

        // sanitizing and validating is finished
        // time to insert the bank account into the database
        // before insert the account, check the bank account is already in the database
        $sql = "SELECT * FROM bank_details_tbl WHERE account_number=? AND company_id=?";
        // prepare the statement for select the bank account details from the database
        $stmt = $conn->prepare($sql);
        // bind the parameters into the prepared statement
        $stmt->bind_param("si", $account_number, $_SESSION['company_id']);
        // execute the prepared statement
        $stmt->execute();
        // get the results from the database
        $result = $stmt->get_result();
        // if the result has any rows, it means the bank account already exists in the database
        if ($result->num_rows > 0) {
            // throw an exception if the result is not empty
            throw new Exception("Bank account already exists!");
        }
        // close the statement
        $stmt->close();

        // insert the new bank details into the tables
        // insert the data into bank_details_tbl
        $sql = "INSERT INTO bank_details_tbl (bank_name, branch, account_number, account_name, company_id, status) VALUES (?,?,?,?,?,?)";
        $status = 1;
        // prepare the statement to insert the bank details
        $stmt = $conn->prepare($sql);
        // bind the parameters into the prepared statement
        $stmt->bind_param("ssssii", $bank_name, $branch, $account_number, $account_name, $_SESSION['company_id'], $status);
        // execute the statement
        $stmt->execute();
        // close the statement
        $stmt->close();

        // get the last inserted bank details id from the bank_details_tbl
        $last_bank_details_id = $conn->insert_id;

        // hardcode the sub account id to the bank account details
        $saving_acc_id = 77;

        // insert the initial account balance into the company_sub_account_balance table
        $sql = "INSERT INTO company_sub_account_balance (company_id, sub_account_id, balance) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        // bind the parameters into the prepared statement
        $stmt->bind_param("iid", $_SESSION['company_id'], $saving_acc_id, $opening_balance);
        // execute the statement
        $stmt->execute();
        // close the statement
        $stmt->close();

        // get the last inserted company_sub_account_balance id
        $last_company_sub_account_balance_id = $conn->insert_id;

        // insert the data into bank_account_tbl
        $sql = "INSERT INTO bank_account_tbl (bank_details_id, company_id, company_sub_account_balance_id) VALUES (?,?,?)";
        // prepare the statement to insert the bank account details
        $stmt = $conn->prepare($sql);
        // bind the parameters into the prepared statement
        $stmt->bind_param("iii", $last_bank_details_id, $_SESSION['company_id'], $last_company_sub_account_balance_id);
        // execute the statement
        $stmt->execute();
        // close the statement
        $stmt->close();

        // balance the double entry by updating the balance of the Retained Earnings account
        // first of all, check if the Retained Earnings account has already existed in the company_sub_account_balance table
        $retained_earning_id = 23;
        $sql = "SELECT * FROM company_sub_account_balance WHERE sub_account_id=? AND company_id=?";
        // prepare the statement for check the account
        $stmt = $conn->prepare($sql);
        // bind the parameters into the statement
        $stmt->bind_param("ii", $retained_earning_id, $_SESSION['company_id']);
        // execute the statement
        $stmt->execute();
        // get the result
        $result = $stmt->get_result();
        // check the retained earnings account is exists in the company_sub_account_balance table
        if ($result->num_rows > 0) {
            // update the retained earnings account balance with the new account balance
            $sql = "UPDATE company_sub_account_balance SET balance = balance + ? WHERE company_id=? AND sub_account_id=?";
            // prepare the statement for update the retained earnings account balance
            $stmt = $conn->prepare($sql);
            // bind the parameters into the statement
            $stmt->bind_param("dii", $opening_balance, $_SESSION['company_id'], $retained_earning_id);
            // execute the statement
            $stmt->execute();
            // close the statement
            $stmt->close();
        } else {
            // insert the initial account balance into the company_sub_account_balance table for the Retained Earnings account
            $sql = "INSERT INTO company_sub_account_balance (company_id, sub_account_id, balance) VALUES (?,?,?)";
            // prepare the statement for inserting the account balance into the company_sub_account_balance table
            $stmt = $conn->prepare($sql);
            // bind the parameters into the statement
            $stmt->bind_param("iid", $_SESSION['company_id'], $retained_earning_id, $opening_balance);
            // execute the statement
            $stmt->execute();
            // close the statement
            $stmt->close();
        }

        // if all the data is inserted successfully, commit the transaction
        $conn->commit();
        $sm = "Bank account added successfully!";
        header("Location:../../pages/Company/Company.php?page=add-bank-account&success=$sm");
        exit();

    } catch (Exception $e) {
        // if any error occurs, rollback the transaction and display the error message
        $conn->rollback();
        $em = "Error: " . $e->getMessage();
        header("Location: ../../pages/Company/Company.php?page=add-bank-account&error=$em");
        exit();
    }
}
?>