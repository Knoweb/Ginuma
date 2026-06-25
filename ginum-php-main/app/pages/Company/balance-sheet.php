<?php
$total_assets = 0;
$total_liabilities = 0;
$total_equities = 0;
$total_income = 0;
$total_expense = 0;

// get the company details from the company id
$sql = "SELECT * FROM company_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
$result = $conn->query($sql);
$company_row = $result->fetch_assoc();
?>

<div class="container mt-5">
    <h1 style="text-align: center;"><?= $company_row['company_name'] ?></h1>
    <div class="text-center mb-4">
        <h3>BALANCE SHEET REPORT</h3>
        <h4 style="text-align: center;"><?= $company_row['company_registered_address'] ?></h4>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ACCOUNT</th>
                <th>TOTAL (<?= $_SESSION['currency_code'] ?>)</th>
            </tr>
        </thead>
        <tbody>
            <!-- ASSETS Section -->
            <tr>
                <td colspan="2"><strong>ASSETS</strong></td>
            </tr>
            <tr>
                <td><strong>Current Assets</strong></td>
                <td></td>
            </tr>
            <?php
            // Current Assets
            $sql = "SELECT 
                        sat.sub_account_name, 
                        sat2.type_name AS sub_account_type_name, 
                        COALESCE(csat.balance, 0) AS balance
                    FROM sub_account_tbl sat
                    LEFT JOIN sub_account_type_tbl sat2 ON sat.sub_account_id = sat2.sub_account_id 
                    LEFT JOIN company_sub_account_balance csat ON csat.sub_account_id = sat.sub_account_id 
                        AND csat.sub_account_type_id IS NULL 
                    WHERE sat.account_type = 'Current' 
                    AND COALESCE(csat.balance, 0) > 0 
                    AND csat.company_id = '" . $_SESSION['company_id'] . "' 
                    ORDER BY sat.sub_account_name, sat2.type_name";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_assets += (float) $row['balance'];
            ?>
                    <tr>
                        <td><?= $row['sub_account_name'] . ($row['sub_account_type_name'] != null ? " (" . $row['sub_account_type_name'] . ")" : "") ?></td>
                        <td class="text-end"><?= number_format($row['balance'], 2) ?></td>
                    </tr>
            <?php
                }
            }
            ?>
            <tr>
                <td><strong>Fixed Assets</strong></td>
                <td></td>
            </tr>
            <?php
            // Fixed Assets
            $sql = "SELECT 
                        sat.sub_account_name, 
                        sat2.type_name AS sub_account_type_name, 
                        COALESCE(csat.balance, 0) AS balance
                    FROM sub_account_tbl sat
                    LEFT JOIN sub_account_type_tbl sat2 ON sat.sub_account_id = sat2.sub_account_id 
                    LEFT JOIN company_sub_account_balance csat ON csat.sub_account_id = sat.sub_account_id 
                        AND csat.sub_account_type_id IS NULL 
                    WHERE sat.account_type = 'Non-Current' 
                    AND COALESCE(csat.balance, 0) > 0 
                    AND csat.company_id = '" . $_SESSION['company_id'] . "' 
                    ORDER BY sat.sub_account_name, sat2.type_name";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_assets += (float) $row['balance'];
            ?>
                    <tr>
                        <td><?= $row['sub_account_name'] . ($row['sub_account_type_name'] != null ? " (" . $row['sub_account_type_name'] . ")" : "") ?></td>
                        <td class="text-end"><?= number_format($row['balance'], 2) ?></td>
                    </tr>
            <?php
                }
            }
            ?>
            <tr>
                <td><strong>TOTAL ASSETS</strong></td>
                <td class="text-end">
                    <b><?= $_SESSION['currency_code'] . " " . number_format($total_assets, 2) ?></b>
                </td>
            </tr>

            <!-- LIABILITIES Section -->
            <tr>
                <td colspan="2"><strong>LIABILITIES</strong></td>
            </tr>
            <tr>
                <td><strong>Current Liabilities</strong></td>
                <td></td>
            </tr>
            <?php
            // Current Liabilities
            $sql = "SELECT * FROM sub_account_tbl sat 
                    INNER JOIN company_sub_account_balance csat 
                    ON sat.sub_account_id = csat.sub_account_id 
                    WHERE sat.account_type = 'Current' 
                    AND csat.company_id = '" . $_SESSION['company_id'] . "' 
                    AND sat.account_id = (SELECT account_id FROM account_tbl WHERE account_name = 'Liabilities')";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_liabilities += (float) $row['balance'];
            ?>
                    <tr>
                        <td><?= $row['sub_account_name'] ?></td>
                        <td class="text-end"><?= number_format($row['balance'], 2) ?></td>
                    </tr>
            <?php
                }
            }
            ?>
            <tr>
                <td><strong>Long Term Liabilities</strong></td>
                <td></td>
            </tr>
            <?php
            // Long-term Liabilities
            $sql = "SELECT * FROM sub_account_tbl sat 
                    INNER JOIN company_sub_account_balance csat 
                    ON sat.sub_account_id = csat.sub_account_id 
                    WHERE sat.account_type = 'Non-Current' 
                    AND csat.company_id = '" . $_SESSION['company_id'] . "' 
                    AND sat.account_id = (SELECT account_id FROM account_tbl WHERE account_name = 'Liabilities')";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_liabilities += (float) $row['balance'];
            ?>
                    <tr>
                        <td><?= $row['sub_account_name'] ?></td>
                        <td class="text-end"><?= number_format($row['balance'], 2) ?></td>
                    </tr>
            <?php
                }
            }
            ?>
            <tr>
                <td><strong>TOTAL LIABILITIES</strong></td>
                <td class="text-end">
                    <b><?= $_SESSION['currency_code'] . " " . number_format($total_liabilities, 2) ?></b>
                </td>
            </tr>

            <!-- EQUITIES Section -->
            <tr>
                <td colspan="2"><strong>EQUITY</strong></td>
            </tr>
            <?php
            // Equity
            $sql = "SELECT * FROM sub_account_tbl sat 
                    INNER JOIN company_sub_account_balance csat 
                    ON sat.sub_account_id = csat.sub_account_id 
                    WHERE csat.company_id = '" . $_SESSION['company_id'] . "' 
                    AND sat.account_id = (SELECT account_id FROM account_tbl WHERE account_name = 'Equity')";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_equities += (float) $row['balance'];
            ?>
                    <tr>
                        <td><?= $row['sub_account_name'] ?></td>
                        <td class="text-end"><?= number_format($row['balance'], 2) ?></td>
                    </tr>
            <?php
                }
            }

            // Calculate Total Income
            $sql = "SELECT SUM(csat.balance) AS total_income
                    FROM sub_account_tbl sat
                    INNER JOIN company_sub_account_balance csat 
                    ON sat.sub_account_id = csat.sub_account_id 
                    WHERE csat.company_id = '" . $_SESSION['company_id'] . "' 
                    AND sat.account_id = (SELECT account_id FROM account_tbl WHERE account_name = 'Income')";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $income_row = $result->fetch_assoc();
                $total_income = $income_row['total_income'];
            }

            // Calculate Total Expenses
            $sql = "SELECT SUM(csat.balance) AS total_expense
                    FROM sub_account_tbl sat
                    INNER JOIN company_sub_account_balance csat 
                    ON sat.sub_account_id = csat.sub_account_id 
                    WHERE csat.company_id = '" . $_SESSION['company_id'] . "' 
                    AND sat.account_id = (SELECT account_id FROM account_tbl WHERE account_name = 'Expenses')";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $expense_row = $result->fetch_assoc();
                $total_expense = $expense_row['total_expense'];
            }

            // Net Income Calculation
            $net_income = $total_income - $total_expense;
            if ($net_income > 0) {
                $total_equities += $net_income;
            } else {
                $total_equities += $net_income; // This will subtract since it's negative
            }
            ?>
            <tr>
                <td><strong>TOTAL EQUITY</strong></td>
                <td class="text-end">
                    <b><?= $_SESSION['currency_code'] . " " . number_format($total_equities, 2) ?></b>
                </td>
            </tr>
        </tbody>
    </table>
</div>