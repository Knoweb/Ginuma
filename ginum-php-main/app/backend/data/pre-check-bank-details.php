<?php
// Start session only if it’s not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../backend/connection/conn.php';

$db = new DBConnection();
$conn = $db->conn;

try {
    // Check if bank details exist
    $company_id = $_SESSION['company_id'];
    $sql = "SELECT * FROM bank_details_tbl WHERE company_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bank_account_row = $result->fetch_assoc();

    if (!$bank_account_row) {
        // Display SweetAlert if no bank details are found
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Bank Details Missing',
                text: 'Please set up your bank details before creating a quotation.',
                confirmButtonText: 'Add Bank Account',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../pages/Company/Company.php?page=add-bank-account';
                }
            });
        </script>";
        exit(); // Halt execution
    }
} catch (Exception $e) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while loading the page. Please try again.',
        });
    </script>";
    exit();
}
?>
