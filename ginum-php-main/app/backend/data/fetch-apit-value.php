<?php
// Start the session
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['basic_salary'])) {
    // Import the database connection class
    require_once '../connection/conn.php';
    // Create a database instance
    $db = new DBConnection();
    // Create the database connection
    $conn = $db->conn;

    // Get the company_id from the session variable
    $company_id = $_SESSION['company_id'];

    try {
        // Get the basic salary data from the POST request
        $basic_salary = $_POST['basic_salary'];

        // Get the APIT ratio according to the basic salary
        $sql = "SELECT * FROM extended_tax_tbl WHERE company_id = ?";
        // Prepare the statement to get the APIT ratio
        $stmt = $conn->prepare($sql);
        // Bind the parameters to the statement
        $stmt->bind_param("i", $company_id);
        // Execute the statement
        $stmt->execute();
        // Get the results from the statement
        $result = $stmt->get_result();
        // Close the statement
        $stmt->close();

        // Check the salary range and return the APIT value
        $apit_value = 0; // Default to 0 (or another appropriate default value)
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Get the starting and ending values
                $starting_value = (float) $row['starting_range'];
                $ending_value = (float) $row['ending_range'];

                if ($basic_salary >= $starting_value && $basic_salary <= $ending_value) {
                    // Calculate the APIT value
                    $apit_value = $basic_salary * ($row['tax_rate'] / 100);
                    break;
                }
            }
        }
        echo $apit_value;

    } catch (Exception $e) {
        $em = "Error: " . $e->getMessage();
        echo $em;
    }
}
?>