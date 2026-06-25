<div class="container py-4">
    <h2>New Project</h2>
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
    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDepartmentModalLabel">Add New Department</h5>

                </div>
                <div class="modal-body">
                    <form class="row g-3" method="post" action="../../backend/data/add-department-data.php"
                        enctype="multipart/form-data">
                        <div class="col-md-12">
                            <label for="dpt_name" class="form-label">Department Name</label>
                            <input type="text" class="form-control" name="dpt_name" id="dpt_name" required>
                        </div>
                        <div class="col-md-12">
                            <label for="dpt_code" class="form-label">Department Code</label>
                            <input type="tel" name="dpt_code" class="form-control" id="dpt_code" required>
                        </div>
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control"></textarea>
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" id="submit" name="add" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                </div>
                <div class="modal-body">
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <form action="../../backend/data/add-order-data.php" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="project_code" class="form-label">Order Code</label>
                        <input type="text" name="project_code" id="project_code" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="project_name" class="form-label">Order Name</label>
                        <input type="text" name="project_name" id="project_name" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="project_description" class="form-label">Description</label>
                    <textarea name="project_description" id="project_description" rows="5"
                        class="form-control"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select name="customer_id" id="customer_id" class="form-select" required>
                            <?php
                            $sql = "SELECT * FROM customer_tbl WHERE status=1";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                            <option value="<?= $row['customer_id'] ?>"><?= $row['name'] ?>
                                <?php
                                }
                            }
                                ?>
                        </select>
                        <small>
                            <a href="#" id="addCustomerLink">
                                <i class="fa fa-plus-circle"></i> Add New Customer
                            </a>
                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select" required>
                            <option>-- Select Department --</option>
                            <?php
                            $sql = "SELECT * FROM department_tbl";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                            <option value="<?= $row['department_id'] ?>"><?= $row['department_name'] ?>
                                <?php
                                }
                            }
                                ?>
                        </select>
                        <small>
                            <a href="#" id="addDepartmentLink">
                                <i class="fa fa-plus-circle"></i> Add New Department
                            </a>
                        </small>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="billing_method" class="form-label">Billing Method</label>
                        <select name="billing_method" id="billing_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Online Banking Tranfer">Online Banking Tranfer</option>
                            <option value="Credit">Credit</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="<?= date("Y-m-d") ?>">
                    </div>
                    <div class="mb-3">
                        <label for="working_status" class="form-label">Working Status</label>
                        <select name="working_status" id="working_status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Completed">Completed</option>
                            <option value="Running">Running</option>
                            <option value="Pending">Pending</option>
                            <option value="Not Started" selected>Not Started</option>
                            <option value="Cancled">Cancled</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="budget" class="form-label">Budget</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon3"><?= $_SESSION['currency_code'] ?></span>
                            <input type="text" class="form-control" name="budget" id="budget"
                                aria-describedby="basic-addon3" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="Low" class="text-primary">Low</option>
                            <option value="Medium" class="text-warning">Medium</option>
                            <option value="High" class="text-danger" selected>High</option>
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <!-- Add Tasks -->
            <div class="mb-3">
                <label for="tasks" class="form-label">Tasks</label>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tasks">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Task Name</th>
                                <th class="text-center">Priority</th>
                                <th class="text-center">Created Date</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Description</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody"></tbody>
                    </table>
                    <button class="btn btn-md btn-primary" id="addBtn" type="button">
                        <i class='bx bx-plus'></i>
                    </button>
                </div>
            </div>
            <div class="mb-3 mt-4">
                <button type="submit" class="btn btn-primary" name="save">Save</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // for page title
    $(document).prop('title', 'Add Project | Ginum');

    //navbar
    $("#dashboard").removeClass("active");
    $("#orders").addClass("active");
    // for table
    let count = 1; // Initialize row count

    // Adding row on click to Add New Row button
    $('#addBtn').click(function() {
        let dynamicRowHTML = `
        <tr class="rowClass"> 
            <td class="row-index text-center"> 
                ${count}
            </td>
            <td class="text-center"> 
                <input type='text' name='taskNames[]' class='form-control' title='Task Name'>
            </td> 
            <td class="text-center"> 
                <select name="task_priority[]" id="task_priority" class="form-select">
                        <option value="Low" class="text-primary">Low</option>
                        <option value="Medium" class="text-warning">Medium</option>
                        <option value="High" class="text-danger" selected>High</option>
                    </select>
            </td> 
            <td class="text-center"> 
                <input type='date' name='createdDates[]' class='form-control quantity'  title='Created Date'>
            </td> 
            <td class="text-center"> 
                <input type='date' name='dueDates[]' class='form-control' title='Due Date'>
            </td> 
            <td class="text-center"> 
                <input type='text' name='task_descriptions[]' class='form-control' title='Description'>
            </td> 
            <td class="text-center"> 
                <button class="btn btn-danger remove" type="button"><i class='bx bx-x'></i></button> 
            </td> 
        </tr>`;
        $('#tbody').append(dynamicRowHTML);
        count++;
    });
    // Remove row on click of Remove button
    $('#tbody').on('click', '.remove', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });
});
// Open Department Modal
$("#addDepartmentLink").click(function(e) {
    e.preventDefault();
    $("#addDepartmentModal").modal('show');
});
// Open Customer Modal
$("#addCustomerLink").click(function(e) {
    e.preventDefault();
    $("#addCustomerModal").modal('show');
});

$(document).ready(function() {
    $("#vat_lbl").hide();
    $("#br_lbl").hide();
    $("#vatNumber").hide();
    $("#businessRegNumber").hide();
    $("#tin").removeClass("col-md-4");
    $("#tin").addClass("col-md-12");

    $("submit").click(function(event) {
        event.preventDefault();
    })

    $("#customer_type").change(function() {
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