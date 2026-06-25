<?php
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // getting user inputs
        $company_name = $_POST['comName'];              // get company name
        $company_category_id = $_POST['com_category'];  // get company category
        $reg_no = $_POST['regNo'];                      // get company registration number
        $tin_no = $_POST['tinNo'];                      // get company TIN number
        $vat_no = $_POST['vatNo'];                      // get company VAT number
        $phone_no = $_POST['phone_no'];                 // get company phone number
        $mobile_no = $_POST['mobile_no'];               // get company mobile number
        $registered_address = $_POST['reg_address'];    // get company registered address
        $factory_address = $_POST['fac_address'];       // get company factory address
        $country_code = $_POST['country'];              // get company country code
        $currency_id = $_POST['currency_id'];           // get company currency id

        // sanitize all the user inputs and validate them
        // sanitize the company name
        if (filter_var($company_name, FILTER_SANITIZE_STRING) != $company_name) {
            // throw an exception if the company name has invalid characters
            throw new Exception("Invalid characters in the company name!");
        }
        // sanitize the company registration number
        if (filter_var($reg_no, FILTER_SANITIZE_STRING) != $reg_no) {
            // throw an exception if the registration number has invalid characters
            throw new Exception("Invalid characters in the registration number!");
        }
        // sanitize the company TIN number
        if (filter_var($tin_no, FILTER_SANITIZE_STRING) != $tin_no) {
            // throw an exception if the TIN number has invalid characters
            throw new Exception("Invalid characters in the TIN number!");
        }
        // sanitize the company VAT number
        if (filter_var($vat_no, FILTER_SANITIZE_STRING) != $vat_no) {
            // throw an exception if the VAT number has invalid characters
            throw new Exception("Invalid characters in the VAT number!");
        }

        // sanitize the company phone number
        // if (filter_var($phone_no, FILTER_SANITIZE_STRING) != $phone_no) {
        //     // throw an exception if the phone number has invalid characters
        //     throw new Exception("Invalid characters in the phone number!");
        // }
        // // sanitize the company mobile number
        // if (filter_var($mobile_no, FILTER_SANITIZE_STRING) != $mobile_no) {
        //     // throw an exception if the mobile number has invalid characters
        //     throw new Exception("Invalid characters in the mobile number!");
        // }

        // sanitize the company registered address
        if (filter_var($registered_address, FILTER_SANITIZE_STRING) != $registered_address) {
            // throw an exception if the registered address has invalid characters
            throw new Exception("Invalid characters in the registered address!");
        }
        // sanitize the company factory address
        if (filter_var($factory_address, FILTER_SANITIZE_STRING) != $factory_address) {
            // throw an exception if the factory address has invalid characters
            throw new Exception("Invalid characters in the factory address!");
        }


        // check the user has entered at least one phone number
        if (empty($phone_no) && empty($mobile_no)) {
            // if it yes then throw an exception
            throw new Exception("Please enter at least one phone number!");
        }

        // check the user has entered at least one address
        if (empty($registered_address) && empty($factory_address)) {
            // if it yes then throw an exception
            throw new Exception("Please enter at least one address!");
        }


        // get the country id using country code
        $sql = "SELECT country_id FROM country_tbl WHERE country_code='$country_code'";
        $result = $conn->query($sql);
        // check if the country code is valid or not / country code is available in database
        if ($result->num_rows > 0) {
            // get the country id from the result
            $row = $result->fetch_assoc();
            // extreact the country code from the associative array
            $country_id = $row['country_id'];

            // update the company information using the company id
            // get the current date for record the updated date of the company details
            $current_date = date("Y-m-d");
            // sql query to update the company information
            $sql = "UPDATE company_tbl SET company_name=?, company_category_id=?, company_reg_no=?, vat_no=?, tin_no=?, company_registered_address=?, company_factory_address=?, phone_no=?, mobile_no=?, date_updated=?, country_id=?, currency_id=? WHERE company_id=?";
            // prepare statement for update statement
            $stmt = $conn->prepare($sql);
            // bind the parameters to the prepared statement and execute the query
            $stmt->bind_param("sissssssssiii", $company_name, $company_category_id, $reg_no, $tin_no, $vat_no, $registered_address, $factory_address, $phone_no, $mobile_no, $current_date, $country_id, $currency_id, $_SESSION['company_id']);
            if ($stmt->execute()) {
                // company details updated successfully
                header("Location:../../pages/Company/Company.php?page=settings&success=Company details updated successfully!");
            } else {
                // throw an exception if there was an error updating the company details
                throw new Exception("Error updating company details!");
            }
        } else {
            // throw an exception if country code is not valid or doesn't exist
            throw new Exception("Invalid country code!");
        }
    } catch (Exception $e) {
        // display error message to the user and redirect to the settings page with error message
        $em = "Error: " . $e->getMessage();
        header("Location:../../pages/Company/Company.php?page=settings&error=$em");
    }

}

?>