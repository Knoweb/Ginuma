<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

function getPrimaryKeyColumnName(mysqli $conn, string $table_name): string
{
    // Prepare SQL query to find primary key column
    $sql = "
        SELECT column_name
        FROM information_schema.key_column_usage
        WHERE table_schema = DATABASE()
        AND table_name = ?
        AND constraint_name = 'PRIMARY'
    ";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the table name parameter
    $stmt->bind_param("s", $table_name);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any result is returned
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();

        // Debugging statement to check the fetched row
        // var_dump($row);

        if (isset($row['COLUMN_NAME'])) {
            return $row['COLUMN_NAME'];
        } else {
            throw new Exception("The 'COLUMN_NAME' key is missing in the result set.");
        }
    } else {
        $stmt->close();
        throw new Exception("No primary key found for table $table_name");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id']) && isset($_POST['status'])) {
    $request_id = $_POST['request_id'];
    if ($_POST['status'] == 'approved') {
        $conn->begin_transaction();
        try {
            $sql = "UPDATE edit_requests SET status='approved' WHERE request_id=? AND company_id=?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare faild: $conn->error");
            }
            $stmt->bind_param("ii", $request_id, $_SESSION['company_id']);
            $stmt->execute();
            $stmt->close();

            $sql = "SELECT new_data, table_name, record_id FROM edit_requests WHERE request_id=? AND company_id=?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare faild: $conn->error");
            }
            $stmt->bind_param("ii", $request_id, $_SESSION['company_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $record_id = $row['record_id'];
                $table_name = $row['table_name'];
                $requested_changes = json_decode(json: $row['new_data'], associative: true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON data');
                }

                $set_clause = [];
                $values = [];

                foreach ($requested_changes as $field => $value) {
                    $set_clause[] = "`$field`=?";
                    $values[] = $value;
                }

                $set_clause_string = implode(separator: ", ", array: $set_clause);
                $primary_key = getPrimaryKeyColumnName($conn, $table_name);

                $sql = "UPDATE `$table_name` SET $set_clause_string WHERE `$primary_key` = ?";
                $stmt = $conn->prepare($sql);

                if (!$stmt) {
                    throw new Exception("Prepare faild: $conn->error");
                }

                // Prepare the parameter types string
                $types = str_repeat('s', count($values)) . 'i';

                // Bind parameters dynamically
                $bind_params = array_merge($values, [$record_id]);
                $stmt->bind_param($types, ...$bind_params);

                if (!$stmt->execute()) {
                    // throw new Exception("Execute failed: " . $stmt->error);
                    throw new Exception("Execute faild: $stmt->error");
                }
                $stmt->close();
            } else {
                throw new Exception('No data found for that ID');
            }

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Edit request has been approved and successfully updated the new changes made by user!']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } elseif ($_POST['status'] == 'rejected') {
        // reject the edit request
        $sql = "UPDATE edit_requests SET status='rejected' WHERE request_id=? AND company_id=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Faild to prepare the statement");
        }
        $stmt->bind_param("ii", $request_id, $_SESSION['company_id']);
        $stmt->execute();
        $stmt->close();

        // return a success message
        echo json_encode(['status' => 'success', 'message' => 'Request has been rejected!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unknown status']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>