<!-- Font Awsome Icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<div>
    <div class="table_header">
        <h2>General Journal</h2>
    </div>
    <?php if (isset($_GET['success'])) { ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Done',
                text: "<?php echo htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8'); ?>"
            });
        </script>
    <?php } ?>
    <?php if (isset($_GET['error'])) { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "<?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>"
            });
        </script>
    <?php } ?>
    <?php if (isset($_GET['warning'])) { ?>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Careful!',
                text: "<?php echo htmlspecialchars($_GET['warning'], ENT_QUOTES, 'UTF-8'); ?>"
            });
        </script>
    <?php } ?>

    <div class="table_section">
        <table id="transactionData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Transaction Date, Time</th>
                <th>Transaction Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Actions</th>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT * FROM transaction_log_tbl WHERE company_id='" . $_SESSION['company_id'] . "' ORDER BY transaction_date DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['transaction_id'];
                ?>
                        <tr>
                            <th><?= $i ?></th>
                            <td><?= $row['transaction_date'] ?></td>
                            <td><?= $row['transaction_type'] ?></td>
                            <td><?= $row['description'] ?></td>
                            <td><?= $_SESSION['currency_code'] . $row['amount'] ?></td>
                            <td>
                                <?php
                                $sql2 = "SELECT * FROM edit_requests WHERE status='pending' AND table_name='transaction_log_tbl' AND record_id=? AND company_id=?";
                                $stmt2 = $conn->prepare($sql2);
                                $stmt2->bind_param("ii", $id, $_SESSION['company_id']);
                                $stmt2->execute();
                                $result2 = $stmt2->get_result();
                                $stmt2->close();
                                if ($result2->num_rows > 0):
                                ?>
                                    <button class="btn btn-warning" type="button" title="Pending for approval" disabled>
                                        <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                                    </button>
                                <?php else: ?>
                                    <!-- Button to navigate to the edit customer page -->
                                    <!-- <button class="btn btn-outline-warning fa-solid fa-pen-to-square"
                                        onclick="window.location.href='./Company.php?page=edit-customer&id=<?= htmlspecialchars($id) ?>'">
                                    </button> -->
                                <?php endif; ?>
                                <?php if ($_SESSION['role'] == 'Company Admin'): ?>
                                    <!-- button to trigger a confirm dialog before deletion -->
                                    <button class="btn btn-outline-danger fa-solid fa-trash"
                                        onclick="return confirmDelete(<?= htmlspecialchars($id) ?>)">
                                    </button>
                                <?php endif; ?>
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
    $(document).prop('title', 'Show Transactions | Ginum');

    $(document).ready(function() {
        // navbar
        $("#dashboard").removeClass("active");
        $("#transactions").addClass("active");

        $('#transactionData').DataTable();
    });

    // // function to confirm if the admin want to delete the selected user
    function confirmDelete(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the transaction?",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // make an ajax request to delete the selected transaction
                $.ajax({
                    method: 'POST',
                    url: '../../backend/data/delete-transaction-data.php',
                    data: {
                        transaction_id: id,
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: "success",
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    // Refresh the table or page after success
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: data.message,
                                    showConfirmButton: true,
                                });
                            }
                        } catch (e) {
                            console.error('Invalid JSON response:', response);
                            Swal.fire({
                                icon: "error",
                                title: "Unexpected error occurred",
                                text: "Unable to parse server response.",
                                showConfirmButton: true,
                            });
                        }
                    },

                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        Swal.fire({
                            icon: "error",
                            title: "Failed to delete transaction",
                            showConfirmButton: true,
                        });
                    }
                })
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
</script>