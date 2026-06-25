<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    // Debugging
    if (!isset($_POST['sub_account_id'])) {
        echo json_encode(["error" => "sub_account_id not set"]);
        exit;
    }

    $sub_account_id = intval($_POST['sub_account_id']);

    try {
        $sql = "SELECT * FROM sub_account_type_tbl WHERE company_id=? AND sub_account_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Could not prepare statement: " . $conn->error);
        }

        $stmt->bind_param("ii", $_SESSION['company_id'], $sub_account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sub_account_types = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sub_account_types[] = $row;
            }
            echo json_encode($sub_account_types);
        } else {
            echo json_encode([]);
        }

    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }

    $stmt->close();
}
?>