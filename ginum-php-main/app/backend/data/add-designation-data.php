<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // import the database connection class
    require_once '../connection/conn.php';
    // create a new instance of the DBConnection class to connect to the database
    $db = new DBConnection();
    // get the connection object from the DBConnection class
    $conn = $db->conn;

    try {
        // get the user inputs from the frontend
        $department_id = $_POST['department_id'];
        $designation_name = $_POST['designation_name'];

        // sanitize and validate the user inputs
        // check the department_id has specified
        if (empty($department_id)) {
            // throw an exception if the department_id is not specified
            throw new Exception("Department must be specified!");
        }
        if (filter_var($designation_name, FILTER_SANITIZE_STRING) != $designation_name) {
            // throw an exception if the designation name has invalid characters
            throw new Exception("Inavlid characters in the designation name!");
        }

        // sanitizing completed successfully
        // time to insert the values to database
        $sql = "INSERT INTO designation_tbl (department_id, designation_name) VALUES (?,?)";
        // prepare the statement for the insertion query
        $stmt = $conn->prepare($sql);
        // bind the parameters into the prepared statement
        $stmt->bind_param("is", $department_id, $designation_name);
        // execute the statement
        $stmt->execute();
        // close the statement
        $stmt->close();

        // insertion completed successfully
        $sm = "Designation added successfully!";
        header("Location:../../pages/Company/Company.php?page=add-designation&success=$sm");
        exit();
    } catch (Exception $e) {
        $em = "Error: " . $e->getMessage();
        header("Location:../../pages/Company/Company.php?page=add-designation&error=$em");
        exit();
    }
}

?>