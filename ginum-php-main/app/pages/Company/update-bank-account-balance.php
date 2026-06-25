<div class="container-fluid py-4">
    <h2>Update Bank Account Balance</h2>
    <?php if (isset($_GET['success'])) { ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Done',
                text: "<?php echo $_GET['success']; ?>"
            });
        </script>
    <?php } ?>
    <?php if (isset($_GET['error'])) { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "<?php echo $_GET['error']; ?>"
            });
        </script>
    <?php } ?>
    <?php if (isset($_GET['warning'])) { ?>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Careful!',
                text: "<?php echo $_GET['warning']; ?>"
            });
        </script>
    <?php } ?>
    <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <form action="../../backend/data/update-bank-account-balance-data.php" method="post">
            <div class="table-responsive">
                <table class="table table-bordered" id="tasks">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                            <th class="text-center">D / W</th>
                            <th class="text-center">Amount (<?php echo $_SESSION['currency_code']; ?>)</th>
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

<script>
    $(document).ready(function () {
        // Set the active state of the navbar
        $("#dashboard").removeClass("active");
        $("#bank-statements").addClass("active");

        // Declare global balance variable
        let globalBalance = 0;
        // Set page title
        $(document).prop("title", "Update Bank Account Balance | Ginum");

        // Adding row on click to Add New Row button
        $('#addBtn').click(function () {
            let dynamicRowHTML = `
            <tr class="rowClass"> 
                <td class="row-index text-center"> 
                    ${count}
                </td>
                <td class="text-center"> 
                    <input type='date' name='dates[]' class='form-control' title='Transaction Date'>
                </td> 
                <td class="text-center"> 
    <select name="debit_acc_ids[]" class="form-select">
        <?php
        $sql = "SELECT DISTINCT sat.*, at.account_name, bdt.bank_name, bdt.branch, bdt.account_name AS bank_account_name, bdt.account_number 
                FROM sub_account_tbl sat 
                INNER JOIN account_tbl at ON sat.account_id = at.account_id
                LEFT JOIN company_sub_account_balance csab ON sat.sub_account_id = csab.sub_account_id
                LEFT JOIN bank_account_tbl bat ON csab.company_sub_account_balance_id = bat.company_sub_account_balance_id
                LEFT JOIN bank_details_tbl bdt ON bat.bank_details_id = bdt.bank_details_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['sub_account_id'] . '">' . $row['account_name'] . ': ' . $row['sub_account_name'];
                if ($row['bank_name']) {
                    echo ' (' . $row['bank_name'] . ' - ' . $row['branch'] . ' - ' . $row['bank_account_name'] . ' - ' . $row['account_number'] . ')';
                }
                echo '</option>';
            }
        }
        ?>
    </select>
</td>
    <td class="text-center"> 
        <select name="credit_acc_ids[]" class="form-select">
            <?php
            $sql = "SELECT DISTINCT sat.*, at.account_name, bdt.bank_name, bdt.branch, bdt.account_name AS bank_account_name, bdt.account_number 
                    FROM sub_account_tbl sat 
                    INNER JOIN account_tbl at ON sat.account_id = at.account_id
                    LEFT JOIN company_sub_account_balance csab ON sat.sub_account_id = csab.sub_account_id
                    LEFT JOIN bank_account_tbl bat ON csab.company_sub_account_balance_id = bat.company_sub_account_balance_id
                    LEFT JOIN bank_details_tbl bdt ON bat.bank_details_id = bdt.bank_details_id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['sub_account_id'] . '">' . $row['account_name'] . ': ' . $row['sub_account_name'];
                    if ($row['bank_name']) {
                        echo ' (' . $row['bank_name'] . ' - ' . $row['branch'] . ' - ' . $row['bank_account_name'] . ' - ' . $row['account_number'] . ')';
                    }
                    echo '</option>';
                }
            }
            ?>
        </select>
    </td>
                <td class="text-center"> 
                    <select name="dOrWs[]" class="form-select">
                        <option value="Deposit">Deposit</option>
                        <option value="Withdrawal">Withdrawal</option>
                    </select>
                </td>
                <td class="text-center">
                    <input type="text" name="amounts[]" class="form-control amount-input">
                </td> 
                <td class="text-center"> 
                    <input type="text" name="descriptions[]" class="form-control">
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
            updateBalanceInTable(); // Update balances after removing a row
        });

        // Update balances when amount changes
        $('#tbody').on('input', '.amount-input', function () {
            updateBalanceInTable();
        });

        // Update the balance in the table
        function updateBalanceInTable() {
            let balance = globalBalance;
            $('#tbody tr').each(function () {
                const amount = parseFloat($(this).find('.amount-input').val()) || 0;
                const debitSelect = $(this).find('select[name="debit_acc_ids[]"]').val();
                const creditSelect = $(this).find('select[name="credit_acc_ids[]"]').val();
                if (debitSelect) {
                    balance -= amount;
                } else if (creditSelect) {
                    balance += amount;
                }
                $(this).find('.balance-input').val(balance.toFixed(2));
            });
        }
    });

    // Initialize row count
    let count = 1;
</script>