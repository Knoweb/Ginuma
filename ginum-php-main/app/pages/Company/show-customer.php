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
        <h2>Customer Details</h2>
    </div>
    <div class="table_section">
        <table id="customerData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Name</th>
                <th>Customer Type</th>
                <th>Email</th>
                <th>Date Added</th>
                <th>Actions</th>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT * FROM customer_tbl WHERE status=1 AND company_id='" . $_SESSION['company_id'] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;

                    while ($row = $result->fetch_assoc()) {
                        $id = $row['customer_id'];
                ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['customer_type'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['date_added'] ?></td>
                            <div class="text-center">
                                <td>
                                    <!-- Button to navigate to the user profile -->
                                    <button class="btn btn-outline-primary fa-solid fa-user"
                                        onclick="window.location.href='./Company.php?page=user-profile&id=<?= htmlspecialchars($id) ?>&type=Customer'">
                                    </button>

                                    <?php
                                    // Securely prepare and execute SQL query
                                    $status = "";
                                    $stmt2 = $conn->prepare("SELECT * FROM edit_requests WHERE status='pending' AND table_name='customer_tbl' AND record_id=? AND company_id=?");
                                    $stmt2->bind_param("ii", $id, $_SESSION['company_id']);
                                    $stmt2->execute();
                                    $result2 = $stmt2->get_result();

                                    if ($result2->num_rows > 0) {
                                        // Data already in the edit_requests table
                                    ?>
                                        <button class="btn btn-warning" type="button" title="Pending for approval" disabled>
                                            <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                                        </button>
                                    <?php
                                    } else {
                                    ?>
                                        <!-- Button to navigate to the edit customer page -->
                                        <button class="btn btn-outline-warning fa-solid fa-pen-to-square"
                                            onclick="window.location.href='./Company.php?page=edit-customer&id=<?= htmlspecialchars($id) ?>'">
                                        </button>
                                    <?php
                                    }

                                    if ($_SESSION['role'] == 'Company Admin') {
                                    ?>
                                        <!-- Button to trigger a confirm dialog before deletion -->
                                        <button class="btn btn-outline-danger fa-solid fa-trash"
                                            onclick="return confirmDelete(<?= htmlspecialchars($id) ?>)">
                                        </button>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </div>
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
    $(document).prop('title', 'Show Customers | Ginum');

    $(document).ready(function() {
        // navbar
        $("#dashboard").removeClass("active");
        $("#customer").addClass("active");

        $('#customerData').DataTable();
    });

    // function to confirm if the admin want to delete the selected user
    function confirmDelete(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the customer?",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete '
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../../backend/data/delete-customer-data.php?id=" + id;
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
        return false; // Prevents the default button action
    }
</script>