<div class="container py-4">
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
                    <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Add Customer</h3>
                    <form class="row g-3" method="post" action="../../backend/data/add-customer-data.php"
                        enctype="multipart/form-data">
                        <div class="col-md-12">
                            <label for="customerName" class="form-label">Name</label>
                            <input type="text" class="form-control" name="customerName" id="customerName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phoneNumber" class="form-label">Phone No.</label>
                            <input type="tel" name="phoneNumber" class="form-control" id="phoneNumber" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nicNumber" class="form-label">NIC No.</label>
                            <input type="text" name="nicNumber" class="form-control" id="nicNumber" required>
                        </div>
                        <div class="col-md-6">
                            <label for="customer_type" class="form-label">Customer Type</label>
                            <select name="customer_type" class="form-select" id="customer_type" required>
                                <!-- Options here -->
                                <option value="Individual">Individual</option>
                                <option value="Company">Company</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="customerAddress" class="form-label">Address</label>
                            <textarea name="customerAddress" id="customerAddress" rows="5" class="form-control"
                                required></textarea>
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
                        <div class="col-12 mt-5">
                            <button type="submit" id="submit" class="btn btn-primary">Add</button>
                            <button type="reset" class="btn btn-secondary">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).prop('title', 'Add Customer | Ginum');

    //navbar
    $("#dashboard").removeClass("active");
    $("#customer").addClass("active");

    $(document).ready(function () {
        $("#vat_lbl").hide();
        $("#br_lbl").hide();
        $("#vatNumber").hide();
        $("#businessRegNumber").hide();
        $("#tin").removeClass("col-md-4");
        $("#tin").addClass("col-md-12");

        $("submit").click(function (event) {
            event.preventDefault();
        })

        $("#customer_type").change(function () {
            if ($(this).val() == "Company") {
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
        })

    })
</script>