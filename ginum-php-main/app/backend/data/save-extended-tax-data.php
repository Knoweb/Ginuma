<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $starting_values = $_POST['starting_values'];
    $ending_values = $_POST['ending_values'];
    $tax_ratios = $_POST['tax_ratios'];

    $conn->begin_transaction();
    try {

        // validating and sanitizing values and checking for 0 or null values in starting_values, ending_values and tax_ratios
        for ($i = 0; $i < count($starting_values); $i++) {
            $starting_value = $starting_values[$i];
            $ending_value = $ending_values[$i];
            $tax_ratio = $tax_ratios[$i];

            // sanitizing values
            if (filter_var($starting_value, FILTER_VALIDATE_FLOAT) == false) {
                $em = "Please ensure starting values are valid numbers.";
                throw new Exception($em);
            }

            if (filter_var($ending_value, FILTER_VALIDATE_FLOAT) == false) {
                $em = "Please ensure ending values are valid numbers.";
                throw new Exception($em);
            }

            if (filter_var($tax_ratio, FILTER_VALIDATE_FLOAT) == false) {
                $em = "Please ensure tax ratios are valid numbers.";
                throw new Exception($em);
            }

            // validating values
            if ($starting_value == 0 || $starting_value == null || $ending_value == 0 || $ending_value == null || $tax_ratio == 0 || $tax_ratio == null) {
                $em = "Please ensure all fields are filled and tax ratio is a number";
                throw new Exception($em);
            }
        }

        // check if the tax rates overlap
        for ($i = 0; $i < count($starting_values) - 1; $i++) {
            for ($j = $i + 1; $j < count($starting_values) - 1; $j++) {
                if (
                    $starting_values[$i] <= $starting_values[$j] && $starting_values[$j] <= $ending_values[$i] ||
                    $starting_values[$i] <= $ending_values[$j] && $ending_values[$j] <= $starting_values[$i]
                ) {
                    $em = "Tax rates overlap. Please ensure they do not overlap.";
                    throw new Exception($em);
                }
            }
            if (
                $starting_values[$i] <= $starting_values[$j] && $starting_values[$j] <= $ending_values[$i] ||
                $starting_values[$i] <= $ending_values[$j] && $ending_values[$j] <= $starting_values[$i]
            ) {
                $em = "Tax rates overlap. Please ensure they do not overlap.";
                throw new Exception($em);
            }
        }

        for ($i = 0; $i < count($starting_values); $i++) {
            $starting_value = $starting_values[$i];
            $ending_value = $ending_values[$i];
            $tax_ratio = $tax_ratios[$i];

            $sql = "INSERT INTO extended_tax_tbl (starting_range, ending_range, tax_rate) VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Could not prepare statement");
            }
            $stmt->bind_param("ddd", $starting_value, $ending_value, $tax_ratio);
            $stmt->execute();
            $stmt->close();
        }
        $conn->commit();
        $conn->close();
        $sm = "APPIT Taxes added successfully";
        header("Location:../../pages/Company/Company.php?page=settings&success=$sm");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        $em = "An error occurred while trying to insert the tax rates: " . $e->getMessage();
        header("Location:../../pages/Company/Company.php?page=settings&error=$em");
        exit();
    }
}

?>