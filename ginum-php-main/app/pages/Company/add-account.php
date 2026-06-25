<div class="container py-4">
    <h2>New Account</h2>
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
        <form action="../../backend/data/add-account-data.php" method="post">
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
                <label for="sub_acc_name" class="form-label">Sub Account Name</label>
                <input type="text" name="sub_acc_name" id="sub_acc_name" class="form-control" required>
            </div>

            <div class="mb-3" id="sub_acc_type_div">
                <label for="sub_account_type" class="form-label">Sub Account Type</label>
                <select name="sub_account_type" id="sub_account_type" class="form-select">
                    <option value="">-- Select an option --</option>
                    <option value="Current">Current</option>
                    <option value="Non-Current">Non-Current</option>
                </select>
            </div>

            <div class="mb-3" id="amountDiv">
                <label for="amount" class="form-label">Opening Balance</label>
                <input type="text" name="amount" id="amount" class="form-control">
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
        // title
        $(document).prop("title", "Add Account | Ginum");

        //navbar
        $("#dashboard").removeClass("active");
        $("#account").addClass("active");
        // check the account name and decide the amount text box would be shown or not
        $("#sub_acc_type_div").hide();
        $("#acc_name").change(function () {
            let text = $("#acc_name option:selected").text();
            if (text == "Income" || text == "Expenses") {
                $("#amountDiv").hide();
                $("#sub_acc_type_div").hide();
            } else {
                $("#amountDiv").show();
                if (text == "Equity") {
                    $("#sub_acc_type_div").hide();
                } else {
                    $("#sub_acc_type_div").show();
                }
            }
        })
    })
</script>