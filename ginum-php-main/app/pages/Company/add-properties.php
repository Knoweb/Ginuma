<div class="container py-5">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="card shadow-lg" style="border-radius: 1rem;">
            <div class="card-body p-4 p-md-5">
                <h3 class="mb-4 pb-2 pb-md-0 mb-md-3">Make Transactions</h3>
                <form action="../../backend/data/add-properties-data.php" method="post" id="form-components">
                    <div class="mt-5 table-responsive">
                        <table class="table table-bordered" id="tasks">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Transaction Type</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Credit</th>
                                    <th class="text-center">Amount (<?= $_SESSION['currency_code'] ?>)</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody"></tbody>
                        </table>
                        <button class="btn btn-md btn-primary" id="addBtn" type="button">
                            <i class='bx bx-plus'></i>
                        </button>
                    </div>
                    <div class="mb-3 mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Set page title
        $(document).prop("title", "Add Properties | Ginum");

        // Adding row on click to Add New Row button
        $('#addBtn').click(function () {
            let dynamicRowHTML = `
            <tr class="rowClass"> 
                <td class="row-index text-center"> 
                    ${count}
                </td>
                <td class="text-center"> 
                    <select name="transaction_types[]" class="form-select">
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </td>
                <td class="text-center"> 
                    <select name="debit_acc_ids[]" class="form-select">
                        <?php
                        $sql = "SELECT DISTINCT sat.*, at.account_name 
                                FROM sub_account_tbl sat 
                                INNER JOIN account_tbl at ON sat.account_id = at.account_id";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <option value="<?= $row['sub_account_id'] ?>">
                                    <?= $row['account_name'] ?>: <?= $row['sub_account_name'] ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </td> 
                <td class="text-center"> 
                    <select name="credit_acc_ids[]" class="form-select">
                        <?php
                        $sql = "SELECT DISTINCT sat.*, at.account_name 
                                FROM sub_account_tbl sat 
                                INNER JOIN account_tbl at ON sat.account_id = at.account_id";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <option value="<?= $row['sub_account_id'] ?>">
                                    <?= $row['account_name'] ?>: <?= $row['sub_account_name'] ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </td>  
                <td class="text-center"> 
                    <input type="text" name="amounts[]" class="form-control">
                </td> 
                <td class="text-center"> 
                    <input type="text" name="descriptions[]" class="form-control" id="description">
                </td>
                <td class="text-center"> 
                    <button class="btn btn-danger remove" type="button"><i class='bx bx-x'></i></button> 
                </td> 
            </tr>`;
            $('#tbody').append(dynamicRowHTML);
            count++;
        });

        // Remove row on click of Remove button
        $('#tbody').on('click', '.remove', function () {
            $(this).closest('tr').remove();
        });
    });

    // Initialize row count
    let count = 1;
</script>
