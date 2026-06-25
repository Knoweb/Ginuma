<?php
// start the session in this page
session_start();

// import the database connection class
require_once '../connection/conn.php';
// create a new instance of the database connection class
$db = new DBConnection();
// call the conn attribute from the database connection class constructor
$conn = $db->conn;


try {
    // get the query from the request body
    $data = json_decode(file_get_contents('php://input'), true);
    $query = $data['query'];

    // run the sql query
    $result = $conn->query($query);
    if (!$result) {
        // if any errors were encountered during execution of the query, return them as json encoded array
        echo json_encode(['error' => $conn->error]);
        exit;
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        // save the records into the array
        $row['amount'] = $_SESSION['currency_code'] . ' ' . $row['amount'];
        $rows[] = $row;
    }

    echo json_encode($rows);
} catch (Exception $e) {
    // if any errors throw while run the program runs, send the error to the frontend
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
