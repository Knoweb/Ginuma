<?php
// enable sessions in this backend page
session_start();

// Enable error display and error reporting in this page
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

// check if the received request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // import the Functions file to use the makeTransactionLog() function
    require_once '../includes/Functions.php';

    // import the database connection class
    require_once '../connection/conn.php';
    // create a new instance of the DBConnection class to connect to the database
    $db = new DBConnection();
    // get the connection object from the DBConnection class
    $conn = $db->conn;

    // get all the user inputs from the frontend
    $debit_account_ids = $_POST['debit_acc_ids'];
    $credit_account_ids = $_POST['credit_acc_ids'];
    $amounts = $_POST['amounts'];
    $descriptions = $_POST['descriptions'];
    $company_id = $_SESSION['company_id'];

    // begin the transactional statement
    $conn->begin_transaction();
    try {
        for ($i = 0; $i < count($debit_account_ids); $i++) {
            $debit_account_id = $debit_account_ids[$i];
            $credit_account_id = $credit_account_ids[$i];
            $amount = $amounts[$i];
            $description = $descriptions[$i];

            // Sanitize the description field
            $description = filter_var($description, 513);

            // Validate money amount
            if (!validateMoneyAmount($amount)) {
                $wm = "Invalid characters in the amount field!";
                header("Location:../../pages/Company/Company.php?page=show-accounts&warning=$wm");
                exit();
            }

            // Validate description
            if ($description != $descriptions[$i]) {
                $wm = "Invalid characters in the description field!";
                header("Location:../../pages/Company/Company.php?page=show-accounts&warning=$wm");
                exit();
            }

            // Check the debit account's and credit account's types
            $sql = "SELECT at.account_id, sat.sub_account_id, at.account_name 
                    FROM account_tbl at 
                    INNER JOIN sub_account_tbl sat ON at.account_id=sat.account_id 
                    WHERE sat.sub_account_id=?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Could not prepare the statement: $conn->error");
            }

            // Get debit account details
            $stmt->bind_param("i", $debit_account_id);
            $stmt->execute();
            $debit_acc_result = $stmt->get_result();
            $row1 = $debit_acc_result->fetch_assoc();
            $debit_main_account_id = $row1['account_id'];
            $debit_sub_account_id = $row1['sub_account_id'];
            $debit_acc_type = $row1['account_name'];
            $stmt->close();

            // Get credit account details
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Could not prepare the statement: $conn->error");
            }
            $stmt->bind_param("i", $credit_account_id);
            $stmt->execute();
            $credit_acc_result = $stmt->get_result();
            $row2 = $credit_acc_result->fetch_assoc();
            $credit_main_account_id = $row2['account_id'];
            $credit_sub_account_id = $row2['sub_account_id'];
            $credit_acc_type = $row2['account_name'];
            $stmt->close();

            // Determine transaction types and update balances
            if ($debit_acc_type == 'Assets') {
                // Assets are increased with debit
                updateAccountBalance($conn, $debit_sub_account_id, $amount, $company_id);

                // Equity or liabilities
                if ($credit_acc_type == 'Equity' || $credit_acc_type == 'Liabilities') {
                    // Both equity and liabilities are increased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, $amount, $company_id);
                    $credit_transaction_type = determineTransactionType($credit_acc_type, 'credit');
                } else {
                    throw new Exception("Invalid credit account type for assets addition");
                }
                // log the transaction into the transaction_log_tbl table
                makeTransactionLog($conn, $company_id, $debit_sub_account_id, $credit_sub_account_id, 'Assets Addition', $amount, $description);
            } elseif ($debit_acc_type == 'Liabilities') {
                // Liabilities are decreased with debit
                updateAccountBalance($conn, $debit_sub_account_id, -$amount, $company_id);

                // Assets or equity
                if ($credit_acc_type == 'Assets' || $credit_acc_type == 'Equity') {
                    // Both assets and equity are decreased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, -$amount, $company_id);
                    $credit_transaction_type = determineTransactionType($credit_acc_type, 'credit');
                } else {
                    throw new Exception("Invalid credit account type for liabilities reduction");
                }
                // log the transactions into the transaction_log_tbl table
                makeTransactionLog($conn, $company_id, $debit_sub_account_id, $credit_sub_account_id, 'Liability Reduction', $amount, $description);
            } elseif ($debit_acc_type == 'Equity') {
                // Equity is decreased with debit
                updateAccountBalance($conn, $debit_sub_account_id, -$amount, $company_id);

                // Assets or liabilities
                if ($credit_acc_type == 'Assets' || $credit_acc_type == 'Liabilities') {
                    // Both assets and liabilities are decreased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, -$amount, $company_id);
                    $credit_transaction_type = determineTransactionType($credit_acc_type, 'credit');
                } else {
                    throw new Exception("Invalid credit account type for equity reduction");
                }
                // log the transactions into the transaction_log_tbl table
                makeTransactionLog($conn, $company_id, $debit_sub_account_id, $credit_sub_account_id, 'Equity Reduction', $amount, $description);
            } elseif ($debit_acc_type == 'Expenses') {
                // Expenses are increased with debit
                updateAccountBalance($conn, $debit_sub_account_id, $amount, $company_id);

                // Handle credit account types
                if ($credit_acc_type == 'Assets') {
                    // Assets are decreased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, -$amount, $company_id);
                    $credit_transaction_type = 'Asset Reduction';
                } elseif ($credit_acc_type == 'Liabilities') {
                    // Liabilities are increased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, $amount, $company_id);
                    $credit_transaction_type = 'Liability Addition';
                } elseif ($credit_acc_type == 'Equity') {
                    // Equity is decreased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, -$amount, $company_id);
                    $credit_transaction_type = 'Equity Reduction';
                } elseif ($credit_acc_type == 'Income') {
                    // Income is decreased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, -$amount, $company_id);
                    $credit_transaction_type = 'Income Reduction';
                } else {
                    throw new Exception("Invalid credit account type for expense");
                }
                // log the transactions into the transaction_log_tbl table
                makeTransactionLog($conn, $company_id, $debit_sub_account_id, $credit_sub_account_id, 'Expense Addition', $amount, $description);
            } elseif ($debit_acc_type == 'Income') {
                // Income is decreased with debit
                updateAccountBalance($conn, $debit_sub_account_id, -$amount, $company_id);

                // Handle credit account types
                if ($credit_acc_type == 'Assets') {
                    // Assets are increased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, $amount, $company_id);
                    $credit_transaction_type = 'Asset Addition';
                } elseif ($credit_acc_type == 'Liabilities') {
                    // Liabilities are decreased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, -$amount, $company_id);
                    $credit_transaction_type = 'Liability Reduction';
                } elseif ($credit_acc_type == 'Equity') {
                    // Equity is increased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, $amount, $company_id);
                    $credit_transaction_type = 'Equity Addition';
                } elseif ($credit_acc_type == 'Expenses') {
                    // Expenses are decreased with credit
                    updateAccountBalance($conn, $credit_sub_account_id, -$amount, $company_id);
                    $credit_transaction_type = 'Expense Reduction';
                } else {
                    throw new Exception("Invalid credit account type for income");
                }
                // log the transactions into the transaction_log_tbl table
                makeTransactionLog($conn, $company_id, $debit_sub_account_id, $credit_sub_account_id, 'Income Reduction', $amount, $description);
            } else {
                throw new Exception("Invalid debit account type");
            }
        }
        $conn->commit();
        header("Location:../../pages/Company/Company.php?page=show-accounts&success=Transaction recorded successfully");
    } catch (Exception $e) {
        $conn->rollback();
        $em = "Error: " . $e->getMessage();
        header("Location: ../../pages/Company/Company.php?page=add-transactions&error=$em");
        exit();
    }
}

function updateAccountBalance(mysqli $conn, int $sub_account_id, float $amount, int $company_id): bool
{
    // Check if the balance entry exists for sub account
    $sql = "SELECT company_sub_account_balance_id 
            FROM company_sub_account_balance 
            WHERE sub_account_id = ? AND company_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare the statement: $conn->error");
    }
    $stmt->bind_param("ii", $sub_account_id, $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();

    if ($exists) {
        // Update existing balance
        $sql = "UPDATE company_sub_account_balance SET balance = balance + ? WHERE sub_account_id = ? AND company_id=?";
    } else {
        // Insert new balance entry
        $sql = "INSERT INTO company_sub_account_balance (sub_account_id, balance, company_id) VALUES (?, ?, ?)";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare the statement: $conn->error");
    }
    if ($exists) {
        $stmt->bind_param("dii", $amount, $sub_account_id, $company_id);
    } else {
        $stmt->bind_param("idi", $sub_account_id, $amount, $company_id);
    }
    $stmt->execute();
    $stmt->close();

    return true;
}

function determineTransactionType(string $account_type, string $entry_type): string
{
    if ($entry_type == 'debit') {
        if ($account_type == 'Assets') {
            return 'Assets Addition';
        } elseif ($account_type == 'Liabilities') {
            return 'Liability Reduction';
        } elseif ($account_type == 'Equity') {
            return 'Equity Reduction';
        } elseif ($account_type == 'Expenses') {
            return 'Expense Addition';
        } elseif ($account_type == 'Income') {
            return 'Income Reduction';
        }
    } elseif ($entry_type == 'credit') {
        if ($account_type == 'Assets') {
            return 'Assets Reduction';
        } elseif ($account_type == 'Liabilities') {
            return 'Liability Addition';
        } elseif ($account_type == 'Equity') {
            return 'Equity Addition';
        } elseif ($account_type == 'Expenses') {
            return 'Expense Reduction';
        } elseif ($account_type == 'Income') {
            return 'Income Addition';
        }
    }
    return 'Unknown Transaction Type';
}
