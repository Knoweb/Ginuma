<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    include_once("../includes/Functions.php");
    $account_id = $_POST['acc_id'];
    $sub_account_name = $_POST['sub_acc_name'];
    $amount = $account_id >= 4 ? 0 : $_POST['amount'];
    $sub_account_type = $account_id >= 3 ? null : $_POST['sub_account_type'];

    // Validating and sanitizing user inputs
    $sub_account_name = filter_var($sub_account_name, FILTER_SANITIZE_STRING);

    // Checking if the user has entered unwanted inputs
    if ($_POST['sub_acc_name'] != $sub_account_name) {
        $wm = "Invalid characters in the Account Type field";
        header("Location:../../pages/Company/Company.php?page=add-account&warning=$wm");
        exit();
    }

    if ($account_id <= 3 && !validateMoneyAmount($amount)) {
        $wm = "Invalid characters in the Amount field";
        header("Location:../../pages/Company/Company.php?page=add-account&warning=$wm");
        exit();
    }

    if ($account_id <= 2 && ($sub_account_type == null || $sub_account_type == "")) {
        $wm = "Invalid sub account type";
        header("Location:../../pages/Company/Company.php?page=add-account&warning=$wm");
        exit();
    }

    // Initializing the transaction object
    $conn->begin_transaction();

    try {
        // Check if the sub account already exists in sub_account_tbl
        $sql = "SELECT * FROM sub_account_tbl WHERE sub_account_name LIKE ?";
        $stmt = $conn->prepare($sql);
        $test_query = "%$sub_account_name%";
        $stmt->bind_param("s", $test_query);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $wm = "Sub Account already exists";
            header("Location:../../pages/Company/Company.php?page=add-account&warning=$wm");
            exit();
        } else {
            // Insert the new sub account into sub_account_tbl
            $sql = "INSERT INTO sub_account_tbl (sub_account_name, account_id, account_type) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sis", $sub_account_name, $account_id, $sub_account_type);
            $stmt->execute();
            $stmt->close();

            // Insert the sub account and its initial account balance into company_sub_account_balance table
            $last_id = $conn->insert_id;
            $company_id = $_SESSION['company_id'];

            // Determine whether it's a debit or credit based on account type
            $balance = 0;
            if ($account_id == 1 || $account_id == 5) {
                // Asset or Expense: Debit initial amount
                $balance = $amount;
            } elseif ($account_id == 2 || $account_id == 3 || $account_id == 4) {
                // Liability, Equity, or Revenue: Credit initial amount
                $balance = -$amount;
            }

            $sql = "INSERT INTO company_sub_account_balance (company_id, sub_account_id, balance) VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iid", $company_id, $last_id, $balance);

            if ($stmt->execute()) {
                $conn->commit();
                $stmt->close();
                $sm = "Sub Account added successfully!";
                header("Location:../../pages/Company/Company.php?page=add-account&success=$sm");
                exit();
            } else {
                throw new Exception("Internal Server Error");
            }
        }
    } catch (Exception $e) {
        // Internal server error
        $conn->rollback();
        $em = "Internal Server Error";
        header("Location:../../pages/Company/Company.php?page=add-account&error=$em");
        exit();
    }
}
?>