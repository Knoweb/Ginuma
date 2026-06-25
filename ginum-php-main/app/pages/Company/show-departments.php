<!-- Font Awsome Icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
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
<div>
    <div class="table_header">
        <h2>Department Details</h2>
    </div>
    <div class="table_section">
        <table id="departmentData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Department</th>
                <th>Department Code</th>
                <th>Assigned Employees</th>
                <th>Actions</th>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT d.department_id, d.department_name, d.department_code, COUNT(e.employee_id) AS employee_count FROM department_tbl d LEFT JOIN designation_tbl des ON d.department_id = des.department_id LEFT JOIN employee_tbl e ON des.designation_id = e.designation_id and e.status = 1 WHERE d.company_id ='" . $_SESSION['company_id'] . "' GROUP BY d.department_id, d.department_name";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['department_id'];
                        ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['department_name'] ?></td>
                            <td><?= $row['department_code'] ?></td>
                            <td><?= $row['employee_count'] ?></td>
                            <td>
                                <div class="text-center">
                                    <?php
                                    // check if the customer details is in the edit_requests table.
                                    $sql2 = "SELECT * FROM edit_requests WHERE status='pending' AND table_name='department_tbl' AND record_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
                                    $result2 = $conn->query($sql2);
                                    if ($result2->num_rows > 0) {
                                        // data already in the edit_requests table.
                                        ?>
                                        <button class="btn btn-warning" type="button" disabled title="Pending for approval">
                                            <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                                        </button>
                                        <?php
                                    } else {
                                        ?>
                                        <button class="btn btn-outline-warning fa-solid fa-pen-to-square"
                                            onclick="window.location.href='./Company.php?page=edit-department&id=<?= $id ?>'"
                                            title="Edit Department"></button>
                                        <?php
                                    }
                                    if ($_SESSION['role'] == 'Company Admin') {
                                        ?>
                                        <button class=" btn btn-outline-danger fa-solid fa-trash"
                                            onclick="confirm(<?= $id ?>)"></button>
                                        <?php
                                    }
                                    ?>
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
        $('#departmentData').DataTable();
        $(document).prop('title', 'Show Department | Ginum');
        // navbar
        $("#dashboard").removeClass("active");
        $("#department").addClass("active");
    });
    // function to confirm if the admin want to delete the selected user
    function confirm(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the department?",
            showCancelButton: true,
            confirmButtonColor:  '#d33',
            cancelButtonColor:'#3085d6' ,
             confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                window.location.href = "../../backend/data/delete-department-data.php?id=" + id + "";
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
</script>