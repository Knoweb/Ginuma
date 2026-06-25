<?php
if (isset($_GET['id'])) {

    $id = $_GET['id'];
    $sql = "SELECT * FROM customer_tbl WHERE customer_id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $phoneNo = $row['phone_no'];
        $email = $row['email'];
        $nic = $row['nic'];
        $customer_type = $row['customer_type'];
        $address = $row['address'];
        $tin_no = $row['tin_no'];
        $vat_no = $row['vat_no'];
        $brNo = $row['business_reg_no'];
    }

    // check if the customer details is in the edit_requests table.
    $sql = "SELECT * FROM edit_requests WHERE status='pending' AND table_name='customer_tbl' AND record_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        // data already in the edit_requests table.
        $status = "pending";
    } else {
        $status = "";
    }

    ?>
    <style>
        .btn-warning.disabled,
        .btn-warning[disabled] {
            pointer-events: none;
        }
    </style>

    <section class="vh-100 gradient-custom">
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
            <div class="row d-flex justify-content-center align-items-center ">
                <div class="col col-xl-8">
                    <div class="card" style="border-radius: 1rem;">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Edit Customer</h3>
                            <form class="row g-3" method="post" id="editForm"
                                action="../../backend/data/edit-customer-data.php?id=<?= $id ?>"
                                enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <label for="customerName" class="form-label">Name</label>
                                    <input type="text" value="<?= $name ?>" class="form-control" name="customerName"
                                        id="customerName" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phoneNumber" class="form-label">Phone No.</label>
                                    <input type="tel" name="phoneNumber" value="<?= $phoneNo ?>" class="form-control"
                                        id="phoneNumber" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= $email ?>" id="email"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nicNumber" class="form-label">NIC No.</label>
                                    <input type="text" name="nicNumber" class="form-control" value="<?= $nic ?>"
                                        id="nicNumber" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="customer_type" class="form-label">Customer Type</label>
                                    <select name="customer_type" class="form-select" id="customer_type" required>
                                        <!-- Options here -->
                                        <option value="Individual" <?= $customer_type == 'Individual' ? 'selected' : '' ?>>
                                            Individual</option>
                                        <option value="Company" <?= $customer_type == 'Company' ? 'selected' : '' ?>>
                                            Company
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label for="customerAddress" class="form-label">Address</label>
                                    <textarea name="customerAddress" id="customerAddress" rows="5" class="form-control"
                                        required><?= $address ?></textarea>
                                </div>
                                <div class="col-md-4" id="tin">
                                    <label for="tinNumber" class="form-label">TIN No.</label>
                                    <input type="text" name="tinNumber" value="<?= $tin_no ?>" class="form-control"
                                        id="tinNumber">
                                </div>
                                <div class="col-md-4">
                                    <label for="vatNumber" class="form-label" id="vat_lbl">VAT No.</label>
                                    <input type="text" name="vatNumber" value="<?= $vat_no ?>" class="form-control"
                                        id="vatNumber">
                                </div>
                                <div class="col-md-4">
                                    <label for="businessRegNumber" id="br_lbl" class="form-label">Business Registration
                                        No.</label>
                                    <input type="text" name="businessRegNumber" value="<?= $brNo ?>" class="form-control"
                                        id="businessRegNumber">
                                </div>
                                <div class="col-12 mt-5">
                                    <!-- Edit Button -->
                                    <button type="submit" id="editButton" class="btn btn-warning">Edit</button>

                                    <!-- Pending State Button (initially hidden) -->
                                    <button id="pendingButton" class="btn btn-warning" type="button" disabled
                                        style="display: none;">
                                        <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                                        <span role="status">Pending...</span>
                                    </button>
                                    <button type="reset" class="btn btn-secondary" id="cls">Clear</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).prop('title', 'Add Customer | Ginum');

        $(document).ready(function () {
            const status = "<?= $status ?>";
            const role = "<?= $_SESSION['role'] ?>";

            // make the pending button visible
            if (status == 'pending' && role == 'User') {
                $('#editButton').hide();
                $('#pendingButton').show();
                $("#cls").hide();
            } else {

            }

            //navbar
            $("#dashboard").removeClass("active");
            $("#customer").addClass("active");

            if ($("#customer_type").val() == 'Individual') {
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

            $("#customer_type").change(function () {
                if ($(this).val() == "Business") {
                    $("#vat_lbl").show();
                    $("#br_lbl").show();
                    $("#vatNumber").show();
                    $("#businessRegNumber").show();
                    $("#tin").removeClass("col-md-12");
                    $("#tin").addClass("col-md-4");
                } else {
                    $("#vat_lbl").hide();
                    $("#br_lbl").hide();
                    $("#vatNumber").hide();
                    $("#businessRegNumber").hide();
                    $("#tin").removeClass("col-md-4");
                    $("#tin").addClass("col-md-12");
                }
            });

            $('#editForm').on('submit', function (event) {
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

        })
    </script>

    <?php
} else {
    header("Location: ./Company.php?page=show-customers");
}
?>