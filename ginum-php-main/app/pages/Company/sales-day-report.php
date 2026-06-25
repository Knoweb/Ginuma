<style>
    .report-container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header h1 {
        margin: 0;
    }

    .header p {
        margin: 5px 0 0;
        color: #6c757d;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }

    th {
        background-color: #f1f1f1;
    }

    .total {
        font-weight: bold;
        text-align: right;
    }

    .total-amount {
        font-size: 1.2em;
    }
</style>
<div class="report-container">
    <div class="mt-4 shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="date-range" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="date-range" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <button class="btn btn-outline-secondary" id="search">Filter</button>
            </div>
        </div>
    </div>
    <div class="header">
        <h1>Daily Sales Report</h1>
        <p>Date: <?= date("Y-m-d") ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Quantity Sold</th>
                <th>Unit Price (<?= $_SESSION['currency_code'] ?>)</th>
                <th>Total Price (<?= $_SESSION['currency_code'] ?>)</th>
            </tr>
        </thead>
        <tbody id="data">
            <?php
            $total = 0;
            $sql = "SELECT it.item_id, srt.qty, it.item_name, invt.unit_price FROM sales_receipt_tbl srt INNER JOIN item_tbl it ON (srt.item_id=it.item_id) INNER JOIN inventory_tbl invt ON (it.item_id=invt.item_id) WHERE it.company_id='" . $_SESSION['company_id'] . "' AND srt.date_made='" . date("Y-m-d") . "'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $total += (float) $row['unit_price'] * (float) $row['qty']
                        ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $row['item_name'] ?></td>
                        <td><?= $row['qty'] ?></td>
                        <td><?= $row['unit_price'] ?></td>
                        <td><?= (float) $row['unit_price'] * (float) $row['qty'] ?></td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?>
        </tbody>
    </table>
    <div class="total">
        Total Sales: <span class="total-amount"><?= $_SESSION['currency_code'] . $total ?></span>
    </div>
</div>


<script>
    $(document).ready(function () {
        // set page title
        $(document).prop('title', 'Daily Sales Report | Ginum');

        // navbar
        $("#dashboard").removeClass("active");
        $("#reports").addClass("active");

        // make a ajax request to get the sales report of the given date or date range
        $("#search").click(function () {
            if ($("#start_date").val() === "") {
                console.error("Error: Start date is required");
                alert("Please enter a start date.");
            } else {
                $.ajax({
                    url: "../../backend/data/get-sales-report.php",
                    method: "POST",
                    data: {
                        start_date: $("#start_date").val(),
                        end_date: $("#end_date").val()
                    },
                    success: function (response) {
                        $("#data").html(response);
                    }
                });
            }
        });

    });
</script>