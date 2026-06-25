<?php
session_start();
// import the database connection class
require_once("../connection/conn.php");
// make an instance for the DBConnection class
$db = new DBConnection();
// call the connection variable using the instance
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_id'])) {
    $employee_id = $_POST['employee_id'];

    // sql query to get all the details of the selected employee
    $sql = "SELECT * FROM employee_tbl et INNER JOIN designation_tbl dsgt ON (et.designation_id=dsgt.designation_id) INNER JOIN department_tbl dpt ON (dpt.department_id=dsgt.department_id) WHERE et.status=1 AND et.company_id=? AND et.employee_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['company_id'], $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $designation_name = $row['designation_name'];
        $department_name = $row['department_name'];
        $date_joined = $row['date_joined'];
        $mobile_no = $row['mobileNo'];
        $email = $row['email'];

        $employee_details = [
            'designation_name' => $designation_name,
            'department_name' => $department_name,
            'date_joined' => $date_joined,
            'mobile_no' => $mobile_no,
            'email' => $email,
        ];
        echo json_encode($employee_details);
    } else {
        echo json_encode([]);
    }
}
?>