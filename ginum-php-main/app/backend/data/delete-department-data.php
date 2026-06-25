<?php
$id = $_GET['id'];
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;
try {
    $sql = "DELETE FROM department_tbl WHERE department_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // success
        $sm = "Department deleted successfully!";
        header("Location: ../../pages/Company/Company.php?page=show-departments&success=$sm");
        exit();
    } else {
        // faild
        $em = "Unable to execute the operation";
        header("Location: ../../pages/Company/Company.php?page=show-departments&error=$em");
        exit();
    }
} catch (Exception) {
    // internal server error
    $em = "Internal Server Error";
    header("Location: ../../pages/Company/Company.php?page=show-departments&error=$em");
    exit();
}

?>