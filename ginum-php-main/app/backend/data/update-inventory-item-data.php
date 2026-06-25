<?php
session_start();
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // import the database connection class
    require_once '../connection/conn.php';
    // create a new instance of the DBConnection class to conenct the database
    $db = new DBConnection();
    // get the connection object from the DBConnection class
    $conn = $db->conn;

    // begin the transactional statement execution
    $conn->begin_transaction();
    try {

        // get the inventory account balance
        // get the sub_account_id of Inventory account from the sub_account_tbl
        $sql = "SELECT sub_account_id FROM sub_account_tbl sat INNER JOIN account_tbl at ON (sat.account_id = at.account_id) WHERE sat.sub_account_name='Inventory'";
        $result = $conn->query($sql);
        if ($result->num_rows < 1) {
            // throw new Exception("Could not find the asset account in the database.");
        }
        $inventory_sub_account_id = $result->fetch_assoc()['sub_account_id'];
        // $result->close();
        // now we need to check if the Inventory account's balance is higher than 0
        $sql = "SELECT balance FROM company_sub_account_balance WHERE sub_account_id='$inventory_sub_account_id' AND company_id='" . $_SESSION['company_id'] . "'";
        $result = $conn->query($sql);
        if ($result->num_rows <= 0) {
            throw new Exception("Could not find the inventory account in the database.");
        }
        $inventory_balance = (float) $result->fetch_assoc()["balance"];

        // now we need to check if the inventory account balance is equal to the current total amount of the inventory_tbl
        $sql = "SELECT SUM(unit_price*qty) AS sum_of_unit_price FROM inventory_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
        $result = $conn->query($sql);
        $sum_of_unit_price = (float)$result->fetch_assoc()['sum_of_unit_price'];
        if ($sum_of_unit_price > $inventory_balance) {
            throw new Exception("Inventory balance does not match the current total amount of the inventory_tbl.");
        }

        // Retrieve form data
        $item_name = $_POST['item_name'];
        $item_units = $_POST['item_units'];
        $qty = $_POST['qty'];
        $unit_price = $_POST['unit_price'];
        $tax = $_POST['tax'] ?: 0;
        $tranfer = $_POST['tranfer'];
        $description = $_POST['description'] ?: "";
        $w_l_v = $_POST['lw_value'];

        // sanitize and validate the usre inputs
        /*
            * FILTER_SANITIZE_STRING - 513
            * FILTER_SANITIZE_NUMBER_FLOAT - 520
            */
        if ($item_name != filter_var($item_name, 513)) {
            throw new \Exception('Invalid characters in the Item Name field.');
        }
        if ($item_units != filter_var($item_units, 513)) {
            throw new \Exception('Invalid characters in the Item Unit field.');
        }
        if ($qty != filter_var($qty, 520)) {
            throw new \Exception('Invalid characters in the Quantity field.');
        }
        if ($unit_price != filter_var($unit_price, 520)) {
            throw new \Exception('Invalid characters in the Unit Price field.');
        }
        if ($tax != filter_var($tax, 520)) {
            throw new \Exception('Invalid characters in the Tax field.');
        }
        if ($description != filter_var($description, 513)) {
            throw new \Exception('Invalid characters in the Description field.');
        }
        if ($w_l_v != filter_var($w_l_v, 520)) {
            throw new \Exception('Invalid characters in the Length/Weight/Volume field.');
        }

        // Calculate item price
        $sub_total = $unit_price * $qty;
        $total_price = $sub_total + $sub_total * $tax / 100;
        $current_date = date("Y-m-d");
        $company_id = $_SESSION['company_id'];

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

        // get the last inserted row id from the database / item_tbl
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

        // now we need to commit the transaction to the database to insert the rows
        $conn->commit();

        // return a success message
        $sm = "Items successfully inserted";
        header("Location:../../pages/Company/Company.php?page=update-inventory-item&success=$sm");
        exit();
    } catch (Exception $e) {
        // if any error occurs, rollback the transaction and display the error message and exit from the program
        $conn->rollback();
        $em = "Error: " . $e->getMessage();
        header("Location: ../../pages/Company/Company.php?page=update-inventory-item&error=$em");
        exit();
    }
}
