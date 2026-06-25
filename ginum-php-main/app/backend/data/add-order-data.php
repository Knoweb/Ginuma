<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    // project details
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
        header("Location: ../../pages/Company/Company.php?page=add-project&warning=$wm");
        exit();
    }
    
    // checking if  project already exists in the database
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    

    $sql = "SELECT * FROM project_tbl WHERE project_code=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $project_code);
    $stmt->execute();
    $result = $stmt->get_result();

    $conn->begin_transaction();
    if ($result->num_rows < 1) {
        try {
            // insert data into projects tbl
            $company_id = $_SESSION['company_id'];
            $end_date = !empty($end_date) ? $end_date : null;
            
            $sql1 = "INSERT INTO project_tbl (priority, project_code, project_name, description, department_id, company_id, billing_method, work_status, start_date, end_date, budget, customer_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("ssssiissssdi", $priority, $project_code, $project_name, $project_description, $department_id, $company_id, $billing_method, $working_status, $start_date, $end_date, $budget, $customer_id);
             
            $stmt1->execute();
            $last_id = $conn->insert_id;

            // inserting data into task_tbl
            if (count($taskNames) > 0) {
                for ($i = 0; $i < count($taskNames); $i++) {
                    $task_name = $taskNames[$i];
                    $task_prority = $task_priorities[$i];
                    $task_created_date = $createdDates[$i];
                    $task_due_date = $dueDates[$i];
                    $task_description = $task_descriptions[$i];
                    $sql2 = "INSERT INTO task_tbl (project_id, task_name, priority, created_date, due_date, description) VALUES (?,?,?,?,?,?)";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("isssss", $last_id, $task_name, $task_prority, $task_created_date, $task_due_date, $task_description);
                    $stmt2->execute();
                }
                $stmt2->close();
            } else {
            }
            $conn->commit();
            $stmt1->close();
            $sm = "Order saved successfully!";
            header("Location: ../../pages/Company/Company.php?page=add-project&success=$sm");
            exit();
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $em = "Unknown error occurred";
            header("Location: ../../pages/Company/Company.php?page=add-project&error=$em");
            exit();
        }
    } else {
        // data already exists
        $wm = "Project details already exists";
        header("Location: ../../pages/Company/Company.php?page=add-project&warning=$wm");
        exit();
    }

} else {
    // redirect to add project page
    header("Location: ../../pages/Company/Company.php?page=show-projects");
    exit();
}
?>