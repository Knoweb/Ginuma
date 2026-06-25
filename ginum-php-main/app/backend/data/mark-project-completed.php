<?php

if (isset($_GET['id'])) {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $id = $_GET['id'];

    $sql = "UPDATE project_tbl SET work_status=? WHERE project_id=?";
    try {
        $stmt = $conn->prepare($sql);
        $status = 'Completed';
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            // success
            $stmt->close();
            $sm = "Marked as Completed";
            header("Location:../../pages/Company/Company.php?page=show-orders&success=$sm");
            exit();
        } else {
            // faild
            $em = "Unable to execute the command";
            header("Location:../../pages/Company/Company.php?page=show-orders&error=$em");
            exit();
        }
    } catch (Exception $e) {
        // internal server error
        $em = "Internal Server Error";
        header("Location:../../pages/Company/Company.php?page=show-orders&error=$em");
        exit();
    }
} else {
    // redirect to show projects page
    header("Location:../../pages/Company/Company.php?page=show-orders");
}

?>