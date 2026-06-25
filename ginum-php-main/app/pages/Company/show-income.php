<!-- Font Awesome Icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<?php if (isset($_GET['success'])) { ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Done',
            text: "<?php echo $_GET['success']; ?>"
        });
    </script>
<?php } ?>
<?php if (isset($_GET['error'])) { ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "<?php echo $_GET['error']; ?>"
        });
    </script>
<?php } ?>
<?php if (isset($_GET['warning'])) { ?>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Careful!',
            text: "<?php echo $_GET['warning']; ?>"
        });
    </script>
<?php } ?>

<div>
    <div class="table_header">
        <h2>Income</h2>
    </div>
    <div class="table_section">
        <table id="incomeData" class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Income Account</th>
                    <th>Reference</th>
                    <th>Amount (<?= $_SESSION['currency_code'] ?>)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableData">
                <?php
                // Enable error display
                ini_set('display_errors', '1');
                ini_set('error_reporting', E_ALL);
                $sql1 = "SELECT it.income_id, 
                        it.date_added, 
                        sat.sub_account_name AS expense_account, 
                        it.amount, 
                        it.reference, 
                        NULL AS item_id,
                        NULL AS quantity,
                        NULL AS tax_amount,
                        NULL AS discount_amount,
                        (SELECT sub_account_name 
                        FROM sub_account_tbl sat2 
                        INNER JOIN transaction_log_tbl tlt 
                        ON sat2.sub_account_id = tlt.sub_account_id 
                        WHERE tlt.company_id = it.company_id 
                        AND tlt.transaction_type = 'Income Addition' 
                        AND tlt.amount = it.amount 
                        LIMIT 1) AS paid_through
            FROM income_tbl it
            INNER JOIN sub_account_tbl sat 
            ON it.sub_account_id = sat.sub_account_id
            WHERE it.company_id = ? 
            AND it.status = 1

            UNION ALL

            SELECT NULL AS income_id, 
                sr.date_made AS date_added, 
                'Sales' AS expense_account, 
                sr.sold_price AS amount, 
                '' AS reference, 
                sr.item_id,
                sr.qty AS quantity,
                sr.tax AS tax_amount,
                sr.discount AS discount_amount,
                NULL AS paid_through
            FROM sales_receipt_tbl sr
            WHERE sr.company_id = ? 
            AND sr.status = 1
            GROUP BY sr.invoice_no, sr.item_id, sr.date_made, sr.sold_price, sr.qty, sr.tax, sr.discount";


            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param('ii', $_SESSION['company_id'], $_SESSION['company_id']);
            $stmt1->execute();
            $result1 = $stmt1->get_result();

                if ($result1->num_rows > 0) {
                    $i = 1;
                    while ($row1 = $result1->fetch_assoc()) {
                        $income_id = $row1['income_id'];
                        $date = $row1['date_added'];
                        $expense_account = $row1['expense_account'];
                        $reference = $row1['reference'] ?? '';
                        $amount = $row1['amount']; // Default amount
                        
                ?>
                        <tr id="income-row-<?php echo $income_id; ?>">
                            <th><?php echo $i; ?></th>
                            <td><?php echo $date; ?></td>
                            <td><?php echo $expense_account; ?></td>
                            <td><?php echo $reference; ?></td>
                            <td><?php echo $amount; ?></td>
                            <td>
                                <div class="text-center">
                                    <!-- <i class="fa-solid fa-receipt"></i> -->
                                    <?php
                                    if ($_SESSION['role'] == 'Company Admin') {
                                    ?>
                                    <button class="btn btn-outline-danger fa-solid fa-trash"
                                        onclick="confirmDelete(<?php echo $income_id; ?>)" title="Delete Income record"></button>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                <?php
                        $i++;
                    }
                } else {
                    // Output a message row when no records are found
                    echo "<tr><td colspan='6' class='text-center'>No income records found.</td></tr>";
                }

                ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $(document).prop('title', 'Show Income | Ginum');
            // navbar
            $("#dashboard").removeClass("active");
            $("#sales").addClass("active");
            $("#incomeData").DataTable({
                paging: true,
                searching: true,
                info: true,
                responsive: true,
                // Handle empty table case gracefully
                language: {
                    emptyTable: "No income records available.",
                    zeroRecords: "No matching records found." // Message for search with no results
                },
                columnDefs: [
                    {
                        targets: '_all', // Apply to all columns
                        defaultContent: '' // Prevent warnings for undefined/null values
                    }
                ]
            });
        });

        // function to confirm if the admin wants to delete the selected income
        function confirmDelete(id) {
            Swal.fire({
                icon: 'question',
                title: "Do you want to delete the Income?",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "../../backend/data/delete-income-data.php",
                        type: "POST",
                        data: { id: id },
                        success: function(response) {
                            const res = JSON.parse(response);
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: res.message
                                });
                                $(`#income-row-${id}`).remove();
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