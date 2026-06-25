<?php
try {
    $id = $_GET['id'];
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $status = 0;
    $sql = "UPDATE customer_tbl SET status=? WHERE customer_id=?";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $status, $id);
        if ($stmt->execute()) {
            // success
            $sm = "Customer deleted successfully!";
            header("Location: ../../pages/Company/Company.php?page=show-customers&success=$sm");
            exit();
        } else {
            // faild
            $em = "Unable to execute the command";
            header("Location: ../../pages/Company/Company.php?page=show-customers&error=$em");
            exit();
        }
    } catch (Exception) {
        // internal server error
        $em = "Internal Server Error";
        header("Location: ../../pages/Company/Company.php?page=show-customers&error=$em");
        exit();
    }
} catch (Exception) {
    // redirect to show customers page
    header("Location: ../../pages/Company/Company.php?page=show-customers");
}