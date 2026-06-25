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

    <div class="table_header ">
        <h2>Orders</h2>
    </div>
    <div class="table_section">
        <table id="orderData" class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Order Code</th>
                    <th>Order Name</th>
                    <th>Customer</th>
                    <th>Budget</th>
                    <th>Priority</th>
                    <th>Completed Tasks</th>
                    <th>Work Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT p.*, ct.name, COUNT(t.task_id) AS task_count, COUNT(CASE WHEN t.status = 1 THEN t.task_id END) AS done_task_count FROM project_tbl p INNER JOIN task_tbl t ON p.project_id = t.project_id INNER JOIN company_tbl c ON p.company_id = c.company_id  INNER JOIN customer_tbl ct ON (p.customer_id=ct.customer_id)  WHERE c.company_id = '" . $_SESSION['company_id'] . "' GROUP BY p.project_id, p.project_name";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['project_id'];
                        $project_code = $row['project_code'];
                        $project_name = $row['project_name'];
                        $customer_name = $row['name'];
                        $project_budget = $row['budget'];
                        $project_priority = $row['priority'];
                        $task_done = $row['done_task_count'] . "/ " . $row['task_count'];
                        $work_status = $row['work_status'];
                        ?>
                        <tr>
                            <th><?= $i ?></th>
                            <td><?= $project_code ?></td>
                            <td><?= $project_name ?></td>
                            <td><?= $customer_name ?></td>
                            <td><?= $_SESSION['currency_code'] . " " . $project_budget ?></td>
                            <td><?= $project_priority ?></td>
                            <td><?= $task_done ?></td>
                            <td><?= $work_status ?></td>
                            <td>
                                <div class="text-center">
                                    <button class="btn btn-outline-success fa-solid fa-check" <?= $work_status == 'Completed' ? " disabled" : "" ?>
                                        onclick="window.location.href='../../backend/data/mark-project-completed.php?id=<?= $id ?>'"
                                        title="Mark as completed"></button>
                                    <button class="btn btn-outline-warning fa-solid fa-pen-to-square"
                                        onclick="window.location.href='./Company.php?page=edit-order&id=<?= $id ?>'"
                                        title="Edit Order Details"></button>
                                    <?php
                                    if ($_SESSION['role'] == 'Company Admin') {
                                        ?>
                                        <button class="btn btn-outline-danger fa-solid fa-trash" onclick="confirm(<?= $id ?>)"
                                            title="Delete Order"></button>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>

        $(document).ready(function () {
            $(document).prop('title', 'Show Orders | Ginum');
            // navbar
            $("#dashboard").removeClass("active");
            $("#orders").addClass("active");
            $("#orderData").DataTable();
        });

        // function to confirm if the admin want to delete the selected user
        function confirm(id) {
            Swal.fire({
                icon: 'question',
                title: "Do you want to delete the Order?",
                showCancelButton: true,
            confirmButtonColor:  '#d33',
            cancelButtonColor:'#3085d6' ,
             confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // window.location.href = "../../backend/data/delete-department-data.php?id=" + id + "";
                    Swal.fire("Ok OK", "", "info");
                } else if (result.isDenied) {
                    Swal.fire("Changes are not saved", "", "info");
                }
            });
        }
    </script>

</div>