<div class="container py-4">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="card shadow-lg" style="border-radius: 1rem;">
            <div class="card-body p-4 p-md-5">
                <form id="filter-form">
                    <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="<?= date("Y") ?>-01-01">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="<?= date("Y") ?>-12-31">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-secondary" id="submit-btn">Filter</button>
                            </div>
                        </div>
                    </div>
                    <h3 class="mb-4 pb-2 pb-md-0 mb-md-3">Trial Balance Sheet</h3>
                    <table class="table table-responsive table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Account</th>
                                <th scope="col">Debit</th>
                                <th scope="col">Credit</th>
                            </tr>
                        </thead>
                        <tbody id="output">
                            <?php
                            // FIXME: Trial balance sheet in not balanced. Should be balanced
                            // Enable error display
                            ini_set('display_errors', '1');
                            ini_set('error_reporting', E_ALL);
                            $startDate = date("Y") . "-01-01";
                            $endDate = date("Y") . "-12-31";
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
                            $stmt->bind_param("issiii", $_SESSION['company_id'], $startDate, $endDate, $_SESSION['company_id'], $_SESSION['company_id'], $_SESSION['company_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            $totalDebit = 0;
                            $totalCredit = 0;

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $debit = 0;
                                    $credit = 0;
                            ?>
                                    <?php
                                    // Determine debit and credit based on account type and balance
                                    if ($row['account_name'] == 'Assets' || $row['account_name'] == 'Expenses') {
                                        if ($row['total_sub_account_type_balance'] > 0) {
                                            $debit = $row['total_sub_account_type_balance'];
                                        } elseif ($row['total_sub_account_type_balance'] < 0) {
                                            $credit = abs($row['total_sub_account_type_balance']);
                                        }
                                    } elseif ($row['account_name'] == 'Liabilities' || $row['account_name'] == 'Equity' || $row['account_name'] == 'Income') {
                                        if ($row['total_sub_account_type_balance'] > 0) {
                                            $credit = abs($row['total_sub_account_type_balance']);
                                        } elseif ($row['total_sub_account_type_balance'] < 0) {
                                            $debit = abs($row['total_sub_account_type_balance']);
                                        }
                                    } else {
                                    }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['actual_sub_account_name']) ?></td>
                                        <td><?= number_format($debit, 2) ?></td>
                                        <td><?= number_format($credit, 2) ?></td>
                                    </tr>
                                <?php
                                    $totalDebit += $debit;
                                    $totalCredit += abs($credit);
                                }
                                ?>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong><?= $_SESSION['currency_code'] . " " . number_format($totalDebit, 2) ?></strong>
                                    </td>
                                    <td><strong><?= $_SESSION['currency_code'] . " " . number_format($totalCredit, 2) ?></strong>
                                    </td>
                                </tr>
                            <?php
                            } else {
                            ?>
                                <tr>
                                    <td colspan="3">No records found</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="mb-3">
                        <button class="btn btn-outline-warning" type="button" id="export-btn">Export to Spreadsheet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).prop("title", "Trial Balance Sheet | Ginum");

        // navbar
        $("#dashboard").removeClass("active");
        $("#reports").addClass("active");

        $("#filter-form").on("submit", function(e) {
            e.preventDefault();
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            $.ajax({
                url: "../../backend/data/get-trial-balance-data.php",
                method: "POST",
                data: {
                    startDate: startDate,
                    endDate: endDate,
                },
                success: function(data) {
                    var output = "";
                    data = JSON.parse(data);
                    if (data.error) {
                        output = "<tr><td colspan='3'>" + data.error + "</td></tr>";
                    } else {
                        var totalDebit = 0;
                        var totalCredit = 0;
                        data.forEach(function(row) {
                            var debit = parseFloat(row.debit.replace(/,/g, '')) || 0;
                            var credit = parseFloat(row.credit.replace(/,/g, '')) || 0;

                            output += "<tr>";
                            output += "<td>" + row.sub_account_name + "</td>";
                            output += "<td>" + row.debit + "</td>";
                            output += "<td>" + row.credit + "</td>";
                            output += "</tr>";
                            totalDebit += debit;
                            totalCredit += credit;
                        });
                        output += "<tr><td><strong>Total</strong></td>";
                        output += "<td><strong>USD " + totalDebit.toFixed(2) + "</strong></td>";
                        output += "<td><strong>USD " + totalCredit.toFixed(2) + "</strong></td></tr>";
                    }
                    $("#output").html(output);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });

        $("#export-btn").on("click", function() {
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            var form = $('<form action="../../backend/data/export-trial-balance.php" method="post"></form>');
            form.append('<input type="hidden" name="startDate" value="' + startDate + '" />');
            form.append('<input type="hidden" name="endDate" value="' + endDate + '" />');
            $('body').append(form);
            form.submit();
        });
    });
</script>