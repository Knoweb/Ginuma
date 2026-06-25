<div class="container py-4">
    <h2>Define New User</h2>
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
        <form action="../../backend/data/add-user-data.php" method="post">
            <div class="mb-3">
                <label for="employee">Select Employee</label>
                <select name="employee" id="employee" class="form-select employee_id">
                    <option value="">-- Select an Employee --</option>
                    <?php
                    $sql = "SELECT * FROM employee_tbl WHERE status=1 AND company_id=?";
                    try {
                        if (!$stmt = $conn->prepare($sql)) {
                            throw new Exception("Unable to prepare the statement");
                        }
                        $stmt->bind_param("i", $_SESSION['company_id']);
                        if (!$stmt->execute()) {
                            throw new Exception("unable to execute the query!");
                        }
                        $result = $stmt->get_result();
                        $stmt->close();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $employee_id = $row['employee_id'];
                                $employee_name = $row['first_name'] . " " . $row["last_name"];
                                ?>
                                <option value="<?= $employee_id ?>"><?= $employee_name ?></option>
                                <?php
                            }
                        }
                    } catch (Exception $e) {
                        $em = "Error: " . $e->getMessage() . "<br/>In line: " . $e->getLine();
                        ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: "<?= $em ?>"
                            })
                        </script>
                        <?php
                    }
                    ?>
                </select>
                <small>
                    <a href="./Company.php?page=add-employee" id="addEmployeeLink">
                        <i class="fa fa-plus-circle"></i> Add New Employee
                    </a>
                </small>
                <hr>
                <div class="row" id="details_box">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" name="designation" id="designation" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" name="department" id="department" class="form-control department"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mobile_no" class="form-label">Mobile No</label>
                            <input type="text" name="mobile_no" id="mobile_no" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_joined" class="form-label">Date Joined</label>
                            <input type="text" name="date_joined" id="date_joined" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" readonly>
                    </div>
                    <input type="hidden" name="employee_id" id="employee_id">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="far fa-eye eye-icon" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="submit" value="Save" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // set title of the page
        $(document).prop('title', 'Add User');

        // highlight the current navbar tab
        $('#dashboard').removeClass('active');
        $('#advanced').addClass('active');

        // toggle the password field visible or not
        $('#togglePassword').on('click', function () {
            var $password = $('#password');
            var type = $password.attr('type') === 'password' ? 'text' : 'password';
            $password.attr('type', type);

            var $icon = $(this).find('i');
            if (type === 'password') {
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        $(".employee_id").change(function () {
            const employeeId = $(this).val();
            $.ajax({
                url: '../../backend/data/get-employee-data.php',
                method: 'POST',
                data: {
                    employee_id: employeeId,
                },
                success: function (response) {
                    if (response.length > 0) {
                        let details = JSON.parse(response);
                        // set the employee details into the form
                        $("#designation").val(details['designation_name']);
                        $(".department").val(details['department_name']);
                        $("#date_joined").val(details['date_joined']);
                        $("#mobile_no").val(details['mobile_no']);
                        $("#email").val(details['email']);
                        $("#employee_id").val(employeeId);
                    }
                },
                error: function (err) {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oh noo.',
                        text: "Faild to fetch the details!"
                    });
                },
            });
        });

    });
</script>