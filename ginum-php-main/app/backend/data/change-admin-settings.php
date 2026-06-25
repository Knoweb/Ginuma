<?php
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // import the functions
    require_once '../includes/Functions.php';
    // import the database connection class
    require_once '../connection/conn.php';
    // create a new instance of the DBConnection class to connect to the database
    $db = new DBConnection();
    // get the connection object from the DBConnection class
    $conn = $db->conn;

    // super admin id
    $admin_id = $_SESSION['s_admin_id'];

    // get the name and email from the sessions
    $admin_email = $_POST['admin_email'];
    $admin_name = $_POST['admin_name'];

    // getting user inputs from the frontend
    try {
        if (!empty($_POST['admin_old_password'])) {
            if (!empty($_POST['admin_new_password']) && !empty($_POST['admin_con_new_password'])) {
                if (!containsScript($_POST['admin_old_password']) && !containsScript($_POST['admin_new_password']) && !containsScript($_POST['admin_con_new_password'])) {
                    $admin_old_password = $_POST['admin_old_password'];
                    $admin_new_password = $_POST['admin_new_password'];
                    $admin_con_new_password = $_POST['admin_con_new_password'];

                    // get the super admin data for check the old passwords
                    $sql = "SELECT * FROM super_admin_tbl WHERE s_admin_id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $admin_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    if ($result->num_rows > 0) {
                        // get the password from the super_admin_tbl
                        $row = $result->fetch_assoc();
                        $password = $row['s_admin_password'];
                        // check the old password in the database and the old password entered by user
                        if (password_verify($admin_old_password, $password)) {
                            // if it yes, check the new password and the confirmed passwords are equal or not
                            if ($admin_new_password == $admin_con_new_password) {
                                // update the admin's email and password
                                $sql = "UPDATE super_admin_tbl SET s_admin_name=?, s_admin_username=?, s_admin_password=? WHERE s_admin_id=?";
                                $hashed_new_password = password_hash($admin_new_password, PASSWORD_DEFAULT);
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("sssi", $admin_name, $admin_email, $hashed_new_password, $admin_id);
                                if ($stmt->execute()) {
                                    // success message
                                    $sm = "Admin details updated successfully!";
                                    header("Location:../../pages/logout.php?msg=$sm");
                                    exit();
                                } else {
                                    throw new Exception("Unable to execute the command!");
                                }
                            } else {
                                // passwords are not matching
                                throw new Exception("Passwords do not match");
                            }
                        } else {
                            // invalid password
                            throw new Exception("Invalid Old Password");
                        }
                    } else {
                        throw new Exception("Internal Server Error");
                    }
                } else {
                    throw new Exception("Invalid characters in the Password fields");
                }
            } else {
                throw new Exception("All password fields are required");
            }
        } else {
            $sql = "UPDATE super_admin_tbl SET s_admin_name=?, s_admin_username=? WHERE s_admin_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $admin_name, $admin_email, $admin_id);

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
        $em = "Error: " . $e->getMessage();
        header("Location: ../../pages/Admin/Admin.php?page=settings&error=$em");
    }
}

?>