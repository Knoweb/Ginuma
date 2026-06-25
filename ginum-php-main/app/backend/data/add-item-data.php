<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $item_name = $_POST['item_name'];
    $item_units = $_POST['item_units'];
    $qty = $_POST['qty'];
    $unit_price = $_POST['unit_price'];
    $tax = $_POST['tax'] ? $_POST['tax'] : 0;
    $tranfer = $_POST['tranfer'];
    $description = $_POST['description'];
    $w_l_v = $_POST['lw_value'];

    // Include necessary files
    include_once("../includes/Functions.php");
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    // Enable error reporting for debugging
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

    // Sanitize and validate inputs
    $item_name = filter_var($item_name, 513);
    try {
        $qty = filter_var($qty, FILTER_SANITIZE_NUMBER_INT);
        $unit_price = filter_var($unit_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $tax = filter_var($tax, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    } catch (Exception $e) {
        $em = $e->getMessage();
        header("Location:../../pages/Company/Company.php?page=add-item&error=$em");
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Calculate item price
        $sub_total = $unit_price * $qty;
        $total_price = $sub_total + $sub_total * $tax / 100;
        $company_id = $_SESSION['company_id'];
        $current_date = date("Y-m-d");

        // insert the data into the item_tbl
        $sql = "INSERT INTO item_tbl (item_name, unit, total_price, date_added, status, description, company_id, length_or_weight_or_vol, bought_item_count) 
                VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: $conn->error");
        }
        $status = 1;
        $stmt->bind_param("ssdsisidd", $item_name, $item_units, $total_price, $current_date, $status, $description, $company_id, $w_l_v, $qty);
        $stmt->execute();
        if ($stmt->errno) {
            throw new Exception("Execute failed: $stmt->error");
        }
        $stmt->close();

        $last_id = $conn->insert_id;

        // insert items into inventory table
        $sql = "INSERT INTO inventory_tbl (item_id, company_id, date_added, qty, unit_price) VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: $conn->error");
        }
        $stmt->bind_param("iisid", $last_id, $company_id, $current_date, $qty, $unit_price);
        $stmt->execute();
        if ($stmt->errno) {
            throw new Exception("Execute failed: $stmt->error");
        }
        $stmt->close();

        // Determine the account and sub-account IDs using SQL queries
        $account_id = null;
        $sub_account_id = null;

        // check the account type
        $sql = "SELECT * FROM sub_account_tbl sat INNER JOIN account_tbl at ON sat.account_id = at.account_id WHERE sat.sub_account_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Could not execute statement");
        }
        $stmt->bind_param("i", $tranfer);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $account_id = $row['account_id'];
            $sub_account_id = $row['sub_account_id'];
            $account_name = $row['account_name'];
            $stmt->close();

            // check the sub account type for update the account balance
            if ($account_name == 'Assets') {
                // deduct the account balance from the account
                $sql = "SELECT * FROM company_sub_account_balance WHERE sub_account_id=? AND company_id=?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Could not execute statement");
                }
                $stmt->bind_param("ii", $sub_account_id, $company_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    // already has the account in the table. time to update the account balance
                    $sql = "UPDATE company_sub_account_balance SET balance=balance-? WHERE sub_account_id=? AND company_id=?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        throw new Exception("Could not execute statement");
                    }
                    $stmt->bind_param("dii", $total_price, $sub_account_id, $company_id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // should insert the account into the table
                    $sql = "INSERT INTO company_sub_account_balance (company_id, sub_account_id, balance) VALUES (?,?,?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        throw new Exception("Could not execute statement");
                    }
                    $total_price = 0 - $total_price;
                    $stmt->bind_param("iid", $company_id, $sub_account_id, $total_price); // Deduct the balance
                    $stmt->execute();
                    $stmt->close();
                }
            } else if ($account_name == 'Liabilities') {
                // add the account balance to the account
                $sql = "SELECT * FROM company_sub_account_balance WHERE sub_account_id=? AND company_id=?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Could not execute statement");
                }
                $stmt->bind_param("ii", $sub_account_id, $company_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    // already has the account in the table. time to update the account balance
                    $sql = "UPDATE company_sub_account_balance SET balance=balance+? WHERE sub_account_id=? AND company_id=?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        throw new Exception("Could not execute statement");
                    }
                    $stmt->bind_param("dii", $total_price, $sub_account_id, $company_id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // should insert the account into the table
                    $sql = "INSERT INTO company_sub_account_balance (company_id, sub_account_id, balance) VALUES (?,?,?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        throw new Exception("Could not execute statement");
                    }
                    $stmt->bind_param("iid", $company_id, $sub_account_id, $total_price);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
            }
            $inventory_sub_account_query = "SELECT sub_account_id FROM sub_account_tbl WHERE sub_account_name = 'Inventory'";
            $inventory_sub_account_result = $conn->query($inventory_sub_account_query);
            if ($inventory_sub_account_result === false || $inventory_sub_account_result->num_rows == 0) {
                throw new Exception("Inventory sub-account not found");
            }
            $row = $inventory_sub_account_result->fetch_assoc();
            $inventory_sub_account_id = $row['sub_account_id'];

            $sql = "SELECT * FROM company_sub_account_balance WHERE sub_account_id = $inventory_sub_account_id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // already in the company_sub_account_balance table.
                $row = $result->fetch_assoc();
                $result = $row['sub_account_id'];
                $sql = "UPDATE company_sub_account_balance SET balance=balance+? WHERE company_id=? AND sub_account_id=?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Could not execute statement");
                }
                $stmt->bind_param("dii", $total_price, $company_id, $inventory_sub_account_id);
                $stmt->execute();
                $stmt->close();
            } else {
                // should insert the account into the table
                $sql = "INSERT INTO company_sub_account_balance (company_id, sub_account_id, balance) VALUES (?,?,?)";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Could not execute statement");
                }
                $stmt->bind_param("iid", $company_id, $inventory_sub_account_id, $total_price);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            throw new Exception("Sub-account not found");
        }

        // Log transaction
        // if (!makeTransactionLog($conn, $company_id, $account_id, $sub_account_id, "Item Addition", $total_price, $description)) {
        //     throw new Exception("Transaction log failed");
        // }

        // Commit transaction
        $conn->commit();

        // pass a success message to the add item frontend via the URL
        $sm = "Item Added Successfully!";
        header("Location:../../pages/Company/Company.php?page=add-item&success=$sm");
        exit();
    } catch (Exception $e) {
        // Rollback transaction and handle error
        $conn->rollback();
        $em = "Internal Server Error: " . $e->getMessage();
        header("Location:../../pages/Company/Company.php?page=add-item&error=$em");
        exit();
    }
}
