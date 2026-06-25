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
        <h2>Supplier Details</h2>
    </div>
    <div class="table_section">
        <table id="supplierData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Name</th>
                <th>Supplier Type</th>
                <th>Email</th>
                <th>Item Category</th>
                <th>Date Added</th>
                <th>Actions</th>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT * FROM supplier_tbl st INNER JOIN item_category_tbl ict ON (st.item_category_id=ict.item_category_id) WHERE st.status=1 AND st.company_id='" . $_SESSION['company_id'] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['supplier_id'];
                ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['supplier_name'] ?></td>
                            <td><?= $row['supplier_type'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['item_category_name'] ?></td>
                            <td><?= $row['date_added'] ?></td>
                            <td>
                                <div class="text-center">
                                    <button class="btn btn-outline-primary fa-solid fa-user"
                                        onclick="window.location.href='./Company.php?page=user-profile&id=<?= $id ?>&type=Supplier'"></button>
                                    <?php
                                    // check if the customer details is in the edit_requests table.
                                    $sql2 = "SELECT * FROM edit_requests WHERE status='pending' AND table_name='supplier_tbl' AND record_id='" . $id . "' AND company_id='" . $_SESSION['company_id'] . "'";
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
                                            onclick="window.location.href='./Company.php?page=edit-supplier&id=<?= $id ?>'"></button>
                                    <?php
                                    }
                                    if ($_SESSION['role'] == 'Company Admin') {
                                    ?>
                                        <button class="btn btn-outline-danger fa-solid fa-trash"
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
    $(document).ready(function() {
        $(document).prop('title', 'Show Suppliers | Ginum');
        // navbar
        $("#dashboard").removeClass("active");
        $("#supplier").addClass("active");
        $('#supplierData').DataTable();

    })
    // function to confirm if the admin want to delete the selected user
    function confirm(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the supplier?",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                window.location.href = "../../backend/data/delete-supplier-data.php?id=" + id + "";
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
</script>