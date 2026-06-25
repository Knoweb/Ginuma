<?php
session_start();
include_once ("../includes/Functions.php");
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $name = $_POST['customerName'];
    $phone = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $nic = $_POST['nicNumber'];
    $customer_type = $_POST['customer_type'] ? $_POST['customer_type'] : "Individual";
    $customerAddress = $_POST['customerAddress'];
    $tinNumber = $_POST['tinNumber'];
    $vatNumber = $customer_type == 'Individual' ? '' : $_POST['vatNumber'];
    $businessRegNumber = $customer_type == 'Individual' ? '' : $_POST['businessRegNumber'];

    // validating and sanitizing user inputs
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $customerAddress = filter_var($customerAddress, FILTER_SANITIZE_STRING);
    $tinNumber = filter_var($tinNumber, FILTER_SANITIZE_STRING);
    $vatNumber = $customer_type != 'Individual' ? filter_var($vatNumber, FILTER_SANITIZE_STRING) : "";
    $businessRegNumber = $customer_type != 'Individual' ? filter_var($businessRegNumber, FILTER_SANITIZE_STRING) : "";

    // checking if the user has entered some unwanted inputs like script
    if ($name != $_POST['customerName']) {
        // error
        $wm = "Invalid characters in the Name field";
        header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
        exit();
    }
    if ($email != $_POST['email']) {
        // error
        $wm = "Invalid characters in the Email field";
        header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
        exit();
    }
    if ($customerAddress != $_POST['customerAddress']) {
        // error
        $wm = "Invalid characters in the Address field";
        header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
        exit();
    }
    if ($tinNumber != $_POST['tinNumber']) {
        // error
        $wm = "Invalid characters in the TIN Number field";
        header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
        exit();
    }
    if ($customer_type != 'Individual') {
        if ($vatNumber != $_POST['vatNumber']) {
            // error
            $wm = "Invalid characters in the VAT Number field";
            header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
            exit();
        }
    }
    if ($customer_type != 'Individual') {
        if ($businessRegNumber != $_POST['businessRegNumber'] && $customer_type != 'Individual') {
            // error
            $wm = "Invalid characters in the Business Registration Number field";
            header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
            exit();
        }
    }
    if (!checkNic($nic)) {
        // nic is  not valid
        $wm = "Invalid NIC number";
        header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
        exit();
    }
    if (!preg_match('/^[0-9]{10}+$/', $phone)) {
        $wm = "Invalid Phone number";
        header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
        exit();
    }

    // check if this customer already saved in the database
    try {
        $sql = "SELECT * FROM customer_tbl WHERE nic=? OR email=? OR phone_no=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nic, $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            $stmt->close();
            $status = 1;
            $current_date = date("Y-m-d");
            $user_id = $_SESSION['company_id'];
            if ($customer_type == 'Individual') {
                $sql = "INSERT INTO customer_tbl (name, email, phone_no, address, nic, customer_type, tin_no, date_added, company_id, status) VALUES (?,?,?,?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Could not execute statement");
                }
                $stmt->bind_param("ssssssssii", $name, $email, $phone, $customerAddress, $nic, $customer_type, $tinNumber, $current_date, $user_id, $status);
            } else {
                $sql = "INSERT INTO customer_tbl (name, email, phone_no, address, nic, customer_type, tin_no, vat_no, business_reg_no, date_added, company_id, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Could not execute statement");
                }
                $stmt->bind_param("ssssssssssii", $name, $email, $phone, $customerAddress, $nic, $customer_type, $tinNumber, $vatNumber, $businessRegNumber, $current_date, $user_id, $status);
            }
            if ($stmt->execute()) {
                $stmt->close();
                $sm = "New Customer Registered Successfully";
                header("Location: ../../pages/Company/Company.php?page=add-customer&success=$sm");
                exit();
            } else {
                $stmt->close();
                $em = "Unable to execuate the command!";
                header("Location: ../../pages/Company/Company.php?page=add-customer&error=$em");
                exit();
            }

        } else {
            // customer already exists
            $wm = "Customer Already Exists";
            header("Location: ../../pages/Company/Company.php?page=add-customer&warning=$wm");
            exit();

        }
    } catch (Exception) {
        $em = "Internal Server Error";
        header("Location: ../../pages/Company/Company.php?page=add-customer&error=$em");
        exit();
    }
}

?>