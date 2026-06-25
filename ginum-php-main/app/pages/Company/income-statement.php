<?php

$total_income = 0;
$total_expenses = 0;
// Enable error display
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

// get the company details from the company id
$sql = "SELECT * FROM company_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
$result = $conn->query($sql);
$company_row = $result->fetch_assoc();
?>

<style>
    .single-underline {
        position: relative;
        padding-bottom: 2px;
    }

    .single-underline::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 2px;
        background-color: black;
    }

    .double-underline {
        position: relative;
        padding-bottom: 5px;
    }

    .double-underline::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 2px;
        background-color: black;
        box-shadow: 0 -5px 0 black;
    }
</style>

<div class="container mt-5">
    <h1 style="text-align: center;"><?= $company_row['company_name'] ?></h1>
    <div class="text-center mb-4">
        <h3>INCOME STATEMENT</h3>
        <h4 style="text-align: center;">For the year Ended December 31, <?= date("Y") ?></h4>
    </div>

    <table class="table">
        <thead>
        </thead>
        <tbody>
            <tr>
                <th colspan="4">Revenues (<?= $_SESSION['currency_code'] ?>)</th>
            </tr>
            <?php
            // get all the income information from income_tbl and sales_receipt_tbl
            $sql1 = "(SELECT it.income_id, 
                        it.date_added, 
                        sat.sub_account_name AS income_account, 
                        it.amount, 
                        it.reference, 
                        NULL AS item_id,
                        NULL AS quantity,
                        NULL AS tax_amount,
                        NULL AS discount_amount,
                        (SELECT sub_account_name 
                            FROM sub_account_tbl sat2 
                            INNER JOIN transaction_log_tbl tlt 
                            ON sat2.sub_account_id = tlt.credit_account  -- Updated to credit_account
                            WHERE tlt.company_id = it.company_id 
                            AND tlt.transaction_type = 'Income Addition' 
                            AND tlt.amount = it.amount 
                            LIMIT 1) AS paid_through
                    FROM income_tbl it
                    INNER JOIN sub_account_tbl sat 
                    ON it.sub_account_id = sat.sub_account_id
                    WHERE it.company_id = ? 
                    AND it.status = 1)

                    UNION ALL

                    (SELECT NULL AS income_id, 
        MAX(sr.date_made) AS date_added,
        'Sales' AS income_account, 
        SUM(sr.sold_price) AS amount, 
        '' AS reference, 
        sr.item_id,
        SUM(sr.qty) AS quantity,
        SUM(sr.tax) AS tax_amount, 
        SUM(sr.discount) AS discount_amount, 
        NULL AS paid_through
 FROM sales_receipt_tbl sr
 WHERE sr.company_id = ? 
   AND sr.status = 1
 GROUP BY sr.invoice_no, sr.item_id)";

            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param('ii', $_SESSION['company_id'], $_SESSION['company_id']);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            if ($result1->num_rows > 0) {
                while ($row1 = $result1->fetch_assoc()) {
                    $amount = $row1['amount']; // Default amount
                    if ($row1['income_account'] == 'Sales') {
                        // data fetched from sales receipt table
                        $sql2 = "SELECT * FROM inventory_tbl WHERE item_id=? AND company_id=?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param('ii', $row1['item_id'], $_SESSION['company_id']);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        if ($result2->num_rows > 0) {
                            $row2 = $result2->fetch_assoc();
                            $bought_price = $row2['unit_price'] ?? 0;
                            $quantity = $row1['quantity'] ?? 1;
                            $tax_amount = $row1['tax_amount'] ?? 0;
                            $discount_amount = $row1['discount_amount'] ?? 0;

                            $amount = (($row1['amount'] ?? 0) - $bought_price) * $quantity + $tax_amount - $discount_amount;
                        }
                        $stmt2->close();
                    }
            ?>
                    <tr>
                        <td></td>
                        <td><?= $row1['income_account'] ?></td>
                        <td>
                            <?= number_format($amount, 2) ?>
                        </td>
                        <td></td>
                    </tr>
                <?php
                    $total_income += (float) $amount;
                }
                ?>
                <tr>
                    <td></td>
                    <th>Total Revenue</th>
                    <td></td>
                    <th class="text-end"><?= $_SESSION['currency_code'] . " " . number_format($total_income, 2) ?>
                    </th>
                </tr>
            <?php
            }
            $stmt1->close();
            ?>
            <tr>
                <th colspan="4">Expenses (<?= $_SESSION['currency_code'] ?>)</th>
            </tr>
            <?php
            // all the expense and expenditure details
            $sql1 = "SELECT * FROM sub_account_tbl sat 
                     INNER JOIN account_tbl at ON sat.account_id = at.account_id 
                     INNER JOIN company_sub_account_balance csat 
                     ON csat.sub_account_id = sat.sub_account_id 
                     WHERE account_name='Expenses' 
                     AND csat.company_id = ?";
            $stmt3 = $conn->prepare($sql1);
            $stmt3->bind_param('i', $_SESSION['company_id']);
            $stmt3->execute();
            $result3 = $stmt3->get_result();
            if ($result3->num_rows > 0) {
                while ($row3 = $result3->fetch_assoc()) {
            ?>
                    <tr>
                        <td></td>
                        <td><?= $row3['sub_account_name'] ?></td>
                        <td>
                            <?= number_format($row3['balance'], 2) ?>
                        </td>
                        <td></td>
                    </tr>
                <?php
                    $total_expenses += (float) $row3['balance'];
                }
                ?>
                <tr>
                    <td></td>
                    <th>Total Expenses</th>
                    <td></td>
                    <th class="single-underline text-end">
                        <?= $_SESSION['currency_code'] . " " . number_format($total_expenses, 2) ?>
                    </th>
                </tr>
            <?php
            }
            ?>
            <tr>
                <th colspan="3">Net Income</th>
                <th class="double-underline text-end">
                    <?= $_SESSION['currency_code'] . " " . number_format($total_income - $total_expenses, 2) ?>
                </th>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        // title
        $(document).prop('title', 'Income Statement | Ginum');

        // navbar
        $("#dashboard").removeClass("active");
        $("#reports").addClass("active");
    })
</script>