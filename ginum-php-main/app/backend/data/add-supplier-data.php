<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $supplierName = $_POST['supplierName'];
    $supplierEmail = $_POST['supplierEmail'];
    $mobileNo = $_POST['mobileNo'];
    $supplierAddress = $_POST['supplierAddress'];
    $supplierType = $_POST['supplierType'] ? $_POST['supplierType'] : "Individual";
    $itemCategory = $_POST['itemCategory'] ? $_POST['itemCategory'] : 1;
    $tinNumber = $_POST['tinNumber'];
    $vatNumber = $_POST['vatNumber'] ? $_POST['vatNumber'] : "";
    $businessRegNumber = $_POST['businessRegNumber'] ? $_POST['businessRegNumber'] : "";

    // sanitizing and validating user inputs
    $supplierName = filter_var($supplierName, FILTER_SANITIZE_STRING);
    $supplierEmail = filter_var($supplierEmail, FILTER_SANITIZE_EMAIL);
    $supplierAddress = filter_var($supplierAddress, FILTER_SANITIZE_STRING);
    $tinNumber = filter_var($tinNumber, FILTER_SANITIZE_STRING);
    $vatNumber = filter_var($vatNumber, FILTER_SANITIZE_STRING);
    $businessRegNumber = filter_var($businessRegNumber, FILTER_SANITIZE_STRING);

    // phone no validation
    if (!preg_match('/^[0-9]{10}+$/', $mobileNo)) {
        $wm = "Invalid Phone number";
        header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
        exit();
    }
    if ($supplierName != $_POST['supplierName']) {
        // error
        $wm = "Invalid characters in the Name field";
        header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
        exit();
    }
    if ($supplierEmail != $_POST['supplierEmail']) {
        // error
        $wm = "Invalid characters in the Email field";
        header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
        exit();
    }
    if ($supplierAddress != $_POST['supplierAddress']) {
        // error
        $wm = "Invalid characters in the Address field";
        header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
        exit();
    }
    if ($tinNumber != $_POST['tinNumber']) {
        // error
        $wm = "Invalid characters in the TIN No. field";
        header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
        exit();
    }
    if (($vatNumber != $_POST['vatNumber']) && $supplierType != 'Business') {
        // error
        $wm = "Invalid characters in the VAT No. field";
        header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
        exit();
    }
    if (($businessRegNumber != $_POST['businessRegNumber']) && $supplierType != 'Business') {
        // error
        $wm = "Invalid characters in the Business Register No. field";
        header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
        exit();
    }

    // check if the supplier already in the database
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    if ($supplierType == 'Individual') {
        $sql = "SELECT * FROM supplier_tbl WHERE phone_no=? OR email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $mobileNo, $supplierEmail);
    } else {
        $sql = "SELECT * FROM supplier_tbl WHERE phone_no=? OR email=? OR vat_no=? OR tin_no=? OR business_reg_no=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $mobileNo, $supplierEmail, $vatNumber, $tinNumber, $businessRegNumber);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows < 1) {
        // file handle
        $filename = "";
        try {
            if (isset($_FILES["br_report"]) && $_FILES["br_report"]["error"] == 0) {
                if ($_FILES['br_report']['type'] == "application/pdf") {
                    $file_name = $_FILES['br_report']['name'];
                    $file_tmp = $_FILES['br_report']['tmp_name'];
                    $filename = "../../assets/docs/uploads/supplier_report/" . $file_name;
                    move_uploaded_file($file_tmp, $filename);
                    // file handling completed
                } else {
                    // pdf files only
                    $wm = "PDF Files Only!";
                    header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
                    exit();
                }
            } else {
            }
        } catch (Exception $e) {
            // unable to upload the BR Report
            $em = "Unable to upload the BR Report";
            header("Location: ../../pages/Company/Company.php?page=add-supplier&error=$em");
            exit();
        }

        try {
            $company_id = $_SESSION['company_id'];
            $current_date = date("Y-m-d");
            $status = 1;
            $sql = "INSERT INTO supplier_tbl (supplier_name, supplier_type, address, phone_no, email, vat_no, tin_no, business_reg_no, br_report_path, item_category_id, date_added, company_id, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssssisii", $supplierName, $supplierType, $supplierAddress, $mobileNo, $supplierEmail, $vatNumber, $tinNumber, $businessRegNumber, $filename, $itemCategory, $current_date, $company_id, $status);
            if ($stmt->execute()) {
                // success
                $sm = "Supplier Registered Successfully!";
                header("Location: ../../pages/Company/Company.php?page=add-supplier&success=$sm");
                exit();
            } else {
                // faild
                $em = "Unable to execuate the command!";
                header("Location: ../../pages/Company/Company.php?page=add-supplier&error=$em");
                exit();
            }
        } catch (Exception $e) {
            // supplier already exists
            $em = "Internal Server Error";
            header("Location: ../../pages/Company/Company.php?page=add-supplier&error=$em");
            exit();
        }
    } else {
        // supplier already exists
        $wm = "Supplier data already exists!";
        header("Location: ../../pages/Company/Company.php?page=add-supplier&warning=$wm");
        exit();
    }
} else {
    // redirect to add supplier page
    header("Location: ../../pages/Company/Company.php?page=add-supplier");
    exit();
}
?>