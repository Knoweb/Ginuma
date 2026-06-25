<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // create the database connection object
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    try {
        // get the sub account id
        $sub_account_id = $_POST['company_sub_account_balance_id'];

        // sql query to get the selected account's balance and send it to the frontend
        $sql = "SELECT * FROM bank_account_tbl bat INNER JOIN company_sub_account_balance csab ON (bat.company_sub_account_balance_id=csab.company_sub_account_balance_id) INNER JOIN bank_details_tbl bdt ON (bat.bank_details_id=bdt.bank_details_id) WHERE bat.company_id=? AND bat.company_sub_account_balance_id=?";
        // prepare the sql statement
        $stmt = $conn->prepare($sql);
        // bind the parameters into the prepared statement
        $stmt->bind_param("ii", $_SESSION['company_id'], $sub_account_id);
        // execute the prepared statement
        $stmt->execute();
        // get the result
        $result = $stmt->get_result();

        // if there is a result, fetch the balance and send it to the frontend
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo $row['balance'];
        } else {
            echo 0;
        }
    } catch (Exception $e) {
        // throw an exception if something goes wrong
        echo htmlspecialchars($e->getMessage());
    }
}

?>