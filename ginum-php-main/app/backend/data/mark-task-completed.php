<?php
if (isset($_GET['task_id'])) {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $task_id = $_GET['task_id'];
    $project_id = $_GET['project_id'];
    $state = $_GET['state'];
    if ($state == 2) {
        // change state as finished
        $status = 1;
        $sql = "UPDATE task_tbl SET status=? WHERE task_id=?";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $status, $task_id);
            if ($stmt->execute()) {
                // success
                $sm = "Task marked as Done!";
                header("Location:../../pages/Company/Company.php?page=edit-order&success=$sm&id=$project_id");
                exit();
            } else {
                // faild
                $em = "Unable to execute the command!";
                header("Location:../../pages/Company/Company.php?page=page=edit-order&error=$em&id=$project_id");
                exit();
            }
        } catch (Exception $e) {
            // internal server error
            $em = "Internal Server Error";
            header("Location:../../pages/Company/Company.php?page=edit-order&error=$em&id=$project_id");
            exit();
        }
    } else {
        // change state as Not finished
        $status = 0;
        $sql = "UPDATE task_tbl SET status=? WHERE task_id=?";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $status, $task_id);
            if ($stmt->execute()) {
                // success
                $sm = "Task marked as Not Finished!";
                header("Location:../../pages/Company/Company.php?page=edit-order&success=$sm&id=$project_id");
                exit();
            } else {
                // faild
                $em = "Unable to execute the command!";
                header("Location:../../pages/Company/Company.php?page=page=edit-order&error=$em&id=$project_id");
                exit();
            }
        } catch (Exception $e) {
            // internal server error
            $em = "Internal Server Error";
            header("Location:../../pages/Company/Company.php?page=edit-order&error=$em&id=$project_id");
            exit();
        }
    }
}
?>