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
<div class="table">
    <div class="table_header">
        <h2>Quotations</h2>
    </div>
    <div class="table_section">
        <table id="quotationData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Quotation No.</th>
                <th>Customer</th>
                <th>Date</th>
                <th>No of Items</th>
                <th>Total Amount</th>
                <th>Actions</th>
            </thead>
            <tbody>
                <?php
                require_once '../../backend/connection/conn.php';
                $db = new DBConnection();
                $conn = $db->conn;

                $sql = "SELECT * FROM quotation_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $quotation_no = $row['quotation_id'];
                        $date_created = $row['date_created'];
                        $customer_name = $row['customer_name'];
                        $tax_amount = $row['tax_amount'];
                        $discount_amount = $row['discount'];

                        // second sql query for get the item details from the quotation_item_tbl
                        $sql2 = "SELECT * FROM quotation_item_tbl WHERE quotation_id=$quotation_no";
                        $result2 = $conn->query($sql2);
                        $item_count = $result2->num_rows;
                        $total_amount = 0;
                        while ($row2 = $result2->fetch_assoc()) {
                            $quotation_item_quantity = $row2['quotation_item_quantity'];
                            $quotation_item_price = $row2['quotation_item_price'];
                            $total_amount += $quotation_item_quantity * $quotation_item_price;
                        }
                        // end of second sql query. time to calculate the final total amount of the quotation
                        $final_total_amount = $total_amount + $tax_amount - $discount_amount;
                        // end of final total amount calculation.
                        ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $quotation_no ?></td>
                            <td><?= $customer_name ?></td>
                            <td><?= $date_created ?></td>
                            <td><?= $item_count ?></td>
                            <td><?= $_SESSION['currency_code'] . ' ' . $final_total_amount ?></td>
                            <td>
                                <div class="text-center">
                                    <button class="btn btn-outline-primary fa-solid fa-receipt"
                                        onclick="window.location.href='./Company.php?page=quotation-preview&id=<?= $quotation_no ?>'"
                                        title="Show Quotation"></button>
                                    <button class="btn btn-outline-warning fa-solid fa-pen-to-square"></button>
                                    <button class="btn btn-outline-danger fa-solid fa-print" title="Print the Quotation"
                                            onclick="window.location.href='../../backend/data/generate-quotation.php?id=<?= $quotation_no ?>'"></button>
                                    <?php
                                    if ($_SESSION['role'] == 'Comapny Admin') {
                                        ?>
                                        
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
        $(document).prop('title', 'All Quotations | Ginum');
        // navbar
        $(" #dashboard").removeClass("active"); $("#quotations").addClass("active"); $('#quotationData').DataTable();
    }) </script>