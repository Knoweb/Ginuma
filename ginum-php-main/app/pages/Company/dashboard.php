<?php
?>
<main>
    <style>
        :root {
            --poppins: 'Poppins', sans-serif;
            --lato: 'Lato', sans-serif;

            --light: #F9F9F9;
            --blue: #3C91E6;
            --light-blue: #CFE8FF;
            --grey: #eee;
            --dark-grey: #AAAAAA;
            --dark: #342E37;
            --red: #DB504A;
            --yellow: #FFCE26;
            --light-yellow: #FFF2C6;
            --orange: #FD7238;
            --light-orange: #FFE0D3;
        }

        html {
            overflow-x: hidden;
        }

        body.dark {
            --light: #0C0C1E;
            --grey: #060714;
            --dark: #FBFBFB;
        }

        body {
            background: var(--grey);
            overflow-x: hidden;
        }

        /* MAIN */

        #content main .head-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            grid-gap: 16px;
            flex-wrap: wrap;
        }

        #content main .head-title .left h1 {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }

        #content main .head-title .left .breadcrumb {
            display: flex;
            align-items: center;
            grid-gap: 16px;
        }

        ol,
        ul {
            padding-left: 0;
        }

        #content main .head-title .left .breadcrumb li {
            color: var(--dark);
        }

        #content main .head-title .left .breadcrumb li a {
            color: var(--dark-grey);
            pointer-events: none;
        }

        #content main .head-title .left .breadcrumb li a.active {
            color: var(--blue);
            pointer-events: unset;
        }

        #content main .head-title .btn-download {
            height: 36px;
            padding: 0 16px;
            border-radius: 36px;
            background: var(--blue);
            color: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            grid-gap: 10px;
            font-weight: 500;
        }




        #content main .box-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            grid-gap: 24px;
            margin-top: 36px;
        }

        #content main .box-info li {
            padding: 24px;
            background: var(--light);
            border-radius: 20px;
            display: flex;
            align-items: center;
            grid-gap: 24px;
        }

        #content main .box-info li .bx {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            font-size: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #content main .box-info li:nth-child(1) .bx {
            background: var(--light-blue);
            color: var(--blue);
        }

        #content main .box-info li:nth-child(2) .bx {
            background: var(--light-yellow);
            color: var(--yellow);
        }

        #content main .box-info li:nth-child(3) .bx {
            background: var(--light-orange);
            color: var(--orange);
        }

        #content main .box-info li .text h3 {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
        }

        #content main .box-info li .text p {
            color: var(--dark);
        }

        #content main .table-data {
            display: flex;
            flex-wrap: wrap;
            grid-gap: 24px;
            margin-top: 24px;
            width: 100%;
            color: var(--dark);
        }

        #content main .table-data>div {
            border-radius: 20px;
            background: var(--light);
            padding: 24px;
            overflow-x: auto;
        }

        #content main .table-data .head {
            display: flex;
            align-items: center;
            grid-gap: 16px;
            margin-bottom: 24px;
        }

        #content main .table-data .head h3 {
            margin-right: auto;
            font-size: 24px;
            font-weight: 600;
        }

        #content main .table-data .head .bx {
            cursor: pointer;
        }

        #content main .table-data .order {
            flex-grow: 1;
            flex-basis: 500px;
        }

        #content main .table-data .order table {
            width: 100%;
            border-collapse: collapse;
        }

        #content main .table-data .order table th {
            padding-bottom: 12px;
            font-size: 13px;
            text-align: left;
            border-bottom: 1px solid var(--grey);
        }

        #content main .table-data .order table td {
            padding: 16px 0;
        }

        #content main .table-data .order table tr td:first-child {
            display: flex;
            align-items: center;
            grid-gap: 12px;
            padding-left: 6px;
        }

        #content main .table-data .order table td img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        #content main .table-data .order table tbody tr:hover {
            background: var(--grey);
        }

        #content main .table-data .order table tr td .status {
            font-size: 10px;
            padding: 6px 16px;
            color: var(--light);
            border-radius: 20px;
            font-weight: 700;
        }

        #content main .table-data .order table tr td .status.completed {
            background: var(--blue);
        }

        #content main .table-data .order table tr td .status.process {
            background: var(--yellow);
        }

        #content main .table-data .order table tr td .status.pending {
            background: var(--orange);
        }


        #content main .table-data .todo {
            flex-grow: 1;
            flex-basis: 300px;
        }

        #content main .table-data .todo .todo-list {
            width: 100%;
        }

        #content main .table-data .todo .todo-list li {
            width: 100%;
            margin-bottom: 16px;
            background: var(--grey);
            border-radius: 10px;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #content main .table-data .todo .todo-list li .bx {
            cursor: pointer;
        }

        #content main .table-data .todo .todo-list li.completed {
            border-left: 10px solid var(--blue);
        }

        #content main .table-data .todo .todo-list li.not-completed {
            border-left: 10px solid var(--orange);
        }

        #content main .table-data .todo .todo-list li:last-child {
            margin-bottom: 0;
        }

        /* MAIN */
        .chart-container {
            width: 100%;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            position: relative;
            height: 400px;
        }

        @media (max-width: 768px) {
            .chart-container {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .chart-container {
                height: 250px;
            }
        }

        /* CONTENT */
    </style>
    <div class="head-title">
        <div class="left">
            <h1>Dashboard</h1>
            <ul class="breadcrumb">
                <li>
                    <a href="#">Dashboard</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>
                    <a class="active" href="./Company.php?page=dashboard">Home</a>
                </li>
            </ul>
        </div>
    </div>
    <ul class="box-info">
        <li>
            <i class='bx bxs-calendar-check'></i>
            <span class="text">
                <h3>
                    <?php
                    $sql = "SELECT COUNT(*) AS count FROM project_tbl WHERE NOT work_status='Completed' OR NOT work_status='Cancled' AND company_id='" . $_SESSION['company_id'] . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        echo $row['count'];
                    } else {
                        echo 0;
                    }
                    ?>
                </h3>
                <p>New Order</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-group'></i>
            <span class="text">
                <h3>
                    <?php
                    $sql = "SELECT IFNULL(SUM(amount), 0) AS amount FROM expense_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        echo $_SESSION['currency_code'] . " " . $row['amount'];
                    } else {
                        echo 0;
                    }
                    ?>
                </h3>
                <p>Total Expenses</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-dollar-circle'></i>
            <span class="text">
                <h3>
                    <?php
                    $sql = "SELECT IFNULL(SUM(amount), 0) AS amount FROM income_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        echo $_SESSION['currency_code'] . " " . $row['amount'];
                    } else {
                        echo 0;
                    }
                    ?>
                </h3>
                <p>Total Income</p>
            </span>
        </li>
    </ul>

    <!-- Add two new sections for the charts -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Recent Orders</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Date Started</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM project_tbl pt INNER JOIN customer_tbl ct ON (pt.customer_id = ct.customer_id) WHERE NOT pt.work_status='Completed' OR NOT pt.work_status='Cancled' AND pt.company_id='" . $_SESSION['company_id'] . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows == 1) {
                        while ($row = $result->fetch_assoc()) {
                            $customer_name = $row['name'];
                            $start_date = $row['start_date'];
                            $status = $row['work_status'];

                            echo "<tr>";
                            echo "<td>$customer_name</td>";
                            echo "<td>$start_date</td>";
                            echo "<td>$status</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        async function fetchData(query) {
            const response = await fetch('../../backend/data/fetch_chart_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    query: query
                })
            });
            if (response.status === 200) {
                return response.json();
            } else {
                console.log(response);
            }
        }

        async function loadCharts() {
            // Fetch the top 5 income data
            const company_id = "<?= $_SESSION['company_id'] ?>";
            const incomeQuery = `
                                WITH SalesRevenue AS (
                                    SELECT 
                                        SUM(
                                            (sr.sold_price * COALESCE(sr.qty, 1)) 
                                            + COALESCE(sr.tax, 0) - COALESCE(sr.discount, 0)
                                        ) AS total_sales_revenue
                                    FROM sales_receipt_tbl sr
                                    WHERE sr.company_id = <?= $_SESSION['company_id'] ?>
                                    AND sr.status = 1
                                    AND YEAR(sr.date_made) = YEAR(CURRENT_DATE())
                                    AND MONTH(sr.date_made) = MONTH(CURRENT_DATE())
                                )

                                (SELECT 
                                    it.income_id, 
                                    it.date_added, 
                                    sat.sub_account_name, 
                                    it.amount AS amount,  -- Use the actual amount from income_tbl
                                    it.reference, 
                                    NULL AS item_id,
                                    NULL AS quantity,
                                    NULL AS tax_amount,
                                    NULL AS discount_amount,
                                    (SELECT sub_account_name 
                                    FROM sub_account_tbl sat2 
                                    INNER JOIN transaction_log_tbl tlt 
                                    ON sat2.sub_account_id = tlt.debit_account 
                                    WHERE tlt.company_id = it.company_id 
                                    AND tlt.transaction_type = 'Income Addition' 
                                    AND tlt.amount = it.amount 
                                    LIMIT 1) AS paid_through
                                FROM income_tbl it
                                INNER JOIN sub_account_tbl sat 
                                    ON it.sub_account_id = sat.sub_account_id
                                WHERE it.company_id = <?= $_SESSION['company_id'] ?>
                                AND it.status = 1
                                AND YEAR(it.date_added) = YEAR(CURRENT_DATE())
                                AND MONTH(it.date_added) = MONTH(CURRENT_DATE())
                                ORDER BY it.amount DESC
                                LIMIT 5)

                                UNION ALL

                                (SELECT 
                                    NULL AS income_id, 
                                    sr.date_made AS date_added, 
                                    'Sales' AS sub_account_name, 
                                    COALESCE((SELECT total_sales_revenue FROM SalesRevenue), 0) AS amount, 
                                    '' AS reference, 
                                    sr.item_id,
                                    sr.qty AS quantity,
                                    sr.tax AS tax_amount,
                                    sr.discount AS discount_amount,
                                    NULL AS paid_through
                                FROM sales_receipt_tbl sr
                                WHERE sr.company_id = <?= $_SESSION['company_id'] ?>
                                AND sr.status = 1
                                AND YEAR(sr.date_made) = YEAR(CURRENT_DATE())
                                AND MONTH(sr.date_made) = MONTH(CURRENT_DATE())
                                GROUP BY sr.invoice_no, sr.item_id, sr.date_made, sr.qty, sr.tax, sr.discount
                                ORDER BY sr.date_made DESC
                                LIMIT 5)
                            `;

            const incomeData = await fetchData(incomeQuery);

            // Fetch the top 5 expense data
            const expenseQuery = `
                SELECT sa.sub_account_name, e.amount, e.date_added
                FROM expense_tbl e
                JOIN sub_account_tbl sa ON e.sub_account_id = sa.sub_account_id
                WHERE MONTH(e.date_added) = MONTH(CURRENT_DATE())
                AND YEAR(e.date_added) = YEAR(CURRENT_DATE())
                AND e.company_id = ${company_id}
                ORDER BY e.amount DESC
                LIMIT 5;
            `;
            const expenseData = await fetchData(expenseQuery);

            const expenseLabels = expenseData.map(item => `${item.sub_account_name} (${item.date_added})`);
            const expenseAmounts = expenseData.map(item => item.amount.split(' ')[1]); // Split to remove currency symbol for data
            const expenseCurrency = expenseData.length > 0 ? expenseData[0].amount.split(' ')[0] : ''; // Extract currency code

            // Create the expense chart
            const ctxExpense = document.getElementById('expenseChart').getContext('2d');
            new Chart(ctxExpense, {
                type: 'doughnut',
                data: {
                    labels: expenseLabels,
                    datasets: [{
                        label: `Top 5 Expenses (${expenseCurrency})`,
                        data: expenseAmounts,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Expenses (Current Month)'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return `${label}: ${expenseCurrency} ${value}`;
                                }
                            }
                        }
                    }
                }
            });

            // Parse the data for Chart.js
            const incomeLabels = incomeData.map(item => `${item.sub_account_name} (${item.date_added})`);
            const incomeAmounts = incomeData.map(item => item.amount.split(' ')[1]); // Split to remove currency symbol for data
            const incomeCurrency = incomeData.length > 0 ? incomeData[0].amount.split(' ')[0] : ''; // Extract currency code

            // Create the income chart
            const ctxIncome = document.getElementById('incomeChart').getContext('2d');
            new Chart(ctxIncome, {
                type: 'doughnut',
                data: {
                    labels: incomeLabels,
                    datasets: [{
                        label: `Top 5 Income (${incomeCurrency})`,
                        data: incomeAmounts,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Income (Current Month)'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return `${label}: ${incomeCurrency} ${value}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Load the charts
        loadCharts();
    </script>
</main>