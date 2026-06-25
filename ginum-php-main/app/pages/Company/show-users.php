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
        <h2>Active Users</h2>
    </div>
    <div class="table_section">
        <table id="userData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Last Login</th>
                <th>Actions</th>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT * FROM sub_logins_tbl slt INNER JOIN employee_tbl et ON (slt.employee_id=et.employee_id) INNER JOIN designation_tbl dsgt ON (dsgt.designation_id=et.designation_id) WHERE slt.company_id=? AND slt.status=1 AND et.status=1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_SESSION['company_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <th><?= $i ?></th>
                            <td><?= $row['first_name'] . " " . $row['last_name'] ?></td>
                            <td><?= $row['designation_name'] ?></td>
                            <td><?= $row['last_login'] ?: "-" ?></td>
                            <td>
                                <div class="text-center">
                                    <button class="btn btn-outline-primary fa-solid fa-user"
                                        onclick="window.location.href='./Company.php?page=user-profile&id=<?= $row['employee_id'] ?>&type=Employee'"></button>
                                    <?php
                                    if ($_SESSION['role'] == 'Company Admin') {
                                        ?>
                                        <button class="btn btn-danger fa-solid fa-trash"
                                            onclick="confirm('<?= $row['employee_id'] ?>')"></button>
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


</section>

<script>
    $(document).prop('title', 'Show Users | Ginum');

    $(document).ready(function () {
        // navbar
        $("#dashboard").removeClass("active");
        $("#advanced").addClass("active");

        $('#userData').DataTable();
    });

    // function to confirm if the admin want to delete the selected user
    function confirm(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the user?",
            showCancelButton: true,
            confirmButtonColor:  '#d33',
            cancelButtonColor:'#3085d6' ,
             confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                window.location.href = "../../backend/data/delete-user-data.php?id=" + id + "";
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
</script>