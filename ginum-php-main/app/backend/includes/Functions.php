<?php
/**
 * Validates a Sri Lankan National Identity Card (NIC) number.
 *
 * This function checks if a given string represents a valid Sri Lankan NIC number.
 * It accepts both old (10-digit) and new (12-digit) NIC numbers.
 *
 * @param string $nic The NIC number to validate.
 * @return bool True if the NIC number is valid, false otherwise.
 */
function checkNic(string $nic): bool
{
    $nic = trim($nic); // Trim any extra spaces

    // Check for 10-digit NIC (Old format)
    if (strlen($nic) == 10) {
        $lastLetter = strtolower($nic[9]); // Convert to lowercase to simplify comparison

        if ($lastLetter === 'v' || $lastLetter === 'x') {
            $year = (int) substr(string: $nic, offset: 0, length: 2);
            $monthDay = (int) substr(string: $nic, offset: 2, length: 3);

            // The year should be in the range of 0-99 (assuming current century is 2000)
            if ($year > 0 && $monthDay > 0 && $monthDay <= 866) {
                return true;
            }
        }
    }
    // Check for 12-digit NIC (New format)
    elseif (strlen($nic) == 12) {
        $year = (int) substr(string: $nic, offset: 0, length: 4);
        $monthDay = (int) substr(string: $nic, offset: 4, length: 3);

        // Year should be within a reasonable range
        $currentYear = (int) date("Y");
        if ($year >= 1900 && $year <= $currentYear - 15 && $monthDay > 0 && $monthDay <= 866) {
            return true;
        }
    }

    return false;
}


/**
 * Validates a money amount.
 *
 * This function checks if a given string represents a valid money amount.
 * It accepts both positive and negative values, and allows up to 2 decimal places.
 *
 * @param string $amount The money amount to validate.
 * @return bool True if the amount is valid, false otherwise.
 */
function validateMoneyAmount($amount): bool
{
    // Remove leading/trailing whitespace
    $cleanedAmount = trim($amount);

    // Regular expression for validating money amount
    $pattern = '/^-?\d+(\.\d{1,2})?$/';

    // Check if the cleaned amount matches the pattern
    if (preg_match($pattern, $cleanedAmount)) {
        // Convert to float and format it with 2 decimal places
        $formattedAmount = number_format((float) $cleanedAmount, 2, '.', '');

        // Compare the formatted amount with the cleaned amount
        return $cleanedAmount == $formattedAmount;
    }

    return false;
}


/**
 * Logs a transaction into the database.
 *
 * This function logs a transaction into the transaction_log_tbl table using the provided database connection.
 * It prepares an SQL statement, binds parameters, executes the statement, and handles any errors that may occur.
 *
 * @param mysqli $conn The database connection object.
 * @param int $company_id The ID of the company associated with the transaction.
 * @param int $debit_acc_id The ID of the debit account.
 * @param int $credit_acc_id The ID of the credit account.
 * @param int $sub_account_id The ID of the sub account.
 * @param string $transaction_type The type of transaction.
 * @param float $amount The amount of the transaction.
 * @param string $description A description of the transaction.
 *
 * @return bool True if the transaction is logged successfully, false otherwise.
 *
 * @throws Exception If there is an error preparing the statement or executing it.
 */
function makeTransactionLog(mysqli $conn, int $company_id, int $debit_acc_id, int $credit_acc_id, int $sub_account_id, string $transaction_type, float $amount, string $description): bool
{
    try {
        // Prepare the SQL statement for logging the transaction
        $sql = "INSERT INTO transaction_log_tbl (company_id, debit_account, credit_account, sub_account_id, transaction_type, amount, description, transaction_date) VALUES (?,?,?,?,?,?,?,NOW())";
        $stmt = $conn->prepare($sql);

        // Check if the statement was prepared correctly
        if ($stmt === false) {
            throw new Exception(message: "Could not prepare the statement: $conn->error");
        }

        // Bind parameters
        $stmt->bind_param("iiiisds", $company_id, $debit_acc_id, $credit_acc_id, $sub_account_id, $transaction_type, $amount, $description);

        // Execute the statement and check for errors
        if (!$stmt->execute()) {
            throw new Exception(message: "Execute failed: $stmt->error");
        }

        // Close the statement
        $stmt->close();
        return true;

    } catch (Exception $e) {
        // Log the error message
        error_log("Error: " . $e->getMessage() . " In Line: " . $e->getLine());
        return false;
    }
}


/**
 * Checks if the input string contains script tags or potentially harmful content.
 *
 * @param string $input The input string to check.
 * @return bool True if the input contains script tags or potentially harmful content, false otherwise
 *
 * @since 1.0.0
 */
function containsScript(string $input): bool
{
    // Check if the input contains script tags or potentially harmful content
    $pattern = "/<script|<\/script|<\?php|<\?|eval\(|system\(|exec\(|passthru\(|shell_exec\(|popen\(|proc_open\(/i";

    // Perform the regular expression match and return the result
    return preg_match($pattern, $input) === 1;
}