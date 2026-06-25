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
        <h2>Sales Receipt Details</h2>
    </div>
    <div class="table_section">
        <table id="salesData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Invoice No.</th>
                <th>Customer</th>
                <th>Payment Mode</th>
                <th>Amount</th>
                <th>Created By</th>
                <?= $_SESSION['role'] == 'Company Admin' ? "<th></th>" : "" ?>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT DISTINCT invoice_no, customer_name, payment_method FROM sales_receipt_tbl WHERE company_id='" . $_SESSION['company_id'] . "' AND status=1";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $amount = 0;
                        $invoice_no = $row['invoice_no'];
                        $customer_name = $row['customer_name'];
                        $payment_mode = $row['payment_method'];

                        $sql2 = "SELECT tax, qty, sold_price, discount FROM sales_receipt_tbl WHERE company_id='" . $_SESSION['company_id'] . "' AND invoice_no='" . $invoice_no . "'";
                        $result2 = $conn->query($sql2);
                        if ($result2->num_rows > 0) {
                            while ($row2 = $result2->fetch_assoc()) {
                                $amount += $row2['qty'] * $row2['sold_price'] - ($row2['discount']) + $row2['tax'];
                            }
                        }

                        echo "<tr>";
                        echo "<td>" . $i++ . "</td>";
                        echo "<td>" . $invoice_no . "</td>";
                        echo "<td>" . $customer_name . "</td>";
                        echo "<td>" . $payment_mode . "</td>";
                        echo "<td>" . $amount . "</td>";
                        echo "<td>" . $_SESSION['company_name'] . "</td>";
                        if ($_SESSION['role'] == 'Company Admin') {
                            ?>
                            <td>
                                <div class="text-center">
                                    <button class="btn btn-outline-primary fa-solid fa-receipt"
                                        onclick="window.location.href='./Company.php?page=sales-receipt-preview&invoice_id=<?= $invoice_no ?>'"></button>
                                </div>
                            </td>
                            <?php
                        }
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).prop('title', 'Show Sales Receipt | Ginum');
        // navbar
        $("#dashboard").removeClass("active");
        $("#sales").addClass("active");

        $('#salesData').DataTable();
    });
    // function to confirm if the admin want to delete the selected user
    function confirm(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the employee?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Save",
            denyButtonText: `Don't save`
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