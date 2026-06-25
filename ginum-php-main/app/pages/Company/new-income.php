<style>
    .upload-receipts {
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background-color: #f9f9f9;
    }

    .drop-zone {
        padding: 20px;
        border: 2px dashed #ccc;
        border-radius: 10px;
        cursor: pointer;
    }

    .drop-zone:hover {
        border-color: #aaa;
    }

    #receipt-upload {
        display: none;
    }

    .drop-zone label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        cursor: pointer;
    }

    .drop-zone i {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .file-size-note {
        font-size: 12px;
        color: #666;
    }
</style>
<div class="container py-5 h-100">
    <h2>New Other Income</h2>
    <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <form action="../../backend/data/record-income-data.php" method="post" enctype="multipart/form-data">
            <div class="row">
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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="<?= date("Y-m-d") ?>">
                    </div>
                    <div class="mb-3">
                        <label for="incomeAcc" class="form-label">Income Account</label>
                        <select name="incomeAcc" id="incomeAcc" class="form-select" required>
                            <?php
                            $sql = "SELECT sub_account_id, sub_account_name FROM sub_account_tbl sat INNER JOIN account_tbl at ON (sat.account_id = at.account_id) WHERE at.account_name='Income' AND NOT sat.sub_account_name='Sales'";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <option value="<?= $row['sub_account_id'] ?>"><?= $row['sub_account_name'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon3"><?= $_SESSION['currency_code'] ?></span>
                            <input type="text" class="form-control" name="amount" id="amount"
                                aria-describedby="basic-addon3" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Customized file upload  -->
                    <div class="container">
                        <div class="upload-receipts mt-3">
                            <h5>Drag or Drop your Invoice</h5>
                            <div class="drop-zone">
                                <input type="file" id="receipt-upload" name="invoice" accept=".pdf">
                                <label for="receipt-upload">
                                    <i class="bi bi-upload"></i>
                                    <span>Upload your PDF</span>
                                </label>
                                <p class="file-size-note">Maximum file size allowed is 5MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label for="transfer" class="form-label">Account</label>
                    <select name="transfer" id="transfer" class="form-select">
                        <?php
                        $sql = "SELECT DISTINCT sat.*, at.account_name 
                                FROM sub_account_tbl sat 
                                INNER JOIN account_tbl at ON sat.account_id = at.account_id 
                                WHERE at.account_name = 'Assets' OR at.account_name = 'Liabilities';";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <option value="<?= $row['account_id'] ?>:<?=  $row['sub_account_id'] ?>">
                                    <?= $row['account_name'] ?>: <?= $row['sub_account_name'] ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="reference" class="form-label">Reference/ Invoice No.</label>
                            <input type="text" name="reference" id="reference" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                    </div>
                    <hr>
                </div>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary" type="submit">Save</button>
                <button class="btn btn-secondary" type="reset">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // navbar
        $(document).ready(function () {
            // navbar
            $("#dashboard").removeClass("active");
            $("#sales").addClass("active");

            // page title
            $(document).prop("title", "New Income | Ginum");
        })
    })
</script>