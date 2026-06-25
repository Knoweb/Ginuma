<?php
function getColorCode(float $bought_count, float $remaining_count): string
{
    $colorCode = "";
    // if() {
    //     //
    // }
    return $colorCode;
}

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];
    $company_id = $_SESSION['company_id'];
    // get all the details of the inventory item
    $sql = "SELECT * FROM item_tbl it INNER JOIN inventory_tbl invt ON (it.item_id=invt.item_id) WHERE it.company_id=? AND it.item_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $company_id, $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // from item_tbl
        $item_name = $row['item_name'];
        $units = $row['unit'];
        $bought_item_count = (float) $row['bought_item_count'];
        $bought_total = (float) $row['total_price'];
        $description = $row['description'];
        $date_added = $row['date_added'];

        // from inventory tbl
        $remaining_item_qty = (float) $row['qty'];
        $selling_price = (float) $row['unit_price'];
    }
}

?>

<div class="container py-4">
    <h2>Edit Item</h2>
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
        <form action="../../backend/data/add-item-data.php" method="post">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="item-type" class="form-label">Item Type</label>
                        <select name="item_type" id="item-type" class="form-select" required>
                            <option value="Good">Good</option>
                            <!-- <option value="Service">Service</option> -->
                        </select>
                    </div>
                </div>
                <div class="col-md-5" id="nameBox">
                    <label for="item-name" class="form-label">Item Name</label>
                    <input type="text" name="item_name" id="item-name" class="form-control" value="<?= $item_name ?>"
                        required>
                </div>
                <div class="col-md-3" id="unitBox">
                    <label for="item-units" class="form-label">Units</label>
                    <select name="item_units" id="item-units" class="form-select" required>
                        <!-- Display all the units -->
                        <option value="box" <?= $units == 'box' ? 'selected' : '' ?>>box</option>
                        <option value="cm" <?= $units == 'cm' ? 'selected' : '' ?>>cm</option>
                        <option value="dz" <?= $units == 'dz' ? 'selected' : '' ?>>dz</option>
                        <option value="ft" <?= $units == 'ft' ? 'selected' : '' ?>>ft</option>
                        <option value="g" <?= $units == 'g' ? 'selected' : '' ?>>g</option>
                        <option value="in" <?= $units == 'in' ? 'selected' : '' ?>>in</option>
                        <option value="kg" <?= $units == 'kg' ? 'selected' : '' ?>>kg</option>
                        <option value="km" <?= $units == 'km' ? 'selected' : '' ?>>km</option>
                        <option value="lb" <?= $units == 'lb' ? 'selected' : '' ?>>lb</option>
                        <option value="mg" <?= $units == 'mg' ? 'selected' : '' ?>>mg</option>
                        <option value="ml" <?= $units == 'ml' ? 'selected' : '' ?>>ml</option>
                        <option value="m" <?= $units == 'm' ? 'selected' : '' ?>>m</option>
                        <option value="pcs" <?= $units == 'pcs' ? 'selected' : '' ?>>pcs</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon3">Rs.</span>
                            <input type="text" class="form-control" id="amount" aria-describedby="basic-addon3"
                                value="<?= $selling_price ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="bought_item_count" class="form-label">Bought Item Count</label>
                        <input type="text" class="form-control" name="bought_item_count" id="bought_item_count"
                            value="<?= $bought_item_count ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="remaining_count" class="form-label">Remaining Item Count</label>
                        <input type="text" class="form-control" name="remaining_count" id="remaining_count"
                            value="<?= $remaining_item_qty ?>">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="5"
                    class="form-control"><?= $description ?></textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Save Item</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // title
        $(document).prop("title", "Add Items | Ginum");

        // navbar
        $("#dashboard").removeClass("active");
        $("#item").addClass("active");
        // change the unit dropdown visible or not
        $("#item-type").change(function () {
            if ($(this).val() == 'Good') {
                $("#unitBox").show();
                $("#nameBox").addClass("col-md-5");
                $("#nameBox").removeClass("col-md-8");
            } else {
                $("#unitBox").hide();
                $("#nameBox").removeClass("col-md-5");
                $("#nameBox").addClass("col-md-8");
            }
        })
    })
</script>