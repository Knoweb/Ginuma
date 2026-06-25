<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_GET['id'];
    $supplierName = $_POST['supplierName'];
    $supplierEmail = $_POST['supplierEmail'];
    $mobileNo = $_POST['mobileNo'];
    $supplierAddress = $_POST['supplierAddress'];
    $supplierType = $_POST['supplierType'] ? $_POST['supplierType'] : "Individual";
    $itemCategory = $_POST['itemCategory'] ? $_POST['itemCategory'] : 1;
    $tinNumber = $_POST['tinNumber'];
    $vatNumber = $_POST['vatNumber'] ? $_POST['vatNumber'] : "";
    $businessRegNumber = $_POST['businessRegNumber'] ? $_POST['businessRegNumber'] : "";

    $new_data = [
        'supplier_name' => $supplierName,
        'supplier_type' => $supplierType,
        'address' => $supplierAddress,
        'phone_no' => $mobileNo,
        'email' => $supplierEmail,
        'vat_no' => $vatNumber,
        'tin_no' => $tinNumber,
        'business_reg_no' => $businessRegNumber,
        'item_category_id' => $itemCategory,
    ];

    // sanitizing and validating user inputs
    $supplierName = filter_var($supplierName, FILTER_SANITIZE_STRING);
    $supplierEmail = filter_var($supplierEmail, FILTER_SANITIZE_EMAIL);
    $supplierAddress = filter_var($supplierAddress, FILTER_SANITIZE_STRING);
    $tinNumber = filter_var($tinNumber, FILTER_SANITIZE_STRING);
    $vatNumber = filter_var($vatNumber, FILTER_SANITIZE_STRING);
    $businessRegNumber = filter_var($businessRegNumber, FILTER_SANITIZE_STRING);

    try {
        // phone no validation
        if (!preg_match('/^[0-9]{10}+$/', $mobileNo)) {
            $wm = "Invalid Phone number";
            throw new Exception($wm);
        }
        if ($supplierName != $_POST['supplierName']) {
            // error
            $wm = "Invalid characters in the Name field";
            throw new Exception($wm);
        }
        if ($supplierEmail != $_POST['supplierEmail']) {
            // error
            $wm = "Invalid characters in the Email field";
            throw new Exception($wm);
        }
        if ($supplierAddress != $_POST['supplierAddress']) {
            // error
            $wm = "Invalid characters in the Address field";
            throw new Exception($wm);
        }
        if ($tinNumber != $_POST['tinNumber']) {
            // error
            $wm = "Invalid characters in the TIN No. field";
            throw new Exception($wm);
        }
        if (($vatNumber != $_POST['vatNumber']) && $supplierType != 'Business') {
            // error
            $wm = "Invalid characters in the VAT No. field";
            throw new Exception($wm);
        }
        if (($businessRegNumber != $_POST['businessRegNumber']) && $supplierType != 'Business') {
            // error
            $wm = "Invalid characters in the Business Register No. field";
            throw new Exception($wm);
        }

        // time to update the supplier data
        require_once '../connection/conn.php';
        $db = new DBConnection();
        $conn = $db->conn;

        if ($_SESSION['role'] == 'User') {
            // add the edit request into the database
            // get the old details from the department_tbl
            $sql = "SELECT * FROM supplier_tbl WHERE supplier_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $old_data_row = $result->fetch_assoc();
                // its time to save the request into the edit_requests table
                $sql = "INSERT INTO edit_requests (section, table_name, record_id, sub_login_id, old_data, new_data, status, company_id) VALUES ('Supplier', 'supplier_tbl', ?, ?, ?, ?, 'pending', ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iissi", $id, $_SESSION['sub_login_id'], json_encode($old_data_row), json_encode($new_data), $_SESSION['company_id']);
                $stmt->execute();
                $stmt->close();
            }
            echo json_encode(["status" => "success", "message" => "Edit request sent to Company Admin. Wait for the approval!"]);
        } else {
            $current_date = date("Y-m-d");
            if ($supplierType == 'Individual') {
                $sql = "UPDATE supplier_tbl SET supplier_name=?, supplier_type=?, address=?, phone_no=?, email=?, tin_no=?, item_category_id=?, date_updated=? WHERE supplier_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssisi", $supplierName, $supplierType, $supplierAddress, $mobileNo, $supplierEmail, $tinNumber, $itemCategory, $current_date, $id);
            } else {
                $sql = "UPDATE supplier_tbl SET supplier_name=?, supplier_type=?, address=?, phone_no=?, email=?, tin_no=?, vat_no=?, business_reg_no=?, item_category_id=?, date_updated=? WHERE supplier_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssisi", $supplierName, $supplierType, $supplierAddress, $mobileNo, $supplierEmail, $tinNumber, $vatNumber, $businessRegNumber, $itemCategory, $current_date, $id);
            }
            if ($stmt->execute()) {
                // success
                $sm = "Supplier details updated successfully!";
            } else {
                // faild
                $em = "Unable to execute the command";
                throw new Exception($em);
            }
            echo json_encode(['status' => 'success', 'message' => $sm]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>