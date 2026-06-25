<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // import the database connection class
    require_once '../connection/conn.php';
    // create an object using the DBConnection class
    $db = new DBConnection();
    // create a new instance from the object
    $conn = $db->conn;

    // import the required function to validate the password
    require_once '../includes/Functions.php';

    try {
        // get the user inputs from the frontend.
        $employee_id = $_POST['employee_id'];
        $password = $_POST['password'];

        // validate the password field
        if (containsScript($password)) {
            throw new Exception("Potentially harmful content detected!");
        }

        // check the selected employee is already a user (already exists in the sub_logins_tbl)
        $sql = "SELECT * FROM sub_logins_tbl WHERE company_id=? AND employee_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['company_id'], $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $wm = 'Employee already defined as a User';
            header("Location: ../../pages/Company/Company.php?page=add-user&warning=$wm");
            exit;
        }

        // hash the raw password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // get the current datetime
        $current_datetime = date("Y-m-d H:i:s");

        // insert the data into the sub_login_tbl table
        $sql = "INSERT INTO sub_logins_tbl (company_id, employee_id, password, date_added, status) VALUES (?, ?, ?, ?, 1)";

        // prepare a new statement to insert the data
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $_SESSION['company_id'], $employee_id, $hashed_password, $current_datetime);

        // execute the statement
        if ($stmt->execute()) {
            // close the statement
            $stmt->close();
            // redirect with a success message
            header("Location: ../../pages/Company/Company.php?page=add-user&success=Employee defined as a User!");
            exit;
        } else {
            // close the statement
            $stmt->close();
            throw new Exception("Failed to execute the command!");
        }
    } catch (Exception $e) {
        // redirect with an error message
        $error_message = "Error: " . $e->getMessage() . " (In Line: " . $e->getLine() . ")";
        header("Location: ../../pages/Company/Company.php?page=add-user&error=$error_message");
        exit;
    }
}
?>