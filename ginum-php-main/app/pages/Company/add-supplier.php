<div class="container py-5 ">

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

    <div class="row d-flex justify-content-center align-items-center ">
        <div class="col col-xl-8">
            <div class="card shadow-lg" style="border-radius: 1rem;">
                <div class="card-body p-4 p-md-5">
                    <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Add Supplier</h3>
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
                            <label for="br_report" class="form-label">Upload BR Report</label>
                            <input type="file" class="form-control" name="br_report" id="br_report" accept=".pdf">
                        </div> -->
                        <div class="col-12">
                            <button type="submit" name="add" class="btn btn-primary">Add</button>
                            <button type="reset" class="btn btn-secondary">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).prop('title', 'Add Supplier | Ginum');

//navbar
$("#dashboard").removeClass("active");
$("#supplier").addClass("active");

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