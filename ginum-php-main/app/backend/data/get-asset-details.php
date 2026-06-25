<?php

// enable sessions for this page
session_start();

// check if the received request is a GET request and if the sub_account_type_id is set in the request body
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['sub_account_type_id'])) {
    // import the database connection class
    require_once '../connection/conn.php';
    // create a new instance of the DBConnection class to connect to the database
    $db = new DBConnection();
    // get the connection object from the DBConnection class
    $conn = $db->conn;


    // user inputs
    $sub_account_type_id = $_GET['sub_account_type_id'];

    // echo $sub_account_type_id;
    // fetch the sub account type details from company_sub_account_balance and sub_account_type_tbl
    $sql = "SELECT * FROM sub_account_type_tbl satt INNER JOIN company_sub_account_balance csab ON (satt.sub_account_type_id=csab.sub_account_type_id) WHERE csab.company_id=? AND satt.sub_account_type_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['company_id'], $sub_account_type_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // close the statement
        $stmt->close();
        // fetch the result row as an associative array
        $row = $result->fetch_assoc();
        // send the result row to the frontend
        echo json_encode(['status' => 'success', 'data' => $row]);
    }
}
