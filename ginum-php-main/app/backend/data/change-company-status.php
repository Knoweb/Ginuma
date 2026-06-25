<?php
if (isset($_GET['id']) && $_GET['status']) {
    // import the database connection class
    require_once '../../backend/connection/conn.php';
    $db = new DBConnection();
    // get the database connection
    $conn = $db->conn;


    // get the required parameters from the url
    $company_id = $_GET['id'];      // get the company id
    $status = $_GET['status'];      // get the status code

    // query to update the status of the company
    $sql = "UPDATE company_tbl SET status=? WHERE company_id=?";
    // prepare the statement
    $stmt = $conn->prepare($sql);
    // bind the parameters to the prepared statement
    $stmt->bind_param("ii", $status, $company_id);
    // execute the query
    $stmt->execute();
    // close the statement
    $stmt->close();

    // redirect to the company list page
    header("Location: ../../pages/Admin/Admin.php?page=registered-companies");
    exit();
} else {
    // redirect to the previous page
    header("Location: ../../pages/Admin/Admin.php?page=requested-companies");
    exit();
}
?>