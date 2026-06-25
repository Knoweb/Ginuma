<!-- Font Awsome Icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<div>
    <div class="table_header">
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
        <h2>Employee Details</h2>
    </div>
    <div class="table_section">
        <table id="employeeData" class="table table-striped table-responsive">
            <thead>
                <th>No.</th>
                <th>Name</th>
                <th>email</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Date Joined</th>
                <th>Actions</th>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT * FROM employee_tbl et INNER JOIN designation_tbl dst ON (et.designation_id=dst.designation_id) INNER JOIN department_tbl dpt ON (dst.department_id=dpt.department_id) WHERE et.status=1 AND et.company_id='" . $_SESSION['company_id'] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['employee_id'];
                        ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['first_name'] . " " . $row['last_name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['designation_name'] ?></td>
                            <td><?= $row['department_code'] ?></td>
                            <td><?= $row['date_joined'] ?></td>
                            <td>
                                <div class="text-center">
                                    <button class="btn btn-outline-primary fa-solid fa-user"
                                        onclick="window.location.href='./Company.php?page=user-profile&id=<?= $id ?>&type=Employee'"></button>
                                    <?php
                                    // check if the customer details is in the edit_requests table.
                                    $sql2 = "SELECT * FROM edit_requests WHERE status='pending' AND table_name='employee_tbl' AND record_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
                                    $result2 = $conn->query($sql2);
                                    if ($result2->num_rows > 0) {
                                        // data already in the edit_requests table.
                                        ?>
                                        <button class="btn btn-warning" type="button" title="Pending for approval" disabled>
                                            <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                                        </button>
                                        <?php
                                    } else {
                                        ?>
                                        <button class="btn btn-outline-warning fa-solid fa-pen-to-square"
                                            onclick="window.location.href='./Company.php?page=edit-employee&id=<?= $id ?>'"
                                            title="Edit Employee"></button>
                                        <?php
                                    }
                                    if ($_SESSION['role'] == 'Company Admin') {
                                        ?>
                                        <button class="btn btn-outline-danger fa-solid fa-trash" onclick="confirm(<?= $id ?>)"
                                            title="Delete Employee"></button>
                                        <?php
                                    } ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>

    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).prop('title', 'Show Employees | Ginum');
        // navbar
        $("#dashboard").removeClass("active");
        $("#employee").addClass("active");

        $('#employeeData').DataTable();

        $("#searchByName").keyup(function () {
            var value = $(this).val();
            $.ajax({
                url: "../../backend/data/get-employee-data.php",
                method: "POST",
                data: {
                    name: value
                },
                success: function (data) {
                    $("#tableData").html(data);
                }
            });
        })
    });
    // function to confirm if the admin want to delete the selected user
    function confirm(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the employee?",
            showCancelButton: true,
            confirmButtonColor:  '#d33',
            cancelButtonColor:'#3085d6' ,
             confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                window.location.href = "../../backend/data/delete-employee-data.php?id=" + id + "";
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
</script>