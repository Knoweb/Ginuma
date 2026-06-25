<?php

// enable session for this page
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // import the database connection class
    require_once '../connection/conn.php';
    // create a new instance of the DBConnection class
    $db = new DBConnection();
    // call the connection attribute
    $conn = $db->conn;

    // get the user inputs from the frontend
    $sub_account_type_id = $_POST['sub_account_type_id'];
    $useful_life_years = $_POST['useful_life_years'];
    $salvage_value = (float) $_POST['salvage_value'];
    $depreciation_rate = (float) $_POST['depreciation_rate'];
    $accumulated_depreciation = (float)$_POST['accumulated_depreciation'];
    $remaining_life_years = $_POST['remaining_life_years'];
    $depreciation_method = $_POST['depreciation_method'];
    $notes = $_POST['notes'];

    // sanitizing and validate the user inputs
    // if($_PO)
}
