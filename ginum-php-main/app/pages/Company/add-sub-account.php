<div class="container py-4">
    <h2>New Sub Account</h2>
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
    <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <form action="../../backend/data/add-sub-account-type-data.php" method="post">
            <div class="mb-3">
                <label for="acc_name" class="form-label">Account Name</label>
                <select name="acc_id" id="acc_name" class="form-select" required>
                    <option>-- Choose an Account --</option>
                    <?php
                    $sql = "SELECT * FROM account_tbl";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <option value="<?= $row['account_id'] ?>"><?= $row['account_name'] ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="sub_acc_name" class="form-label">Account Type</label>
                <select name="sub_account_id" class="form-select sub_account_name" id="sub_account_name">
                    <?php
                    $sql = "SELECT DISTINCT sat.*, at.account_name FROM sub_account_tbl sat INNER JOIN account_tbl at ON sat.account_id = at.account_id ORDER BY at.account_name ASC";
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
            </div>

            <div class="mb-3 dataTbl">
                <label for="tbl" class="form-label lbl"></label>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tbl">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Sub Acc. Name</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody"></tbody>
                    </table>
                    <button class="btn btn-md btn-primary" id="addBtn" type="button">
                        <i class='bx bx-plus'></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Set page title
        $(document).prop("title", "Add Sub Accounts | Ginum");

        // Hide the table initially
        $(".dataTbl").hide();

        // Navbar active state
        $("#dashboard").removeClass("active");
        $("#account").addClass("active");

        // Handle account selection change
        $("#acc_name").change(function () {
            let text = $("#acc_name option:selected").text();
            let acc_id = $("#acc_name option:selected").val();

            // Show the table when an account is selected
            $(".dataTbl").show();

            // Set the label based on the selected account type
            let labelMap = {
                'Assets': "Add Sub Accounts for Assets",
                'Liabilities': "Add Sub Accounts for Liabilities",
                'Equity': "Add Sub Accounts for Equity",
                'Income': "Add Sub Accounts for Income",
                'Expenses': "Add Sub Accounts for Expenses"
            };

            $(".lbl").html(labelMap[text] || "");

            // Fetch and populate sub-account data via AJAX
            $.ajax({
                url: '../../backend/data/get-sub-account-data.php',
                type: 'post',
                data: {
                    acc_id: acc_id
                },
                success: function (response) {
                    $("#sub_account_name").html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });

        // Handle sub-account type selection change
        $("#sub_account_name").change(function () {
            let sub_account_id = $(this).val();
            console.log(sub_account_id);

            // Clear existing table rows
            $("#tbody").empty();

            // Fetch and populate sub-account types for the selected sub-account ID
            $.ajax({
                url: '../../backend/data/get-sub-account-types.php',
                type: 'POST',
                data: {
                    sub_account_id: sub_account_id
                },
                success: function (data) {
                    console.log(data);
                    // Assuming the data is in JSON format with fields 'type_name' and 'amount'
                    let parsedData = JSON.parse(data);

                    if (parsedData.length > 0) {
                        let rowHTML = '';
                        $.each(parsedData, function (index, item) {
                            rowHTML += `
                            <tr class="rowClass"> 
                                <td class="row-index text-center">${index + 1}</td>
                                <td class="text-center">
                                    <input type='text' name='sub_account_type_names[]' class='form-control' value='${item.type_name}' title='Sub Account Name'>
                                </td>
                                <td class="text-center">
                                    <input type='text' name='sub_account_type_amounts[]' class='form-control' value='${item.amount}' title='Amount'>
                                </td>
                                <td class="text-center"> 
                                    <button class="btn btn-danger remove" type="button"><i class='bx bx-x'></i></button> 
                                </td> 
                            </tr>`;
                        });
                        $('#tbody').append(rowHTML);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });

        // Initialize row count based on existing rows
        function updateRowNumbers() {
            $('#tbody tr').each(function (index) {
                $(this).find('.row-index').text(index + 1);
            });
        }

        // Add new row when "Add New Row" button is clicked
        $('#addBtn').off('click').on('click', function () {
            let rowCount = $('#tbody tr').length + 1;
            let dynamicRowHTML = `
            <tr class="rowClass"> 
                <td class="row-index text-center">${rowCount}</td>
                <td class="text-center">
                    <input type='text' name='sub_account_type_names[]' class='form-control' title='Sub Account Name'>
                </td>
                <td class="text-center">
                    <input type='text' name='sub_account_type_amounts[]' class='form-control' title='Amount'>
                </td>
                <td class="text-center"> 
                    <button class="btn btn-danger remove" type="button"><i class='bx bx-x'></i></button> 
                </td> 
            </tr>`;
            $('#tbody').append(dynamicRowHTML);
            updateRowNumbers(); // update the row numbers for the current account
        });

        // Remove row on click of Remove button and update the row numbers
        $('#tbody').on('click', '.remove', function () {
            $(this).closest('tr').remove();
            updateRowNumbers(); // Update row numbers after removing a row
        });

        // Call updateRowNumbers to initialize row numbers on page load
        updateRowNumbers();
    });
</script>