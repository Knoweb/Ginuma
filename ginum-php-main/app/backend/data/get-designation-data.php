<?php
session_start();

require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

$dpt_id = $_POST['dpt_id'];
$sql = "SELECT * FROM designation_tbl WHERE department_id=$dpt_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $designation_id = $row['designation_id'];
        $designation_name = $row['designation_name'];
        echo "<option value='$designation_id'>$designation_name</option>";
    }
}