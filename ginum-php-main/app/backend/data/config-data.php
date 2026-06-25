<?php
session_start();
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deduction_names = $_POST['deduction_names'];
    $deduction_ratios = $_POST['deduction_ratios'];
    $company_id = $_SESSION['company_id'];

    for ($i = 0; $i < count($deduction_names); $i++) {
        $name = $deduction_names[$i];
        $ratio = $deduction_ratios[$i];

        // validating and sanitizing user inputs
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $ratio = filter_var($ratio, FILTER_SANITIZE_NUMBER_INT);

        if ($name != $deduction_names[$i]) {
            // error
            $wm = "Invalid characters in the deduction name field!";
            header("Location:../../pages/Company/Company.php?page=settings&warning=$wm");
            exit();
        }
        if ($ratio != $deduction_ratios[$i]) {
            // error
            $wm = "Invalid characters in the deduction ratio field!";
            header("Location:../../pages/Company/Company.php?page=settings&warning=$wm");
            exit();
        }

        // no issues
        $conn->begin_transaction();
        try {
            $sql = "INSERT INTO taxes_tbl (company_id, tax_name, tax_percentage) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $company_id, $name, $ratio);
            $stmt->execute();
        } catch (Exception $e) {
            // internal server error
            $conn->rollback();
            $em = "Internal Server Error";
            header("Location:../../pages/Company/Company.php?page=settings&error=$em");
            exit();
        }
    }
    $stmt->close();
    $conn->commit();
    $sm = "Deductions added successfully";
    header("Location:../../pages/Company/Company.php?page=settings&success=$sm");
    exit();
}
?>