<?php

require_once '../../backend/connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;

if (isset($_GET['id'])) {

    $id = $_GET['id'];
    $sql = "SELECT * FROM employee_tbl et INNER JOIN designation_tbl dst ON (et.designation_id=dst.designation_id) INNER JOIN department_tbl dpt ON (dst.department_id=dpt.department_id) WHERE et.employee_id='" . $id . "' AND et.company_id='" . $_SESSION['company_id'] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
        $gender = $row['gender'];
        $designationId = $row['designation_id'];
        $address = $row['address'];
        $mobileNo = $row['mobileNo'];
        $dob = $row['dob'];
        $nic = $row['nic'];
        $epfNo = $row['epf_no'];
        $email = $row['email'];
        $dateJoined = $row['date_joined'];
        $department_id = $row['department_id'];

        // check if the customer details is in the edit_requests table.
        $status = "";
        $sql = "SELECT * FROM edit_requests WHERE table_name='employee_tbl' AND record_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // data already in the edit_requests table.
            $status = "pending";
        }

    }
    ?>
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
                <div class="card" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Edit Employee</h3>
                        <form class="row g-3" method="post" id="editForm"
                            action="../../backend/data/edit-employee-data.php?id=<?= $id ?>&dpt_id=<?= $department_id ?>&designationId=<?= $designationId ?>"
                            enctype="multipart/form-data">
                            <div class="col-md-6">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" value="<?= $firstName ?>" name="firstName"
                                    id="firstName" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" value="<?= $lastName ?>" name="lastName"
                                    id="lastName" required>
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" class="form-select" id="gender" required>
                                    <option value="Male" <?= $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $gender == 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= $gender == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" value="<?= $dob ?>" name="dob" id="dob" required>
                            </div>
                            <div class="col-md-4">
                                <label for="epf_no" class="form-label">EPF No.</label>
                                <input type="text" class="form-control" name="epf_no" value="<?= $epfNo ?>" id="epf_no"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="nic" class="form-label">NIC No.</label>
                                <input type="text" class="form-control" name="nic" value="<?= $nic ?>" id="nic" required>
                            </div>
                            <div class="col-md-4">
                                <label for="mobileNo" class="form-label">Mobile No.</label>
                                <input type="tel" class="form-control" name="mobileNo" value="<?= $mobileNo ?>"
                                    id="mobileNo" required>
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" value="<?= $email ?>" name="email" id="email"
                                    required>
                            </div>
                            <div class="col-md-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" id="address" rows="4" class="form-control"
                                    required><?= $address ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="date_joined" class="form-label">Date of joining</label>
                                <input type="date" class="form-control" value="<?= $dateJoined ?>" name="date_joined"
                                    id="date_joined" required>
                            </div>
                            <div class="col-md-4">
                                <label for="department" class="form-label">Department</label>
                                <select name="dpt_id" id="department" class="form-select dpt">
                                    <option>--Choose a Department --</option>
                                    <?php
                                    $sql = "SELECT * FROM department_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            if ($row['department_id'] == $department_id) {
                                                ?>
                                                <option value="<?= $row['department_id'] ?>" selected><?= $row['department_name'] ?>
                                                </option>
                                                <?php
                                            } else {
                                                ?>
                                                <option value="<?= $row['department_id'] ?>"><?= $row['department_name'] ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="designation" class="form-label">Designation</label>
                                <select name="designation" id="designation" class="form-select" required></select>
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
    </section>

    <script>
        $(document).prop('title', 'Edit Employee | Ginum');

        $(document).ready(function () {
            // navbar
            $("#dashboard").removeClass("active");
            $("#employee").addClass("active");

            var dpt_id = $(".dpt").val();
            $.ajax({
                url: "../../backend/data/get-designation-data.php",
                method: "POST",
                data: {
                    dpt_id: dpt_id,
                    designationId: "<?= $designationId ?>"
                },
                success: function (data) {
                    $("#designation").html(data);
                }
            });
            $(".dpt").change(function () {
                var dpt_id = $(this).val();
                $.ajax({
                    url: "../../backend/data/get-designation-data.php",
                    method: "POST",
                    data: {
                        dpt_id: dpt_id,
                    },
                    success: function (data) {
                        $("#designation").html(data);
                    }
                });
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
}
?>