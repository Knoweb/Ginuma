<?php

if (isset($_GET['id'])) {

    $id = $_GET['id'];
    $sql = "SELECT * FROM department_tbl WHERE department_id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $department_name = $row['department_name'];
        $department_code = $row['department_code'];
        $description = $row['description'];
    }

    // check if the customer details is in the edit_requests table.
    $status = "";
    $sql = "SELECT * FROM edit_requests WHERE table_name='department_tbl' AND record_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // data already in the edit_requests table.
        $status = "pending";
    }

    ?>

    <style>
        .btn-warning.disabled,
        .btn-warning[disabled] {
            pointer-events: none;
        }
    </style>

    <section>
        <div class="container py-5">
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
            <div class="container">
                <h3 class="mb-3 pb-2 pb-md-0">Edit Department</h3>
                <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
                    <form class="row g-3" id="editForm" method="post"
                        action="../../backend/data/edit-department-data.php?id=<?= $id ?>" enctype="multipart/form-data">
                        <div class="col-md-12">
                            <label for="dpt_name" class="form-label">Department Name</label>
                            <input type="text" class="form-control" name="dpt_name" id="dpt_name"
                                value="<?= $department_name ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label for="dpt_code" class="form-label">Department Code</label>
                            <input type="tel" name="dpt_code" class="form-control" id="dpt_code"
                                value="<?= $department_code ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="form-control"><?= $description ?></textarea>
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
                            <button type="reset" class="btn btn-secondary">Clear</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <script>
        $(document).prop('title', 'Edit Department | Ginum');
        $(document).ready(function () {
            //navbar
            $("#dashboard").removeClass("active");
            $("#departmtnt").addClass("active");

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
    header("Location: ./Company.php?page=show-departments");
}
?>