<div class="container py-4">
    <h2>Add Item</h2>
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
    <!-- Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">Add Supplier</h5>
                </div>
                <div class="modal-body">
                    <form class="row g-3" method="post" action="../../backend/data/add-supplier-data.php"
                        enctype="multipart/form-data">
                        <div class="col-md-12">
                            <label for="supplierName" class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" name="supplierName" id="supplierName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="supplierEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" name="supplierEmail" id="supplierEmail" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mobileNo" class="form-label">Mobile No.</label>
                            <input type="tel" class="form-control" name="mobileNo" id="mobileNo" required>
                        </div>
                        <div class="col-md-12">
                            <label for="supplierAddress" class="form-label">Address</label>
                            <textarea name="supplierAddress" id="supplierAddress" rows="3" class="form-control"
                                required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="supplierType" class="form-label">Supplier Type</label>
                            <select name="supplierType" class="form-select" id="supplierType" required>
                                <!-- Add options here -->
                                <option value="Individual">Individual</option>
                                <option value="Company">Company</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="itemCategory" class="form-label">Item Category</label>
                            <select name="itemCategory" class="form-select" id="itemCategory" required>
                                <option value="">Select Category</option>
                                <!-- Add options here -->
                                <?php
                                $sql = "SELECT * FROM item_category_tbl";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                ?>
                                <option value="<?= $row['item_category_id'] ?>"><?= $row['item_category_name'] ?>
                                </option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4" id="tin">
                            <label for="tinNumber" class="form-label">TIN No.</label>
                            <input type="text" name="tinNumber" class="form-control" id="tinNumber">
                        </div>
                        <div class="col-md-4">
                            <label for="vatNumber" class="form-label" id="vat_lbl">VAT No.</label>
                            <input type="text" name="vatNumber" class="form-control" id="vatNumber">
                        </div>
                        <div class="col-md-4">
                            <label for="businessRegNumber" id="br_lbl" class="form-label">Business Registration
                                No.</label>
                            <input type="text" name="businessRegNumber" class="form-control" id="businessRegNumber">
                        </div>
                        <!-- <div class="col-12" id="brReport">
                            <label for="br_report" class="form-label">Upload BR Report (optional)</label>
                            <input type="file" class="form-control" name="br_report" id="br_report" accept=".pdf">
                        </div> -->
                        <div class="col-12">
                            <button type="submit" name="add" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <form action="../../backend/data/add-item-data.php" method="post">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3" id="typeBox">
                        <label for="item-type" class="form-label">Item Type</label>
                        <select name="item_type" id="item-type" class="form-select" required>
                            <option value="Good" selected>Good</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4" id="nameBox">
                    <label for="item-name" class="form-label">Item Name</label>
                    <input type="text" name="item_name" id="item-name" class="form-control" required>
                </div>
                <div class="col-md-2" id="unitBox">
                    <label for="item-units" class="form-label">Units</label>
                    <select name="item_units" id="item-units" class="form-select" required>
                        <!-- Display all the units -->
                        <option value="">-- Choose One --</option>
                        <option value="box">box</option>
                        <option value="cm">cm</option>
                        <option value="dz">dz</option>
                        <option value="ft">ft</option>
                        <option value="g">g</option>
                        <option value="in">in</option>
                        <option value="kg">kg</option>
                        <option value="km">km</option>
                        <option value="lb">lb</option>
                        <option value="mg">mg</option>
                        <option value="ml">ml</option>
                        <option value="m">m</option>
                        <option value="pcs">pcs</option>
                    </select>
                </div>
                <div class="col-md-3" id="lw_box">
                    <label for="lw_lbl" class="form-label" id="lw_lbl">Length/ Weight/ Volume</label>
                    <input type="text" name="lw_value" id="lw_lbl" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="qty" class="form-label">Quantity</label>
                        <input type="text" name="qty" id="qty" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="unit_price" class="form-label">Unit Price</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon3"><?= $_SESSION['currency_code'] ?></span>
                            <input type="text" name="unit_price" class="form-control" id="unit_price"
                                aria-describedby="basic-addon3" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="tax" class="form-label">Tax</label>
                        <div class="input-group">
                            <input type="text" id="tax" name="tax" class="form-control" aria-label="tax"
                                aria-describedby="basic-addon2">
                            <span class="input-group-text" id="basic-addon2">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier Name</label>
                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                            <?php
                            $sql = "SELECT * FROM supplier_tbl WHERE status=1";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                            <option value="<?= $row['supplier_id'] ?>" selected>
                                <?= $row['supplier_name'] ?>
                            </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <small>
                            <a href="#" id="addSupplierLink">
                                <i class="fa fa-plus-circle"></i> Add New Supplier
                            </a>
                        </small>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="tranfer" class="form-label">Tranfer</label>
                <select name="tranfer" id="tranfer" class="form-select">
                    <?php
                    $sql = "SELECT DISTINCT sat.*, at.account_name 
                            FROM sub_account_tbl sat 
                            INNER JOIN account_tbl at ON sat.account_id = at.account_id 
                            WHERE at.account_name = 'Assets' OR at.account_name = 'Liabilities';";
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
            <div class="mb-3">
                <label for="" class="form-label text-danger" id="output"></label>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="5" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Save Item</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // title
    $(document).prop("title", "Add Inventory Items | Ginum");

    //navbar
    $("#dashboard").removeClass("active");
    $("#items").addClass("active");

    // change the unit dropdown visible or not
    $("#item-type").change(function() {
        if ($(this).val() == 'Good') {
            $("#unitBox").show();
            $("#nameBox").addClass("col-md-5");
            $("#nameBox").removeClass("col-md-8");
        } else {
            $("#unitBox").hide();
            $("#nameBox").removeClass("col-md-5");
            $("#nameBox").addClass("col-md-8");
        }
    });

    // listener for unit select box
    $("#item-units").change(function() {
        const val = $(this).val();
        const lengthUnits = ['cm', 'ft', 'in', 'km', 'm'];
        const weightUnits = ['g', 'kg', 'lb', 'mg'];
        const volumeUnits = ['ml'];

        const isLengthUnit = lengthUnits.includes(val);
        const isWeightUnit = weightUnits.includes(val);
        const isVolumeUnit = volumeUnits.includes(val);

        if (val === "box" || val === 'dz' || val === 'pcs') {
            $("#lw_box").hide();
            $("#nameBox").removeClass("col-md-4").addClass("col-md-5");
            $("#unitBox").removeClass("col-md-2").addClass("col-md-4");
        } else {
            $("#lw_box").show();
            $("#nameBox").removeClass("col-md-5").addClass("col-md-4");
            $("#unitBox").removeClass("col-md-4").addClass("col-md-2");

            if (isLengthUnit) {
                $("#lw_lbl").text('Length');
            } else if (isWeightUnit) {
                $("#lw_lbl").text('Weight');
            } else if (isVolumeUnit) {
                $("#lw_lbl").text('Volume');
            }
        }
    });

    // Function to calculate the total price
    function calculateTotal() {
        let unitPrice = parseFloat($("#unit_price").val()) || 0;
        let qty = parseFloat($("#qty").val()) || 0;
        let tax = parseFloat($("#tax").val()) || 0;

        let output = (unitPrice * qty) + ((unitPrice * qty) * tax / 100);
        $("#output").html(`Total: ${output.toFixed(2)}`);
    }

    // Attach the calculation function to keyup event on all relevant inputs
    $("#unit_price, #qty, #tax").keyup(function() {
        calculateTotal();
    });

    // catch the item unit from the drop down menu and add a new length input box to the form
    $("#item-units").change(function() {
        const unit = $(this).val();
        console.log(unit);
    })

});
// Open Department Modal
$("#addSupplierLink").click(function(e) {
    e.preventDefault();
    $("#addSupplierModal").modal('show');
});

$(document).ready(function() {
    $("#vat_lbl").hide();
    $("#br_lbl").hide();
    $("#vatNumber").hide();
    $("#businessRegNumber").hide();
    $("#brReport").hide();
    $("#tin").removeClass("col-md-4");
    $("#tin").addClass("col-md-12");

    $("#supplierType").change(function() {
        if ($(this).val() == "Company") { // Change from "Business" to "Company"
            $("#vat_lbl").show();
            $("#br_lbl").show();
            $("#vatNumber").show();
            $("#businessRegNumber").show();
            $("#brReport").show();
            $("#tin").removeClass("col-md-12");
            $("#tin").addClass("col-md-4");
        } else {
            $("#vat_lbl").hide();
            $("#br_lbl").hide();
            $("#vatNumber").hide();
            $("#businessRegNumber").hide();
            $("#brReport").hide();
            $("#tin").removeClass("col-md-4");
            $("#tin").addClass("col-md-12");
        }
    });
});
</script>