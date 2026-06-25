<?php
session_start();
include_once("../includes/Functions.php");
// Enable error display
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sanitizing and validation
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    if ($email != $_POST['email']) {
        $em = "Invalid characters in the email address";
        header("Location: ../../login.php?error=" . urlencode($em));
        exit();
    }

    if (containsScript($password)) {
        $em = "Unwanted scripts in the password field";
        header("Location: ../../login.php?error=" . urlencode($em));
        exit();
    }

    if (empty($password)) {
        $em = "Password cannot be empty";
        header("Location: ../../login.php?error=" . urlencode($em));
        exit();
    }

    $sql = "SELECT * FROM company_tbl ct INNER JOIN privilege_tbl pt ON (ct.privilege_id = pt.privilege_id) WHERE ct.email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $currency_id = $row['currency_id'];
            $sql2 = "SELECT * FROM currency_tbl WHERE currency_id=?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("i", $currency_id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            if ($result2->num_rows > 0) {
                $row2 = $result2->fetch_assoc();
                $_SESSION['currency_code'] = $row2['currency_code'];

                /*
                 * These are the status codes that company would be have to
                 * 0 - Pending -> Pending for accept or reject the request
                 * 1 - Active -> Company registration form accepted by Administrator and Active 
                 * 2 - Rejected -> Company registration form rejected by Administrator
                 * 3 -> Not Paid -> Company not paid for this application
                 */

                // Check the status of the company
                switch ((int) $row['status']) {
                    case 0:
                        // Pending
                        $em = "Your registration request is still pending approval by the administrator.";
                        header("Location: ../../login.php?info=" . urlencode($em));
                        break;
                    case 1:
                        // Active
                        $_SESSION['role'] = 'Company Admin';
                        $_SESSION['company_id'] = $row['company_id'];
                        $_SESSION['company_name'] = $row['company_name'];
                        $_SESSION['img'] = $row['img_path'];
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['country_id'] = $row['country_id'];
                        $_SESSION['privilege_name'] = $row['privilege_name'];
                        header('Location: ../../pages/Company/Company.php');
                        break;
                    case 2:
                        // Rejected
                        $em = "Your registration request has been rejected by the administrator.";
                        header("Location: ../../login.php?error=" . urlencode($em));
                        break;
                    case 3:
                        // Not Paid
                        $em = "Your application has not been paid yet. Please pay the required amount.";
                        header("Location: ../../login.php?warning=" . urlencode($em));
                        break;
                    default:
                        echo "Invalid status code.";
                }
                $stmt2->close();
                $stmt->close();
                exit();
            } else {
                $stmt2->close();
                $stmt->close();
                echo "No Data Available";
                exit();
            }
        } else {
            $stmt->close();
            $em = "Invalid Password";
            header("Location: ../../login.php?error=" . urlencode($em));
            exit();
        }
    } else {
        // Check if the email belongs to the super admin
        $sql3 = "SELECT * FROM super_admin_tbl WHERE s_admin_username=?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("s", $email);
        $stmt3->execute();
        $result3 = $stmt3->get_result();

        if ($result3->num_rows == 1) {
            $row = $result3->fetch_assoc();
            if (password_verify($password, $row['s_admin_password'])) {
                // Update the last login and redirect to the super admin dashboard
                $timestamp = date('Y-m-d H:i:s');
                $sql2 = "UPDATE super_admin_tbl SET last_login=? WHERE s_admin_id=?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("si", $timestamp, $row['s_admin_id']);
                $stmt2->execute();
                $stmt2->close();

                $_SESSION['role'] = 'Admin';
                $_SESSION['s_admin_id'] = $row['s_admin_id'];
                $_SESSION['s_admin_username'] = $row['s_admin_username'];
                $_SESSION['s_admin_name'] = $row['s_admin_name'];
                header('Location: ../../pages/Admin/Admin.php');
                exit();
            } else {
                $em = "Invalid Password";
                header("Location: ../../login.php?error=" . urlencode($em));
                exit();
            }
        } else {
            $sql = "SELECT * FROM sub_logins_tbl slt INNER JOIN employee_tbl et ON (slt.employee_id=et.employee_id) INNER JOIN company_tbl ct ON (ct.company_id=et.company_id) INNER JOIN privilege_tbl pt ON (ct.privilege_id = pt.privilege_id) WHERE et.status=1 AND slt.status=1 AND et.email=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $timestamp = date('Y-m-d H:i:s');
                    $sql2 = "UPDATE sub_logins_tbl SET last_login=? WHERE sub_login_id=?";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("si", $timestamp, $row['sub_login_id']);
                    $stmt2->execute();
                    $stmt2->close();

                    $currency_id = $row['currency_id'];
                    $sql3 = "SELECT * FROM currency_tbl WHERE currency_id=?";
                    $stmt3 = $conn->prepare($sql3);
                    $stmt3->bind_param("i", $currency_id);
                    $stmt3->execute();
                    $result3 = $stmt3->get_result();
                    $stmt3->close();

                    $row2 = $result3->fetch_assoc();

                    $_SESSION['currency_code'] = $row2['currency_code'];

                    /*
                     * These are the status codes that company would be have to
                     * 0 - Pending -> Pending for accept or reject the request
                     * 1 - Active -> Company registration form accepted by Administrator and Active 
                     * 2 - Rejected -> Company registration form rejected by Administrator
                     * 3 -> Not Paid -> Company not paid for this application
                     */

                    // Check the status of the company
                    switch ((int) $row['status']) {
                        case 0:
                            // Pending
                            $em = "Your registration request is still pending approval by the administrator.";
                            header("Location: ../../login.php?info=" . urlencode($em));
                            break;
                        case 1:
                            // Active
                            $_SESSION['role'] = 'User';
                            $_SESSION['sub_login_id'] = $row['sub_login_id'];
                            $_SESSION['company_id'] = $row['company_id'];
                            $_SESSION['company_name'] = $row['company_name'];
                            $_SESSION['img'] = $row['img_path'];
                            $_SESSION['email'] = $row['email'];
                            $_SESSION['country_id'] = $row['country_id'];
                            $_SESSION['privilege_name'] = $row['privilege_name'];
                            header('Location: ../../pages/Company/Company.php');
                            break;
                        case 2:
                            // Rejected
                            $em = "Your registration request has been rejected by the administrator.";
                            header("Location: ../../login.php?error=" . urlencode($em));
                            break;
                        case 3:
                            // Not Paid
                            $em = "Your application has not been paid yet. Please pay the required amount.";
                            header("Location: ../../login.php?warning=" . urlencode($em));
                            break;
                        default:
                            echo "Invalid status code.";
                    }
                } else {
                    $em = "Invalid Password";
                    header("Location: ../../login.php?error=" . urlencode($em));
                    exit();
                }
            } else {
                $em = "Invalid Email";
                header("Location: ../../login.php?error=" . urlencode($em));
                exit();
            }
        }
    }
}
?>