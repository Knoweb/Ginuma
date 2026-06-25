<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // project details
    $id = $_GET['id'];
    $project_code = $_POST['project_code'];
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $customer_id = $_POST['customer_id'];
    $department_id = $_POST['department_id'];
    $billing_method = $_POST['billing_method'] ? $_POST['billing_method'] : "Cash";
    $budget = $_POST['budget'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $working_status = $_POST['working_status'] ? $_POST['working_status'] : "Not Started";
    $priority = $_POST['priority'] ? $_POST['priority'] : "High";

    $new_order_data = [
        "project_code" => $project_code,
        "project_name" => $project_name,
        "description" => $project_description,
        "customer_id" => $customer_id,
        "department_id" => $department_id,
        "billing_method" => $billing_method,
        "budget" => $budget,
        "start_date" => $start_date,
        "end_date" => $end_date,
        "work_status" => $working_status,
        "priority" => $priority,
    ];

    // task details
    $taskNames = $_POST['taskNames'];
    $task_priorities = $_POST['task_priority'];
    $createdDates = $_POST['createdDates'];
    $dueDates = $_POST['dueDates'];
    $task_descriptions = $_POST['task_descriptions'];

    // validating and sanitizing data
    $project_code = filter_var($project_code, FILTER_SANITIZE_STRING);
    $project_name = filter_var($project_name, FILTER_SANITIZE_STRING);
    $project_description = filter_var($project_description, FILTER_SANITIZE_STRING);

    // Check if the entered budget is a valid number
    if (!is_numeric($budget) && !$budget >= 0) {
        // Invalid budget amount
        $wm = "Invalid budget amount";
        throw new Exception($wm);
    }
    if ($project_code != $_POST['project_code']) {
        // error
        $wm = "Invalid characters in the Order Code field";
        throw new Exception($wm);
    }
    if ($project_name != $_POST['project_name']) {
        // error
        $wm = "Invalid characters in the Order Name field";
        throw new Exception($wm);
    }
    if ($project_description != $_POST['project_description']) {
        // error
        $wm = "Invalid characters in the Order Description field";
        throw new Exception($wm);
    }

    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $conn->begin_transaction();
    try {
        // transactional statements
        // update projects tbl
        $company_id = $_SESSION['company_id'];
        if ($end_date) {
            $sql1 = "UPDATE project_tbl SET priority=?, project_code=?, project_name=?, description=?, department_id=?, billing_method=?, work_status=?, start_date=?, end_date=?, budget=?, customer_id=? WHERE project_id=?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("ssssisssssii", $priority, $project_code, $project_name, $project_description, $department_id, $billing_method, $working_status, $start_date, $end_date, $budget, $customer_id, $id);
        } else {
            $sql1 = "UPDATE project_tbl SET priority=?, project_code=?, project_name=?, description=?, department_id=?, billing_method=?, work_status=?, start_date=?, budget=?, customer_id=? WHERE project_id=?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("ssssissssii", $priority, $project_code, $project_name, $project_description, $department_id, $billing_method, $working_status, $start_date, $budget, $customer_id, $id);
        }
        $stmt1->execute();
        if ($_GET['task_length'] > 0) {
            echo "Task Length: " . $_GET['task_length'] . "<br>";
            if ($_GET['task_length'] < count($taskNames)) {
                // new task added to insert
                // first, delete the current tasks to add new task
                $task_name = end($taskNames);
                $task_prority = end($task_priorities);
                $task_created_date = end($createdDates);
                $task_due_date = end($dueDates);
                $task_description = end($task_descriptions);

                $sql2 = "INSERT INTO task_tbl (project_id, task_name, priority, created_date, due_date, description) VALUES (?,?,?,?,?,?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("isssss", $id, $task_name, $task_prority, $task_created_date, $task_due_date, $task_description);
                $stmt2->execute();

            } else {
                // update current tasks
                $url_param = $_GET['taskIds'];
                // Split the value into individual key-value pairs
                $pairs = explode(',', $url_param);
                // Create an array to store the results
                $taskIds = [];
                // Parse each key-value pair and store the values by index
                foreach ($pairs as $pair) {
                    list($index, $value) = explode('=', $pair);
                    $taskIds[(int) $index] = (int) $value;
                }

                // Loop through task details and update the task table
                foreach ($taskIds as $index => $taskId) {
                    // Check if the index exists in other arrays
                    $task_name = $taskNames[$index];
                    $task_prority = $task_priorities[$index];
                    $task_created_date = $createdDates[$index];
                    $task_due_date = $dueDates[$index];
                    $task_description = $task_descriptions[$index];
                    $sql4 = "UPDATE task_tbl SET task_name=?, priority=?, created_date=?, due_date=?, description=? WHERE task_id=?";
                    $stmt4 = $conn->prepare($sql4);
                    $stmt4->bind_param("sssssi", $task_name, $task_prority, $task_created_date, $task_due_date, $task_description, $taskId);
                    $stmt4->execute();
                    $stmt4->close();
                }
            }
        } else {
        }
        $stmt1->close();
        $conn->commit();
        $sm = "Order saved successfully!";
        header("Location:../../pages/Company/Company.php?page=show-orders&success=$sm");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        $em = "Unknown error occurred";
        header("Location: ../../pages/Company/Company.php?page=edit-order&error=$em&id=$id");
        exit();
    }
} else {
    header("Location:../../pages/Company/Company.php?page=edit-order&id=$id");
    exit();
}
?>