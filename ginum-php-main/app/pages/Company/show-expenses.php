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
        <h2>Expenses</h2>
    </div>
    <div class="table_section">
        <table id="expenseData" class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Expense Account</th>
                    <th>Paid Through</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableData">
                <?php
                if (isset($_SESSION['company_id'])) {
                    $company_id = $_SESSION['company_id'];

                    $sql1 = "SELECT 
    et.expense_id, 
    et.date_added, 
    et.amount, 
    et.reference, 
    sa1.sub_account_name AS expense_account, 
    COALESCE(sa2.sub_account_name, 'N/A') AS paid_through
FROM 
    expense_tbl et 
INNER JOIN 
    sub_account_tbl sa1 ON et.sub_account_id = sa1.sub_account_id
INNER JOIN 
    transaction_log_tbl tlt ON et.company_id = tlt.company_id 
    AND (tlt.debit_account = et.sub_account_id OR tlt.credit_account = et.sub_account_id)
    AND tlt.transaction_type = 'Expense Addition'
    AND tlt.debit_account IS NOT NULL
    AND tlt.credit_account IS NOT NULL
INNER JOIN 
    sub_account_tbl sa2 ON tlt.debit_account = sa2.sub_account_id 
WHERE 
    et.company_id = ? 
    AND et.status = 1
GROUP BY 
    et.expense_id, et.date_added, et.amount, et.reference, sa1.sub_account_name, sa2.sub_account_name";

                    // Prepare and execute the query
                    $stmt = $conn->prepare($sql1);
                    $stmt->bind_param('i', $company_id); // Bind the dynamic company_id
                    $stmt->execute();
                    $result1 = $stmt->get_result();

                    if ($result1->num_rows > 0) {
                        $i = 1;
                        while ($row1 = $result1->fetch_assoc()) {
                            $date = $row1['date_added'];
                            $expense_account = $row1['expense_account'];
                            $paid_through = $row1['paid_through'];
                            $amount = $row1['amount'];
                            $reference = $row1['reference'];
                            $expense_id = $row1['expense_id'];
                ?>

                            <tr id="expense-row-<?= $expense_id ?>">
                                <th><?= $i ?></th>
                                <td><?= $date ?></td>
                                <td><?= $expense_account ?></td>
                                <td><?= $paid_through ?></td>
                                <td><?= $reference ?></td>
                                <td><?= $amount ?></td>
                                <td>
                                    <div class="text-center">
                                        <?php if ($_SESSION['role'] == 'Company Admin') { ?>
                                            <button class="btn btn-outline-danger fa-solid fa-trash"
                                                onclick="confirmDelete(<?php echo $expense_id; ?>)"
                                                title="Delete Expense"></button>
                                        <?php
                                        } else { ?>
                                            <button class="btn btn-outline-danger fa-solid fa-trash"
                                                onclick="unauthorizedAction()"
                                                title="Delete Expense"></button>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                <?php
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No expense records found.</td></tr>";
                    }
                    $stmt->close();
                } else {
                    echo "Error: Company ID not found in the session.";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $(document).prop('title', 'Show Expenses | Ginum');

            // Initialize DataTable
            const table = $("#expenseData").DataTable({
                paging: true,
                searching: true,
                info: true,
                responsive: true,
                // Handle empty table case gracefully
                language: {
                    emptyTable: "No expense records available.",
                    zeroRecords: "No matching records found." // Message for search with no results
                },
                columnDefs: [{
                    targets: '_all', // Apply to all columns
                    defaultContent: '' // Prevent warnings for undefined/null values
                }]
            });

            // navbar
            $("#dashboard").removeClass("active");
            $("#purchase").addClass("active");

        });


        // Function to confirm and delete an expense
        function confirmDelete(id) {
            Swal.fire({
                icon: 'question',
                title: "Do you want to delete this expense?",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "../../backend/data/delete-expense-data.php",
                        type: "POST",
                        data: {
                            id: id
                        },
                        success: function(response) {
                            const res = JSON.parse(response);
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: res.message
                                });
                                // Remove the row dynamically without page refresh
                                $(`#expense-row-${id}`).remove();
                                // $(`#expenseData`).DataTable().draw(); // Redraw the DataTable

                                // $('#expenseData').DataTable().draw();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: res.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: "An unexpected error occurred."
                            });
                        }
                    });
                }
            });
        }
    </script>

</div>