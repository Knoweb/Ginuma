<?php
session_start();
include_once ("../includes/Functions.php");
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $gender = $_POST['gender'] ? $_POST['gender'] : "Male";
    $dob = $_POST['dob'];
    $nic = $_POST['nic'];
    $address = $_POST['address'];
    $phone = $_POST['mobileNo'];
    $email = $_POST['email'];
    $epf = $_POST['epf_no'];
    $designation_id = $_POST['designation_id'];
    $date_joined = $_POST['date_joined'];

    // sanitizing user inputs
    $fname = filter_var($fname, FILTER_SANITIZE_STRING);
    $lname = filter_var($lname, FILTER_SANITIZE_STRING);
    $address = filter_var($address, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // nic validation
    if (!checkNic($nic)) {
        // nic is  not valid
        $wm = "Invalid NIC number";
        header("Location: ../../pages/Company/Company.php?page=add-employee&warning=$wm");
        exit();
    }

    // phone no validation
    if (!preg_match('/^[0-9]{10}+$/', $phone)) {
        $wm = "Invalid Phone number";
        header("Location: ../../pages/Company/Company.php?page=add-employee&warning=$wm");
        exit();
    }

    // checking if the user has entered some unwanted inputs like script
    if ($fname != $_POST['firstName']) {
        // error
        $wm = "Invalid characters in the First Name field";
        header("Location: ../../pages/Company/Company.php?page=add-employee&warning=$wm");
        exit();
    }
    if ($lname != $_POST['lastName']) {
        // error
        $wm = "Invalid characters in the Last Name field";
        header("Location: ../../pages/Company/Company.php?page=add-employee&warning=$wm");
        exit();
    }
    if ($email != $_POST['email']) {
        // error
        $wm = "Invalid characters in the Email field";
        header("Location: ../../pages/Company/Company.php?page=add-employee&warning=$wm");
        exit();
    }
    if ($address != $_POST['address']) {
        // error
        $wm = "Invalid characters in the Address field";
        header("Location: ../../pages/Company/Company.php?page=add-employee&warning=$wm");
        exit();
    }

    // check if the employee was already saved in the database

    $sql = "SELECT * FROM employee_tbl WHERE epf_no=? OR nic=? OR mobileNo=? OR email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $epf, $nic, $phone, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows < 1) {
        $stmt->close();
        $filename = "";
        // image processing part
        // if ($_FILES['profilePicture']['error'] < 1) {
        // no error. image here
        // extract image details
        // $img_temp_name = $_FILES['profilePicture']['tmp_name'];
        // $img_size = $_FILES['profilePicture']['size'];
        // $img_name = $_FILES['profilePicture']['name'];
        // $img_type = $_FILES['profilePicture']['type'];

        // $temp = explode(".", $img_name);
        // $filename = "../../assets/imgs/uploads/employee_img/$nic." . $temp[1];
        $filename = "";

        // check the image file type (jpeg or png are allowd)
        // if ($img_type == "image/jpeg" || $img_type == "image/png") {
        // ok
        // check if the file size
        // if ($img_size <= 5000000) {
        // try {
        //     list($width, $height) = getimagesize($img_temp_name);
        //     $new_image = imagecreatetruecolor(160, 160);  // 160x160
        //     // Load the original image
        //     if ($img_type == 'image/jpeg') {
        //         $original_image = imagecreatefromjpeg($img_temp_name);
        //     } else {
        //         $original_image = imagecreatefrompng($img_temp_name);
        //     }
        //     // Resize the original image to the new dimensions
        //     imagecopyresampled($new_image, $original_image, 0, 0, 0, 0, 160, 160, $width, $height);
        //     // Save the new image 
        //     if ($img_type == 'image/jpeg') {
        //         imagejpeg($new_image, $filename);
        //     } else {
        //         imagepng($new_image, $filename);
        //     }
        //     // Free up memory 
        //     imagedestroy($original_image);
        //     imagedestroy($new_image);

        // image manipulation finished....

        // time to insert all the data into employee_tbl
        $date_added = date("Y-m-d");
        $company_id = $_SESSION['company_id'];
        $status = 1;
        try {
            $sql = "INSERT INTO employee_tbl (first_name, last_name, gender, designation_id, company_id, address, mobileNo, dob, nic, epf_no, email, date_joined, date_added, img_path, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiisssssssssi", $fname, $lname, $gender, $designation_id, $company_id, $address, $phone, $dob, $nic, $epf, $email, $date_joined, $date_added, $filename, $status);
            if ($stmt->execute()) {
                // success
                $sm = "Employee Added Successfully!";
                header("Location: ../../pages/Company/Company.php?page=add-employee&success=$sm");
                exit();
            } else {
                // faild
                $em = "Unable to execuate the command!";
                header("Location: ../../pages/Company/Company.php?page=add-employee&error=$em");
                exit();
            }
        } catch (Exception) {
            $em = "Internal Srever Error";
            header("Location: ../../pages/Company/Company.php?page=add-employee&error=$em");
            exit();
        }
        //             } catch (Exception) {
        //                 $em = "Unable to resize the image and faild to save. Please contact the developer!";
        //                 header("Location: ../../pages/Company/Company.php?page=add-employee&error=$em");
        //                 exit();
        //             }
        //         } else {
        //             $wm = "Image file is too large (5MB maximum)";
        //             header("Location: ../../pages/Company/Company.php?page=add-employee&warning=$wm");
        //             exit();
        //         }
        //     }
        // } else {
        //     $em = "Unable to resize the image and faild to save. Please contact the developer!";
        //     header("Location: ../../pages/Company/Company.php?page=add-employee&error=$em");
        //     exit();
        // }
    } else {
        // employee already exists
        $wm = "Employee Data already exists!";
        header("Location: ../../pages/Company/Company.php?page=add-employee&warning=$wm");
        exit();
    }

} else {
    // redirect to add employee 
    header("Location: ../../pages/Company/Company.php?page=add-employee");
    exit();
}
?>