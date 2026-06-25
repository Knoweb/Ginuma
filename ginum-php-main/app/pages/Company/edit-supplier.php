<?php
if (isset($_GET['id'])) {

    $id = $_GET['id'];
    $sql = "SELECT * FROM supplier_tbl WHERE supplier_id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $supplierName = $row['supplier_name'];
        $supplierType = $row['supplier_type'];
        $address = $row['address'];
        $phoneNo = $row['phone_no'];
        $phoneNo = $row['phone_no'];
        $email = $row['email'];
        $vatNo = $row['vat_no'];
        $tinNo = $row['tin_no'];
        $businessRegNumber = $row['business_reg_no'];
        $itemCategoryId = $row['item_category_id'];

        // check if the customer details is in the edit_requests table.
        $status = "";
        $sql = "SELECT * FROM edit_requests WHERE table_name='supplier_tbl' AND record_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // data already in the edit_requests table.
            $status = "pending";
        }

    }
    ?>

    <style>
        .btn-warning.disabled,
        .btn-warning[disabled] {
            pointer-events: none;
        }
    </style>

    <div class="container py-5 h-100">
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

        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-8">
                <div class="card" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Add Supplier</h3>
                        <form class="row g-3" method="post" id="editForm"
                            action="../../backend/data/edit-supplier-data.php?id=<?= $id ?>" enctype="multipart/form-data">
                            <div class="col-md-12">
                                <label for="supplierName" class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" value="<?= $supplierName ?>" name="supplierName"
                                    id="supplierName" required>
                            </div>
                            <div class="col-md-6">
                                <label for="supplierEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= $email ?>" name="supplierEmail"
                                    id="supplierEmail" required>
                            </div>
                            <div class="col-md-6">
                                <label for="mobileNo" class="form-label">Mobile No.</label>
                                <input type="tel" class="form-control" value="<?= $phoneNo ?>" name="mobileNo" id="mobileNo"
                                    required>
                            </div>
                            <div class="col-md-12">
                                <label for="supplierAddress" class="form-label">Address</label>
                                <textarea name="supplierAddress" id="supplierAddress" rows="3" class="form-control"
                                    required><?= $address ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="supplierType" class="form-label">Supplier Type</label>
                                <select name="supplierType" class="form-select" id="supplierType" required>
                                    <!-- Add options here -->
                                    <option value="Individual" <?= $supplierType == 'Individual' ? 'selected' : '' ?>>
                                        Individual
                                    </option>
                                    <option value="Company" <?= $supplierType == 'Company' ? 'selected' : '' ?>>Company
                                    </option>
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
                                            if ($itemCategoryId == $row['item_category_id']) {
                                                ?>
                                                <option value="<?= $row['item_category_id'] ?>" selected>
                                                    <?= $row['item_category_name'] ?>
                                                </option>
                                                <?php
                                            } else {
                                                ?>
                                                <option value="<?= $row['item_category_id'] ?>"><?= $row['item_category_name'] ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4" id="tin">
                                <label for="tinNumber" class="form-label">TIN No.</label>
                                <input type="text" name="tinNumber" value="<?= $tinNo ?>" class="form-control"
                                    id="tinNumber">
                            </div>
                            <div class="col-md-4">
                                <label for="vatNumber" class="form-label" id="vat_lbl">VAT No.</label>
                                <input type="text" name="vatNumber" value="<?= $vatNo ?>" class="form-control"
                                    id="vatNumber">
                            </div>
                            <div class="col-md-4">
                                <label for="businessRegNumber" id="br_lbl" class="form-label">Business Registration
                                    No.</label>
                                <input type="text" name="businessRegNumber" value="<?= $businessRegNumber ?>"
                                    class="form-control" id="businessRegNumber">
                            </div>
                            <div class="col-12">
                                <!-- Edit Button -->
                                <button type="submit" id="editButton" class="btn btn-warning">Edit</button>

                                <!-- Pending State Button (initially hidden) -->
                                <button id="pendingButton" class="btn btn-warning" type="button" disabled
                                    style="display: none;">
                                    <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                                    <span role="status">Pending...</span>
                                </button>
                                <button type="reset" class="btn btn-secondary">Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).prop('title', 'Edit Supplier | Ginum');

        $(document).ready(function () {
            // navbar
            $("#dashboard").removeClass("active");
            $("#supplier").addClass("active");

            if ($("#supplierType").val() == 'Individual') {
                $("#vat_lbl").hide();
                $("#br_lbl").hide();
                $("#vatNumber").hide();
                $("#businessRegNumber").hide();
                $("#tin").removeClass("col-md-4");
                $("#tin").addClass("col-md-12");

            } else {
                $("#vat_lbl").show();
                $("#br_lbl").show();
                $("#vatNumber").show();
                $("#businessRegNumber").show();
                $("#tin").addClass("col-md-4");
                $("#tin").removeClass("col-md-12");
            }

            $("#supplierType").change(function () {
                if ($(this).val() == "Business") {
                    $("#vat_lbl").show();
                    $("#br_lbl").show();
                    $("#vatNumber").show();
                    $("#businessRegNumber").show();
                    // $("#brReport").show();
                    $("#tin").removeClass("col-md-12");
                    $("#tin").addClass("col-md-4");
                } else {
                    $("#vat_lbl").hide();
                    $("#br_lbl").hide();
                    $("#vatNumber").hide();
                    $("#businessRegNumber").hide();
                    // $("#brReport").hide();
                    $("#tin").removeClass("col-md-4");
                    $("#tin").addClass("col-md-12");
                }
            });

            const status = "<?= $status ?>";
            const role = "<?= $_SESSION['role'] ?>";

            // make the pending button visible
            if (status == 'pending' && role == 'User') {
                $('#editButton').hide();
                $('#pendingButton').show();
                $("#cls").hide();
            }

            $('#editForm').off('submit').on('submit', function (event) {
                event.preventDefault(); // Prevent the form from submitting immediately

                // Show the pending button and hide the original button
                if (role == 'User') {
                    $('#editButton').hide();
                    $('#pendingButton').show();
                }

                // Submit the form using AJAX
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function (response) {
                        console.log(response);
                        let data = JSON.parse(response);
                        Swal.fire({
                            icon: 'success',
                            title: data['message']
                        });
                    },
                    error: function () {
                        // Handle error
                        Swal.fire({
                            icon: 'error',
                            title: 'An error occurred while processing your request.'
                        });
                        // Show the original button again if an error occurs
                        if (role == 'User') {
                            $('#editButton').show();
                            $('#pendingButton').hide();
                        }
                    }
                });
            });

        });
    </script>

    <?php
} else {
    // header("Location: ./Company.php?page=show-suppliers");
}
?>