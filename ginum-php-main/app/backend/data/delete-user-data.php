<?php
session_start();
if (isset($_GET['id'])) {
    // import the database connection class
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    try {
        // get the employee id from the url
        $employee_id = $_GET['id'];
        // get the current company login from the session variable
        $company_id = $_SESSION['company_id'];

        // make the user's status to 0 making the user can't log into the system
        $sql = "UPDATE sub_logins_tbl SET status=0 WHERE employee_id=? AND company_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $employee_id, $company_id);
        if ($stmt->execute()) {
            // close the statement
            $stmt->close();
            // redirect to the parent page
            $sm = "User removed successfully!";
            header("Location: ../../pages/Company/Company.php?page=show-users&success=$sm");
            exit;
        } else {
            throw new Exception("Unable to process the request!");
        }
    } catch (Exception $e) {
        $em = "Error: " . $e->getMessage() . " (In line: " . $e->getLine() . ")";
    }
}
?>