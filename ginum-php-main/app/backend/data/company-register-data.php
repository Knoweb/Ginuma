<?php

include_once("../includes/Functions.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $company_name = $_POST['comName'];
    $company_category_id = $_POST['com_category'];
    $register_no = $_POST['regNo'] ? $_POST['reg_no'] : "";
    $tin_no = $_POST['tinNo'] ? $_POST['tin_no'] : "";
    $vatNo = $_POST['vatNo'] ? $_POST['vat_no'] : "";
    $reg_address = $_POST['reg_address'];
    $fac_address = $_POST['fac_address'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $password = $_POST['password'];
    $con_password = $_POST['con_password'];
    $country_id = $_POST['country_id'];
    $currency_id = $_POST['currency_id'];
    $phone_no = $_POST['phone_no'];
    $mobile_no = $_POST['mobile_no'];

    // validating and sanitizing user inputs
    $company_name = filter_var($company_name, FILTER_SANITIZE_STRING);
    $register_no = filter_var($register_no, FILTER_SANITIZE_STRING);
    $tin_no = filter_var($tin_no, FILTER_SANITIZE_STRING);
    $vatNo = filter_var($vatNo, FILTER_SANITIZE_STRING);
    $reg_address = filter_var($reg_address, FILTER_SANITIZE_STRING);
    $fac_address = filter_var($fac_address, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    if ($_POST['comName'] != $company_name) {
        $em = "Invalid Characters in Company Name field.";
        header("Location: ../../register.php?error=$em");
    }
    if ($_POST['regNo'] != $register_no) {
        // error
        $em = "Invalid Characters in Register field.";
        header("Location: ../../register.php?error=$em");
    }
    if ($_POST['tinNo'] != $tin_no) {
        // error
        $em = "Invalid Characters in TIN No. field.";
        header("Location: ../../register.php?error=$em");
    }
    if ($_POST['vatNo'] != $vatNo) {
        // error
        $em = "Invalid Characters in VAT No. field.";
        header("Location: ../../register.php?error=$em");
    }
    if ($_POST['reg_address'] != $reg_address) {
        // error
        $em = "Invalid Characters in Registered Address field.";
        header("Location: ../../register.php?error=$em");
    }
    if ($_POST['fac_address'] != $fac_address) {
        // error
        $em = "Invalid Characters in Factory Address field.";
        header("Location: ../../register.php?error=$em");
    }
    if ($_POST['email'] != $email) {
        // error
        $em = "Invalid Characters in Registered Address field.";
        header("Location: ../../register.php?error=$em");
    }
    if (containsScript($password)) {
        // error
        $em = "Invalid Characters in Password field.";
        header("Location: ../../register.php?error=$em");
    }

    if ($password == $con_password) {
        // ok

        $sql = "SELECT * FROM company_tbl WHERE email=? OR company_name=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            // no data
            // image processing part
            $filename = "";
            // image processing part
            // if ($_FILES['logo']['error'] < 1) {
            //     // no error. image here
            //     // extract image details
            //     $img_temp_name = $_FILES['logo']['tmp_name'];
            //     $img_size = $_FILES['logo']['size'];
            //     $img_name = $_FILES['logo']['name'];
            //     $img_type = $_FILES['logo']['type'];

            //     $temp = explode(".", $img_name);
            //     $filename = "../../assets/imgs/uploads/business_img/$company_name." . $temp[1];

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

            //     // image manipulation finished....
            // } catch (Exception) {
            //     $em = "Unable to resize the image and faild to save. Please contact the developer!";
            //     header("Location: ../../register.php?error=$em");
            // }

            try {
                // check the user has entered at least one phone number
                if (empty($phone_no) && empty($mobile_no)) {
                    // if it yes then throw an exception
                    throw new Exception("Please enter at least one phone number!");
                }

                // check the user has entered at least one address
                if (empty($reg_address) && empty($fac_address)) {
                    // if it yes then throw an exception
                    throw new Exception("Please enter at least one address!");
                }

                /*
                 * These are the status codes that company would be have to
                 * 0 - Pending -> Pending for accept or reject the request
                 * 1 - Active -> Company registration form accepted by Administrator and Active 
                 * 2 - Rejected -> Company registration form rejected by Administrator
                 * 3 - Not Paid -> Company not paid for this application
                 */

                $status = 0;
                $privilage_id = 3;
                $current_date = date("Y-m-d");
                // hashing the input password
                $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
                // insert to database
                $sql = "INSERT INTO company_tbl (company_name, company_category_id, company_reg_no, vat_no, tin_no, company_registered_address, company_factory_address, email, website_url, password, img_path, status, date_joined, country_id, currency_id, phone_no, mobile_no, privilege_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sisssssssssisiissi", $company_name, $company_category_id, $register_no, $vatNo, $tin_no, $reg_address, $fac_address, $email, $website, $hashed_pwd, $filename, $status, $current_date, $country_id, $currency_id, $phone_no, $mobile_no, $privilage_id);
                if ($stmt->execute()) {
                    // success
                    $sm = "Request Sent to the Administrator!";
                    header("Location: ../../register.php?success=$sm");
                } else {
                    // failure
                    $em = "Internal Server Error";
                    header("Location: ../../register.php?error=$em");
                }
            } catch (Exception $e) {
                $em = "Error: " . $e->getMessage();
                header("Location: ../../register.php?error=$em");
            }
            // } else {
            //     // file is too large
            //     $em = "Image file is too large (5MB maximum)";
            //     header("Location: ../../register.php?error=$em");
            // }
            // } else {
            //     // jpg or png files only
            //     $em = "Image Files only! (jpeg/ png)";
            //     header("Location: ../../register.php?error=$em");
            // }
        } else {
            $em = "Data Already Exists";
            header("Location: ../../register.php?error=$em");
            exit();
        }
    } else {
        // company already exists
        $em = "Data Already exists!";
        header("Location: ../../register.php?error=$em");
        exit();
    }
} else {
    // the passwords you entered doesn't match. Try again! 
    $em = "The passwords you entered doesn't match. Try again! ";
    header("Location: ../../register.php?error=$em");
    exit();
}
