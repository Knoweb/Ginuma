<div class="container py-4">
    <h2>Add Opening Balances</h2>
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
        <form action="../../backend/data/add-opening-balance-data.php" method="post">
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
                <select name="sub_account_id" class="form-select" id="sub_account_name">
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
        $("#opening_balance").addClass("active");
        // check the account name and decide the amount text box would be shown or not
        $("#acc_name").change(function () {
            let text = $("#acc_name option:selected").text();
            let acc_id = $("#acc_name option:selected").val();
            if (text == "Income" || text == "Expenses") {
                $("#amountDiv").hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "You can't add opening balances for Income or Expenses."
                });
            } else {
                $("#amountDiv").show();
            }

            $.ajax({
                url: '../../backend/data/get-sub-account-data.php',
                type: 'post',
                data: { acc_id: acc_id },
                success: function (response) {
                    $("#sub_account_name").html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            })
        })
    })
</script>