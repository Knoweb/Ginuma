<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<style>
    header {
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
        color: #333;
    }

    header p {
        margin: 5px 0;
        color: #555;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    thead {
        background-color: #f4f4f4;
    }

    th {
        font-weight: bold;
        color: #333;
    }

    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
</style>

<?php

if (isset($_GET['date'])) {
    $date = $_GET['date'];
    list($year, $month) = explode('-', $date);
    $year = (int) $year;
    $month = (int) $month;

    $company_id = $_SESSION['company_id'];

    $sql = "
            SELECT 
                bd.bank_name AS `Bank Name`, 
                bd.account_number AS `Account No.`,
                bd.account_name AS `Account Name`, 
                bs.transaction_date AS `Date`,
                bs.transaction_description AS `Details`,
                CASE 
                    WHEN bs.d_or_w = 'Deposit' THEN bs.amount
                    ELSE 0
                END AS `Deposits`,
                CASE 
                    WHEN bs.d_or_w = 'Withdrawal' THEN bs.amount
                    ELSE 0
                END AS `Withdrawals`,
                bs.balance AS `Balance`
            FROM 
                bank_statement_tbl bs
            JOIN 
                bank_account_tbl ba ON bs.bank_id = ba.bank_id
            JOIN 
                bank_details_tbl bd ON ba.bank_details_id = bd.bank_details_id
            WHERE 
                YEAR(bs.date_created) = ? AND
                MONTH(bs.date_created) = ? AND
                bs.company_id = ?
            ORDER BY 
                bs.date_created ASC
        ";
    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the year, month, and company_id parameters
    $stmt->bind_param("iii", $year, $month, $company_id);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the data
    $bankStatements = [];
    while ($row = $result->fetch_assoc()) {
        $bankStatements[] = $row;
    }
    ?>

    <div class="container">
        <header>
            <h1>Bank Statement</h1>
            <p>Account Holder: <?= $bankStatements[0]['Account Name'] ?></p>
            <p>Account Number: <?= $bankStatements[0]['Account No.'] ?></p>
            <p>Statement Period: <?= DateTime::createFromFormat('!m', $month)->format("F") ?> 1, <?= $year ?> -
                <?= DateTime::createFromFormat('!m', $month)->format("F") ?>     <?= date("t") ?>,
                <?= $year ?>
            </p>
        </header>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Withdrawals</th>
                    <th>Deposits</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($bankStatements as $statement) {
                    echo "<tr>";
                    echo "<td>" . $statement['Date'] . "</td>";
                    echo "<td>" . $statement['Details'] . "</td>";
                    echo "<td>" . $statement['Withdrawals'] . "</td>";
                    echo "<td>" . $statement['Deposits'] . "</td>";
                    echo "<td>" . $statement['Balance'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="../../backend/data/generate-bank-statement-sheet.php?date=<?= $date ?>" class="btn btn-secondary"><i
                class="fas fa-print"></i> &nbsp;Print</a>
    </div>

    <?php
}
?>