<?php
require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Assuming you have a field named 'unit_price' in your item_tbl table
    $sql = "SELECT unit_price FROM item_tbl it INNER JOIN inventory_tbl invt ON (it.item_id=invt.item_id) WHERE it.item_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $unitPrice = $row['unit_price'];

        // Return the data as JSON
        echo json_encode(['unitPrice' => $unitPrice]);
    } else {
        // echo json_encode(['unitPrice' => 0]);
        $sql = "SELECT budget FROM project_tbl WHERE project_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $unitPrice = $row['budget'];
            echo json_encode(['unitPrice' => $unitPrice]);
        } else {
            echo json_encode(['unitPrice' => 0]);
        }
    }

    $stmt->close();
}
?>