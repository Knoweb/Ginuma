<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    try {
        $sql = "
            SELECT 
                sa.sub_account_name AS actual_sub_account_name,
                at.account_name AS account_name,
                COALESCE(tl.balance, 0) + COALESCE(csb.balance, 0) AS balance,
                COALESCE(st_balance.total_sub_account_type_balance, 0) AS total_sub_account_type_balance
            FROM 
                sub_account_tbl sa
            INNER JOIN 
                account_tbl at ON sa.account_id = at.account_id
            LEFT JOIN 
                (
                    SELECT 
                        credit_account AS account_id,
                        SUM(CASE 
                            WHEN transaction_type IN ('Assets Addition', 'Item Addition', 'Expenses Addition') THEN amount 
                            WHEN transaction_type IN ('Inventory Deduction', 'Assets Deduction', 'Expenses Deduction') THEN -amount 
                            WHEN transaction_type IN ('Liability Addition', 'Equity Addition', 'Retained Earnings Addition') THEN -amount 
                            WHEN transaction_type IN ('Liability Deduction', 'Equity Deduction', 'Retained Earnings Deduction') THEN amount 
                            ELSE 0 
                        END) AS balance
                    FROM 
                        transaction_log_tbl
                    WHERE 
                        company_id = ?
                        AND transaction_date BETWEEN ? AND ?
                    GROUP BY 
                        credit_account
                ) AS tl ON sa.sub_account_id = tl.account_id
            LEFT JOIN 
                company_sub_account_balance csb ON sa.sub_account_id = csb.sub_account_id AND csb.company_id = ?
            LEFT JOIN 
                (
                    SELECT 
                        sa.sub_account_id,
                        SUM(COALESCE(csab.balance, 0)) AS total_sub_account_type_balance
                    FROM 
                        sub_account_tbl sa
                    LEFT JOIN 
                        sub_account_type_tbl sat ON sa.sub_account_id = sat.sub_account_id
                    LEFT JOIN 
                        company_sub_account_balance csab ON (csab.sub_account_id = sa.sub_account_id 
                        AND csab.sub_account_type_id IS NULL 
                        AND csab.company_id = ?) 
                        OR (csab.sub_account_type_id = sat.sub_account_type_id 
                        AND csab.company_id = ?)
                    GROUP BY 
                        sa.sub_account_id
                ) AS st_balance ON sa.sub_account_id = st_balance.sub_account_id
            WHERE 
                at.account_name IN ('Assets', 'Liabilities', 'Equity', 'Income', 'Expenses')
            ORDER BY 
                at.account_name ASC, sa.sub_account_name ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $_SESSION['company_id'], $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $totalDebit = 0;
        $totalCredit = 0;

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $debit = 0;
            $credit = 0;

            if ($row['account_name'] == 'Assets' || $row['account_name'] == 'Expenses') {
                if ($row['balance'] > 0) {
                    $debit = $row['balance'];
                } elseif ($row['balance'] < 0) {
                    $credit = abs($row['balance']);
                }
            } elseif ($row['account_name'] == 'Liabilities' || $row['account_name'] == 'Equity' || $row['account_name'] == 'Income') {
                if ($row['balance'] > 0) {
                    $credit = abs($row['balance']);
                } elseif ($row['balance'] < 0) {
                    $debit = abs($row['balance']);
                }
            } else {
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $data[] = [
                'sub_account_name' => htmlspecialchars($row['sub_account_name']),
                'debit' => number_format($debit, 2),
                'credit' => number_format($credit, 2),
            ];
        }

        // Add totals row
        $data[] = [
            'sub_account_name' => 'Total',
            'debit' => $_SESSION['currency_code'] . ' ' . number_format($totalDebit, 2),
            'credit' => $_SESSION['currency_code'] . ' ' . number_format($totalCredit, 2),
        ];

        echo json_encode($data);
    } catch (Exception $e) {
        echo json_encode(['error' => 'An error occurred while fetching the data.']);
    }
}
