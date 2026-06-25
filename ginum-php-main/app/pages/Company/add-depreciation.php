<div class="container py-4">
    <h2>Add Depreciation</h2>
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
        <form action="../../backend/data/add-depreciation-data.php" method="post">
            <div class="mb-3">
                <label for="type" class="form-label">Asset Type</label>
                <select name="sub_account_type_id" id="acc" class="form-select">
                    <option value="">-- Select a Asset Type --</option>
                    <?php
                    $sql = "SELECT * FROM company_sub_account_balance csab INNER JOIN sub_account_type_tbl satt ON (csab.sub_account_type_id=satt.sub_account_type_id) INNER JOIN sub_account_tbl sat ON (satt.sub_account_id=sat.sub_account_id) INNER JOIN account_tbl at ON (sat.account_id=at.account_id) WHERE csab.company_id='" . $_SESSION['company_id'] . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <option value="<?= $row['sub_account_type_id'] ?>"><?= $row['sub_account_name'] ?>:
                                <?= $row['type_name'] ?>
                            </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="initial_value" class="form-label">Initial Value</label>
                <input type="number" name="initial_value" id="initial_value" class="form-control" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="useful_life_years" class="form-label">Useful Life (Years)</label>
                <input type="number" name="useful_life_years" id="useful_life_years" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="salvage_value" class="form-label">Salvage Value</label>
                <input type="number" name="salvage_value" id="salvage_value" class="form-control" step="0.01">
            </div>

            <div class="mb-3">
                <label for="depreciation_date" class="form-label">Depreciation Date</label>
                <input type="date" name="depreciation_date" id="depreciation_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="depreciation_rate" class="form-label">Depreciation Rate</label>
                <input type="number" name="depreciation_rate" id="depreciation_rate" class="form-control"
                    step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="accumulated_depreciation" class="form-label">Accumulated Depreciation</label>
                <input type="number" name="accumulated_depreciation" id="accumulated_depreciation" class="form-control"
                    step="0.01" required readonly>
            </div>

            <div class="mb-3">
                <label for="remaining_life_years" class="form-label">Remaining Useful Life (Years)</label>
                <input type="number" name="remaining_life_years" id="remaining_life_years" class="form-control"
                    required>
            </div>

            <div class="mb-3">
                <label for="depreciation_method" class="form-label">Depreciation Method</label>
                <select name="depreciation_method" id="depreciation_method" class="form-select" required>
                    <option value="Straight Line" selected>Straight Line</option>
                    <option value="Reducing Balance">Reducing Balance</option>
                    <option value="Sum of Years' Digits">Sum of Years' Digits</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // title
        $(document).prop("title", "Add Account | Ginum");

        //navbar
        $("#dashboard").removeClass("active");
        $("#depreciation").addClass("active");

        // get the sub_account_type id and fetch the details about that asset
        $("#acc").change(function() {
            const account_id = $(this).val();
            $.ajax({
                url: '../../backend/data/get-asset-details.php',
                method: 'GET',
                data: {
                    sub_account_type_id: account_id,
                },
                success: function(data) {
                    const response = JSON.parse(data);
                    $("#initial_value").val(response.data.initial_value);
                    $("#depreciation_date").val(response.data.date_added);
                },
                error: function(err) {
                    console.error(err);
                }
            });
        });
    })
</script>