<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acc_id = $_POST['acc_id'];

    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $sql = "SELECT * FROM sub_account_tbl WHERE account_id=$acc_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['sub_account_id'] . "'>" . $row['sub_account_name'] . "</option>";
        }
    }
}
?>