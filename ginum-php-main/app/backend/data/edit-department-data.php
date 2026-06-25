<?php
session_start();
$id = $_GET['id'];

require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dpt_name = $_POST['dpt_name'];
    $dpt_code = $_POST['dpt_code'];
    $description = $_POST['description'];

    $new_data = [
        'department_code' => $dpt_code,
        'department_name' => $dpt_name,
        'description' => $description,
    ];

    // sanitizing and validating user inputs
    $dpt_name = filter_var($dpt_name, FILTER_SANITIZE_STRING);
    $dpt_code = filter_var($dpt_code, FILTER_SANITIZE_STRING);
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    try {
        // checking if the user has entered some unwanted inputs like script
        if ($dpt_name != $_POST['dpt_name']) {
            // error
            $wm = "Invalid characters in the Department Name field";
            throw new Exception($wm);
        }
        if ($dpt_code != $_POST['dpt_code']) {
            // error
            $wm = "Invalid characters in the Department Code field";
            throw new Exception($wm);
        }
        if ($description != $_POST['description']) {
            // error
            $wm = "Invalid characters in the Description field";
            throw new Exception($wm);
        }

        // time to update the values inside
        if ($_SESSION['role'] == 'User') {
            // add the edit request into the database
            // get the old details from the department_tbl
            $sql = "SELECT * FROM department_tbl WHERE department_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $old_data_row = $result->fetch_assoc();
                // its time to save the request into the edit_requests table
                $sql = "INSERT INTO edit_requests (section, table_name, record_id, sub_login_id, old_data, new_data, status, company_id) VALUES ('Department', 'department_tbl', ?, ?, ?, ?, 'pending', ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iissi", $id, $_SESSION['sub_login_id'], json_encode($old_data_row), json_encode($new_data), $_SESSION['company_id']);
                $stmt->execute();
                $stmt->close();
            }
            echo json_encode(array("status" => "success", "message" => "Edit request sent to Company Admin. Wait for the approval!"));
        } else {
            $sql = "UPDATE department_tbl SET department_code=?, department_name=?, description=? WHERE department_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $dpt_code, $dpt_name, $description, $id);
            if ($stmt->execute()) {
                // success
                $stmt->close();
                echo json_encode(['status' => 'success', 'message' => 'Department data updated successfully!']);
            } else {
                //faild
                $stmt->close();
                echo json_encode(['status' => 'error', 'message' => 'Unable to execute the command']);
            }
        }
    } catch (Exception $e) {
        // internal server error
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>