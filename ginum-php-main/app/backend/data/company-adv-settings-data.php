<?php
include_once ("../includes/Functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = $_GET['id'];
    $email = $_POST['email'];

    // Validate and sanitize company email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Check if the email sanitization removed any characters
    if ($email != $_POST['email']) {
        $wm = "Invalid characters in the Email field";
        header("Location: ../../pages/Company/Company.php?page=settings&warning=$wm");
        exit();
    }

    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $conn->begin_transaction();

    try {
        if (!empty($_POST['old_password'])) {
            if (!empty($_POST['new_password']) && !empty($_POST['con_new_password'])) {
                if (!containsScript($_POST['old_password']) && !containsScript($_POST['new_password']) && !containsScript($_POST['con_new_password'])) {
                    $old_password = $_POST['old_password'];
                    $new_password = $_POST['new_password'];
                    $con_new_password = $_POST['con_new_password'];

                    $sql = "SELECT * FROM company_tbl WHERE company_id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $company_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $password = $row['password'];
                        if (password_verify($old_password, $password)) {
                            if ($new_password == $con_new_password) {
                                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                                $sql = "UPDATE company_tbl SET password=?, email=? WHERE company_id=?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ssi", $hashed_new_password, $email, $company_id);

                                if ($stmt->execute()) {
                                    $conn->commit();
                                    $sm = "Login Settings saved. Now you can login!";
                                    header("Location: ../../pages/logout.php?msg=$sm");
                                    exit();
                                } else {
                                    throw new Exception("Failed to update the database");
                                }
                            } else {
                                $wm = "Passwords do not match";
                                header("Location: ../../pages/Company/Company.php?page=settings&warning=$wm");
                                exit();
                            }
                        } else {
                            $wm = "Old password is incorrect";
                            header("Location: ../../pages/Company/Company.php?page=settings&warning=$wm");
                            exit();
                        }
                    }
                } else {
                    $wm = "Invalid characters in the Password fields";
                    header("Location: ../../pages/Company/Company.php?page=settings&warning=$wm");
                    exit();
                }
            } else {
                $wm = "All password fields are required";
                header("Location: ../../pages/Company/Company.php?page=settings&warning=$wm");
                exit();
            }
        } else {
            $sql = "UPDATE company_tbl SET email=? WHERE company_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $email, $company_id);

            if ($stmt->execute()) {
                $conn->commit();
                $sm = "Login Settings saved. Now you can login!";
                header("Location: ../../pages/logout.php?msg=$sm");
                exit();
            } else {
                throw new Exception("Failed to update the database");
            }
        }
    } catch (Exception $e) {
        $conn->rollback();
        $em = "Internal Server Error";
        header("Location: ../../pages/Company/Company.php?page=settings&error=$em");
        exit();
    }
}
?>