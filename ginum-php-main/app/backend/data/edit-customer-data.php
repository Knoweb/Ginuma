<?php
session_start();
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once("../includes/Functions.php");
    $id = $_GET['id'];
    $name = $_POST['customerName'];
    $phone = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $nic = $_POST['nicNumber'];
    $customer_type = $_POST['customer_type'] ? $_POST['customer_type'] : "Individual";
    $customerAddress = $_POST['customerAddress'];
    $tinNumber = $_POST['tinNumber'];
    $vatNumber = $customer_type == 'Individual' ? '' : $_POST['vatNumber'];
    $businessRegNumber = $customer_type == 'Individual' ? '' : $_POST['businessRegNumber'];

    // save the user input into a associative array
    $new_data = [
        'name' => $name,
        'email' => $email,
        'phone_no' => $phone,
        'address' => $customerAddress,
        'nic' => $nic,
        'customer_type' => $customer_type,
        'vat_no' => $vatNumber,
        'tin_no' => $tinNumber,
        'business_reg_no' => $businessRegNumber,
    ];

    // validating and sanitizing user inputs
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $customerAddress = filter_var($customerAddress, FILTER_SANITIZE_STRING);
    $tinNumber = filter_var($tinNumber, FILTER_SANITIZE_STRING);
    $vatNumber = $customer_type != 'Individual' ? filter_var($vatNumber, FILTER_SANITIZE_STRING) : "";
    $businessRegNumber = $customer_type != 'Individual' ? filter_var($businessRegNumber, FILTER_SANITIZE_STRING) : "";

    try {
        // checking if the user has entered some unwanted inputs like script
        if ($name != $_POST['customerName']) {
            // error
            $wm = "Invalid characters in the Name field";
            throw new Exception($wm);
        }
        if ($email != $_POST['email']) {
            // error
            $wm = "Invalid characters in the Email field";
            throw new Exception($wm);
        }
        if ($customerAddress != $_POST['customerAddress']) {
            // error
            $wm = "Invalid characters in the Address field";
            throw new Exception($wm);
        }
        if ($tinNumber != $_POST['tinNumber']) {
            // error
            $wm = "Invalid characters in the TIN Number field";
            throw new Exception($wm);
        }
        if ($customer_type != 'Individual') {
            if ($vatNumber != $_POST['vatNumber']) {
                // error
                $wm = "Invalid characters in the VAT Number field";
                throw new Exception($wm);
            }
        }
        if ($customer_type != 'Individual') {
            if ($businessRegNumber != $_POST['businessRegNumber'] && $customer_type != 'Individual') {
                // error
                $wm = "Invalid characters in the Business Registration Number field";
                throw new Exception($wm);
            }
        }
        if (!checkNic($nic)) {
            // nic is  not valid
            $wm = "Invalid NIC number";
            throw new Exception($wm);
        }
        if (!preg_match('/^[0-9]{10}+$/', $phone)) {
            $wm = "Invalid Phone number";
            throw new Exception($wm);
        }

        // time to update the customer data
        if ($_SESSION['role'] == 'User') {
            // add the edit request into the database
            // get the old details from the customer_tbl
            $sql = "SELECT * FROM customer_tbl WHERE customer_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $old_data_row = $result->fetch_assoc();
                // its time to save the request into the edit_requests table
                $sql = "INSERT INTO edit_requests (section, table_name, record_id, sub_login_id, old_data, new_data, status, company_id) VALUES ('Customer', 'customer_tbl', ?, ?, ?, ?, 'pending', ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iissi", $id, $_SESSION['sub_login_id'], json_encode($old_data_row), json_encode($new_data), $_SESSION['company_id']);
                $stmt->execute();
                $stmt->close();
            }
            echo json_encode(array("status" => "success", "message" => "Edit request sent to Company Admin. Wait for the approval!"));
        } else {
            $current_date = date("Y-m-d");
            if ($customer_type == 'Individual') {
                $sql = "UPDATE customer_tbl SET name=?, email=?, phone_no=?, address=?, nic=?, customer_type=?, tin_no=?, date_updated=? WHERE customer_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssi", $name, $email, $phone, $customerAddress, $nic, $customer_type, $tinNumber, $current_date, $id);
            } else {
                $sql = "UPDATE customer_tbl SET name=?, email=?, phone_no=?, address=?, nic=?, customer_type=?, tin_no=?, vat_no=?, business_reg_no=?, date_updated=? WHERE customer_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssssi", $name, $email, $phone, $customerAddress, $nic, $customer_type, $tinNumber, $vatNumber, $businessRegNumber, $current_date, $id);
            }
            if ($stmt->execute()) {
                // success
                $stmt->close();
                echo json_encode(["status" => "success", "message" => "Customer details updated successfully!"]);
            } else {
                // faild
                echo json_encode(["status" => "error", "message" => "Unable to execute the command"]);
            }
        }
    } catch (Exception $e) {
        // internal server error
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>