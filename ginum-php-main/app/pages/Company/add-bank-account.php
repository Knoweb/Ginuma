<div class="container py-4">
    <h2>New Bank Account</h2>
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
        <form action="../../backend/data/add-bank-account-data.php" method="post">
            <div class="mb-3">
                <label for="bank_name" class="form-label">Bank Name</label>
                <input type="text" name="bank_name" id="bank_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="branch" class="form-label">Branch</label>
                <input type="text" name="branch" id="branch" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="account_name" class="form-label">Account Name</label>
                <input type="text" name="account_name" id="account_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="account_number" class="form-label">Account Number</label>
                <input type="text" name="account_number" id="account_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="opening_balance" class="form-label">Opening Balance</label>
                <input type="text" name="opening_balance" id="opening_balance" class="form-control" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-secondary">Rest</button>
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
    });
</script>