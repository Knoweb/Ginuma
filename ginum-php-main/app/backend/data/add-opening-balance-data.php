<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // import the DBConnection class into the program
    require_once '../connection/conn.php';
    // create a new instance of the DBConnection class to connect to the database
    $db = new DBConnection();
    // get the connection object from the DBConnection class
    $conn = $db->conn;

    // call the company id from session variables
    $company_id = $_SESSION['company_id'];

    // begin the transaction
    $conn->begin_transaction();
    try {
        // get user inputs from the frontend
        $acc_id = $_POST['acc_id'];
        $sub_account_id = $_POST['sub_account_id'];
        $amount = $_POST['amount'];

        // sanitize and validate the money amount
        if (!is_numeric($amount)) {
            throw new Exception("Invalid amount!");
        }

        // check if the sub account is already in the company sub account balance
        $sql = "SELECT * FROM company_sub_account_balance WHERE sub_account_id=? AND company_id=?";
        // prepare a statement to execute the query
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Could not prepare statement: " . $conn->error);
        }
        // bind the parameters into the statement
        $stmt->bind_param("ii", $sub_account_id, $company_id);
        // execute the statement
        $stmt->execute();
        // get the result
        $result = $stmt->get_result();
        // check if the sub account is in the balance table
        if ($result->num_rows > 0) {
            // update the account balance in the balance table
            $sql = "UPDATE company_sub_account_balance SET balance=balance+? WHERE sub_account_id=? AND company_id=?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Could not prepare statement: " . $conn->error);
            }
            // bind the parameters to the statement
            $stmt->bind_param("dii", $amount, $sub_account_id, $company_id);
            // execute the statement
            $stmt->execute();
        } else {
            // add the sub account into the company_sub_account_balance table
            $sql = "INSERT INTO company_sub_account_balance (sub_account_id, company_id, balance) VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Could not prepare statement: " . $conn->error);
            }
            // bind the parameters to the statement
            $stmt->bind_param("iid", $sub_account_id, $company_id, $amount);
            // execute the statement
            $stmt->execute();
        }
        // commit the transaction
        $conn->commit();
        $sm = "Opening Balance added successfully!";
        header("Location:../../pages/Company/Company.php?page=add-opening-balance&success=$sm");

    } catch (Exception $e) {
        $em = $e->getMessage();
        echo $em;
    }
}

?>