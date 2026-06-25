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
        <h2>Employees</h2>
    </div>
    <div class="table_section">
        <table id="employeeData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Name</th>
                <th>email</th>
                <th>Designation</th>
                <th>Salary</th>
                <th>Actions</th>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT et.*, dst.designation_name, dpt.department_name, pt.payroll_id, pt.year, pt.month, pt.basic_salary
                        FROM employee_tbl et 
                        LEFT JOIN designation_tbl dst ON et.designation_id = dst.designation_id 
                        LEFT JOIN department_tbl dpt ON dst.department_id = dpt.department_id 
                        LEFT JOIN payroll_tbl pt ON et.employee_id = pt.employee_id 
                        WHERE et.status=1 AND et.company_id='" . $_SESSION['company_id'] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['employee_id'];
                        $year = $row['year'];
                        $month = $row['month'];
                        $payroll_id = $row['payroll_id'];
                        ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['first_name'] . " " . $row['last_name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['designation_name'] ?></td>
                            <td><?= $row['basic_salary'] ?></td>
                            <td>
                                <div class="text-center">
                                    <a class="btn btn-outline-warning fa-solid fa-dollar-sign"
                                        href='./Company.php?page=create-payroll&id=<?= $id ?>' title="Create payroll"></a>
                                    <?php
                                    if ($month == null || $year == null || $payroll_id == null) {
                                        ?>
                                        <a class="disabled btn btn-outline-primary fa-solid fa-money-check-dollar"
                                            href="./Company.php?page=payslip&year=<?= $year ?>&month=<?= $year ?>&id=<?= $id ?>"></a>
                                        <?php
                                    } else {
                                        ?>
                                        <a class="btn btn-outline-primary fa-solid fa-money-check-dollar"
                                            href="./Company.php?page=payslip&year=<?= $year ?>&month=<?= $month ?>&id=<?= $id ?>&payroll_id=<?= $payroll_id ?>"></a>
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
        $(document).prop('title', 'Payrolls for Employees | Ginum');
        // navbar
        $("#dashboard").removeClass("active");
        $("#payrolls").addClass("active");
        $('#employeeData').DataTable();

    })
    // function to confirm if the admin want to delete the selected user
    function confirm(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the supplier?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Save",
            denyButtonText: `Don't save`
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../../backend/data/delete-supplier-data.php?id=" + id + "";
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
</script>