<?php

require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['companyId']) && isset($_POST['privilegeLevel'])) {
            $companyId = $_POST['companyId'];
            $privilegeLevel = $_POST['privilegeLevel'];

            // Update the database with the new privilege level
            $sql = "UPDATE company_tbl SET privilege_id = ? WHERE company_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $privilegeLevel, $companyId);
                $stmt->execute();
                // if ($stmt->execute()) {
                //     echo "Privilege level updated successfully.";
                // } else {
                //     echo "Error updating privilege level: " . $stmt->error;
                // }

                $stmt->close();
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>