<?php

session_start();

require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $dpt_name = $_POST['dpt_name'];
    $dpt_code = $_POST['dpt_code'];
    $description = $_POST['description'];

    // sanitizing and validating user inputs
    $dpt_name = filter_var($dpt_name, FILTER_SANITIZE_STRING);
    $dpt_code = filter_var($dpt_code, FILTER_SANITIZE_STRING);
    $description = filter_var($description, FILTER_SANITIZE_STRING);

    // checking if the user has entered some unwanted inputs like script
    if ($dpt_name != $_POST['dpt_name']) {
        // error
        $wm = "Invalid characters in the Department Name field";
        header("Location: ../../pages/Company/Company.php?page=add-department&warning=$wm");
        exit();
    }
    if ($dpt_code != $_POST['dpt_code']) {
        // error
        $wm = "Invalid characters in the Department Code field";
        header("Location: ../../pages/Company/Company.php?page=add-department&warning=$wm");
        exit();
    }
    if ($description != $_POST['description']) {
        // error
        $wm = "Invalid characters in the Department Description field";
        header("Location: ../../pages/Company/Company.php?page=add-department&warning=$wm");
        exit();
    }

    // check if this department already saved in the database
    $sql = "SELECT * FROM department_tbl WHERE (department_name=? OR department_code=?) AND company_id=?";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $dpt_name, $dpt_code, $_SESSION['company_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows < 1) {
            // no data
            $company_id = $_SESSION['company_id'];
            $sql = "INSERT INTO department_tbl (department_code, department_name, company_id, description) VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $dpt_code, $dpt_name, $company_id, $description);
            if ($stmt->execute()) {
                // success
                $stmt->close();
                $sm = "Department Registered Successfully!";
                header("Location: ../../pages/Company/Company.php?page=add-department&success=$sm");
                exit();
            } else {
                // error
                $stmt->close();
                $sem = "Unable to process the command!";
                header("Location: ../../pages/Company/Company.php?page=add-department&error=$em");
                exit();
            }
        } else {
            // data already exists
            $wm = "$dpt_name Department Already Exists!";
            header("Location: ../../pages/Company/Company.php?page=add-department&warning=$wm");
            exit();
        }
    } catch (Exception) {
        // internal server error
        $em = "Internal Server Error";
        header("Location: ../../pages/Company/Company.php?page=add-department&error=$em");
        exit();
    }
}