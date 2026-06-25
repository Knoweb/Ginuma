<?php if (isset($_GET['success'])) { ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Done',
            text: "<?= $_GET['success'] ?>"
        })
    </script>
<?php } ?>
<?php if (isset($_GET['error'])) { ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "<?= $_GET['error'] ?>"
        })
    </script>
<?php } ?>
<?php if (isset($_GET['warning'])) { ?>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Careful!',
            text: "<?= $_GET['warning'] ?>"
        })
    </script>
<?php } ?>
<div class="container py-4">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="card shadow-lg" style="border-radius: 1rem;">
            <div class="card-body p-4 p-md-5">
                <h3 class="mb-4 pb-2 pb-md-0 mb-md-3">Add Bank Statement</h3>
                <form action="../../backend/data/record-bank-statement-data.php" method="post" id="form-components">
                    <div class="mb-3">
                        <label for="sub_acc" class="form-label">Select your Bank Account</label>
                        <select name="company_sub_account_balance_id" id="sub_acc" class="form-select">
                            <?php
                            $bank_id;
                            $sql = "SELECT * FROM bank_account_tbl bat INNER JOIN company_sub_account_balance csab ON (bat.company_sub_account_balance_id=csab.company_sub_account_balance_id) INNER JOIN bank_details_tbl bdt ON (bat.bank_details_id=bdt.bank_details_id) WHERE bat.company_id='" . $_SESSION['company_id'] . "'";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $bank_id = $row['bank_id'];
                                    ?>
                                    <option value="<?= $row['company_sub_account_balance_id'] ?>">
                                        <?= $row['account_name'] . " - " . $row['account_number'] ?>
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <input type="hidden" name="bank_id" value="<?= $bank_id ?>">
                    </div>
                    <hr>
                    <div class="mt-5 table-responsive">
                        <table class="table table-bordered" id="tasks">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Account</th>
                                    <th class="text-center">D / W</th>
                                    <th class="text-center">Amount (<?= $_SESSION['currency_code'] ?>)</th>
                                    <th class="text-center">Balance (<?= $_SESSION['currency_code'] ?>)</th>
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
        // set the active state of the navbar
        $("#dashboard").removeClass("active");
        $("#bank-statements").addClass("active");

        // Declare global balance variable
        let globalBalance = 0;
        // Set page title
        $(document).prop("title", "Add Bank Statement | Ginum");

        // Fetch the account balance on loading the page
        fetchAccountBalance($("#sub_acc").val());

        // Fetch the account balance when the user selects a bank account
        $("#sub_acc").change(function () {
            fetchAccountBalance($(this).val());
        });

        // Function to fetch account balance
        function fetchAccountBalance(company_sub_account_balance_id) {
            $.ajax({
                url: "../../backend/data/fetch_account_balance.php",
                method: "POST",
                data: {
                    company_sub_account_balance_id: company_sub_account_balance_id
                },
                success: function (data) {
                    try {
                        // Store the account balance into a variable
                        globalBalance = parseFloat(data);
                        console.log("Initial Balance: ", globalBalance);
                        updateBalanceInTable(); // Update the balance for each row
                    } catch (err) {
                        // Display the error message
                        alert(err);
                        console.log(err);
                    }
                }
            });
        }

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
                    <input type='text' name='descriptions[]' class='form-control' title='Transaction Description'>
                </td>  
                
                <td class="text-center"> 
                    <select name="sub_account_ids[]" class="form-select" title="Sub Account">
                        <?php
                        $sql = "SELECT sub_account_id, sub_account_name, account_name FROM sub_account_tbl sat INNER JOIN account_tbl at ON (sat.account_id = at.account_id)";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['sub_account_id'] . "'>" . $row['account_name'] . ": " . $row['sub_account_name'] . "</option>";
                            }
                        } else {
                            echo "0 results";
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
                    <input type='text' name='amounts[]' class='form-control amount-input' title='Transaction Amount'>
                </td> 
                <td class="text-center"> 
                    <input type='text' name='balances[]' class='form-control balance-input' title='Account Balance' readonly>
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

        // Update balances when amount or type changes
        $('#tbody').on('input', '.amount-input', function () {
            updateBalanceInTable();
        });

        $('#tbody').on('change', 'select[name="dOrWs[]"]', function () {
            updateBalanceInTable();
        });

        // Update the balance in the table
        function updateBalanceInTable() {
            let balance = globalBalance;
            $('#tbody tr').each(function () {
                const amount = parseFloat($(this).find('.amount-input').val()) || 0;
                const transactionType = $(this).find('select[name="dOrWs[]"]').val();
                if (transactionType === 'Deposit') {
                    balance += amount;
                } else if (transactionType === 'Withdrawal') {
                    balance -= amount;
                }
                $(this).find('.balance-input').val(balance.toFixed(2));
            });
        }
    });

    // Initialize row count
    let count = 1;
</script>