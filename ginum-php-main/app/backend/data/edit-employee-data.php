<?php
session_start();
include_once("../includes/Functions.php");
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_GET['id'];
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $gender = $_POST['gender'] ? $_POST['gender'] : "Male";
    $dob = $_POST['dob'];
    $nic = $_POST['nic'];
    $address = $_POST['address'];
    $phone = $_POST['mobileNo'];
    $email = $_POST['email'];
    $epf = $_POST['epf_no'];
    $designation_id = $_POST['designation'];
    $date_joined = $_POST['date_joined'];

    $prev_dpt_id = $_GET['dpt_id'];
    $prev_designation_id = $_GET['designationId'];

    $new_data = [
        'first_name' => $fname,
        'last_name' => $lname,
        'gender' => $gender,
        'designation_id' => $designation_id,
        'address' => $address,
        'mobileNo' => $phone,
        'dob' => $dob,
        'nic' => $nic,
        'epf_no' => $epf,
        'email' => $email,
        'date_joined' => $date_joined,
    ];

    // sanitizing user inputs
    $fname = filter_var($fname, FILTER_SANITIZE_STRING);
    $lname = filter_var($lname, FILTER_SANITIZE_STRING);
    $address = filter_var($address, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    try {
        // nic validation
        if (!checkNic($nic)) {
            // nic is  not valid
            $wm = "Invalid NIC number";
            throw new Exception($wm);
        }
        // phone no validation
        if (!preg_match('/^[0-9]{10}+$/', $phone)) {
            $wm = "Invalid Phone number";
            throw new Exception($wm);
        }
        // checking if the user has entered some unwanted inputs like script
        if ($fname != $_POST['firstName']) {
            // error
            $wm = "Invalid characters in the First Name field";
            throw new Exception($wm);
        }
        if ($lname != $_POST['lastName']) {
            // error
            $wm = "Invalid characters in the Last Name field";
            throw new Exception($wm);
        }
        if ($email != $_POST['email']) {
            // error
            $wm = "Invalid characters in the Email field";
            throw new Exception($wm);
        }
        if ($address != $_POST['address']) {
            // error
            $wm = "Invalid characters in the Address field";
            throw new Exception($wm);
        }

        if ($_SESSION['role'] == 'User') {
            // add the edit request into the database
            // get the old details from the department_tbl
            $sql = "SELECT * FROM employee_tbl WHERE employee_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $old_data_row = $result->fetch_assoc();
                // its time to save the request into the edit_requests table
                $sql = "INSERT INTO edit_requests (section, table_name, record_id, sub_login_id, old_data, new_data, status, company_id) VALUES ('Employee', 'employee_tbl', ?, ?, ?, ?, 'pending', ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iissi", $id, $_SESSION['sub_login_id'], json_encode($old_data_row), json_encode($new_data), $_SESSION['company_id']);
                $stmt->execute();
                $stmt->close();
            }
            echo json_encode(["status" => "success", "message" => "Edit request sent to Company Admin. Wait for the approval!"]);
        } else {
            // time to update employee details
            $current_date = date("Y-m-d");
            $sql = "UPDATE employee_tbl SET first_name=?, last_name=?, gender=?, designation_id=?, address=?, mobileNo=?, dob=?, nic=?, epf_no=?, email=?, date_joined=?, date_updated=? WHERE employee_id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssissssssssi", $fname, $lname, $gender, $designation_id, $address, $phone, $dob, $nic, $epf, $email, $date_joined, $current_date, $id);
            if ($stmt->execute()) {
                // success
                echo json_encode(['status' => 'success', 'message' => 'Employee details udpated successfully!']);
            } else {
                // faild
                throw new Exception("Unable to execute the command");
            }
        }
    } catch (Exception $e) {
        echo json_encode(['status' => ($e->getCode()), 'message' => $e->getMessage()]);
    }

}

?>