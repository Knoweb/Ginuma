<?php

function updateOrInsertAccountBalance(mysqli $conn, int $company_id, int $sub_account_id, float $amount, bool $isExpense = true): void
{
    $sql = "SELECT * FROM company_sub_account_balance WHERE sub_account_id=? AND company_id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare statement");
    }
    $stmt->bind_param("ii", $sub_account_id, $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        // Update balance
        $sql = "UPDATE company_sub_account_balance SET balance=balance" . ($isExpense ? "+" : "-") . "? WHERE sub_account_id=? AND company_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Could not prepare statement");
        }
        $stmt->bind_param("dii", $amount, $sub_account_id, $company_id);
    } else {
        // Insert new balance
        $sql = "INSERT INTO company_sub_account_balance (company_id, sub_account_id, balance) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Could not prepare statement");
        }
        $balance = $isExpense ? $amount : -$amount;
        $stmt->bind_param("iid", $company_id, $sub_account_id, $balance);
    }
    $stmt->execute();
    $stmt->close();
}

function getAccountId(mysqli $conn, int $sub_account_id): mysqli_result
{
    $sql = "SELECT * FROM sub_account_tbl sat INNER JOIN account_tbl at ON sat.account_id = at.account_id WHERE sat.sub_account_id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare statement");
    }
    $stmt->bind_param("i", $sub_account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

function handleTransferAccount(mysqli $conn, int $company_id, int $transfer, float $amount, string $notes): void
{
    $result = getAccountId($conn, $transfer);

    if ($result->num_rows == 0) {
        throw new Exception("Invalid transfer account");
    }

    $row = $result->fetch_assoc();
    $account_name = $row['account_name'];
    $account_id = $row['account_id'];
    $sub_account_id = $row['sub_account_id'];

    // Update or insert balance for assets or liabilities
    if ($account_name == 'Assets') {
        updateOrInsertAccountBalance($conn, $company_id, $sub_account_id, $amount, false);
    } elseif ($account_name == 'Liabilities') {
        updateOrInsertAccountBalance($conn, $company_id, $sub_account_id, $amount, true);
    } else {
        throw new Exception("Unknown account type");
    }
}

session_start();
include("../includes/Functions.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $date_added = $_POST['date'];
    $amount = $_POST['amount'];
    $expenseAcc = $_POST['expenseAcc'];
    $transfer = $_POST['transfer'];
    $reference = $_POST['reference'];
    $notes = $_POST['notes'];

    // Sanitize user inputs
    $notes = filter_var($notes, FILTER_SANITIZE_STRING);

    // Validate inputs
    if ($notes != $_POST['notes']) {
        $wm = "Invalid characters in the Notes field";
        header("Location:../../pages/Company/Company.php?page=new-expense&warning=$wm");
        exit();
    }

    if (!validateMoneyAmount($amount)) {
        $wm = "Invalid characters in the Money Amount field";
        header("Location:../../pages/Company/Company.php?page=new-expense&warning=$wm");
        exit();
    }

    $conn->begin_transaction();
    try {
        $company_id = $_SESSION['company_id'];
        $status = 1;
        $target_file = null;

        // Handle file upload if provided
        if ($_FILES['bill']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../../assets/docs/uploads/bills/";
            $target_file = $target_dir . basename($_FILES["bill"]["name"]);
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    throw new Exception("Failed to create directories.");
                }
            }

            if ($fileType != "pdf") {
                throw new Exception("Sorry, only PDF files are allowed.");
            }

            if (!move_uploaded_file($_FILES["bill"]["tmp_name"], $target_file)) {
                $error_message = error_get_last();
                throw new Exception("Unable to upload the bill to the server. Please try again. Error details: " . print_r($error_message, true));
            }
        }

        // Split the transfer value into debit_acc_id and sub_account_id
        list($debit_acc_id, $transfer_sub_account_id) = explode(":", $transfer);

        // Ensure they are integers
        $debit_acc_id = (int)$debit_acc_id;
        $transfer_sub_account_id = (int)$transfer_sub_account_id;

        // Insert expense
        $sql = "INSERT INTO expense_tbl (company_id, sub_account_id, amount, reference, note, date_added, " . ($target_file ? "bill_img_path, " : "") . "status) 
                VALUES (?,?,?,?,?,?" . ($target_file ? ",?" : "") . ",?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Could not prepare statement");
        }

        if ($target_file) {
            $stmt->bind_param("iidssssi", $company_id, $expenseAcc, $amount, $reference, $notes, $date_added, $target_file, $status);
        } else {
            $stmt->bind_param("iidsssi", $company_id, $expenseAcc, $amount, $reference, $notes, $date_added, $status);
        }
        $stmt->execute();
        $stmt->close();

        // Update or insert sub account balance
        updateOrInsertAccountBalance($conn, $company_id, $expenseAcc, $amount, true);

        // Handle transfer account
        handleTransferAccount($conn, $company_id, $transfer_sub_account_id, $amount, $notes);

        // Log the transaction
        if (!makeTransactionLog($conn, $company_id, $debit_acc_id, $expenseAcc, $transfer_sub_account_id, "Expense Addition", (float)$amount, $notes)) {
        throw new Exception("Transaction logging failed!");
        }

        // Commit transaction and redirect
        $conn->commit();
        $sm = "Expense recorded Successfully!";
        header("Location:../../pages/Company/Company.php?page=new-expense&success=$sm");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $em = "Internal Server Error: " . $e->getMessage();
        header("Location:../../pages/Company/Company.php?page=new-expense&error=$em");
        exit();
    }
}
?>