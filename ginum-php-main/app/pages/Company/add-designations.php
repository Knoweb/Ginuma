<div class="container py-4">
    <h2>New Designation</h2>
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
        <form action="../../backend/data/add-designation-data.php" method="post">
            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select name="department_id" id="department_id" class="form-select" required>
                    <?php
                    $sql = "SELECT * FROM department_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                    <option value="<?= $row['department_id'] ?>" selected><?= $row['department_name'] ?> </option>
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
            <div class="mb-3">
                <label for="designation_name" class="form-label">Designation Name</label>
                <input type="text" name="designation_name" id="designation_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Save</button>
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
    $("#department").addClass("active");
})
// Open Department Modal
$("#addDepartmentLink").click(function(e) {
    e.preventDefault();
    $("#addDepartmentModal").modal('show');
});
</script>