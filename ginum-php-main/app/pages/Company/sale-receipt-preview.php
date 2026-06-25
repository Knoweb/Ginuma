<?php

if (isset($_GET['invoice_id'])) {
    // Get the company details from the database
    $sql = "SELECT * FROM company_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $company_name = $row['company_name'];
    $company_registered_address = $row['company_registered_address'];
    $email = $row['email'];

    $invoice_no = $_GET['invoice_id'];
    $sql = "SELECT * FROM sales_receipt_tbl srt 
            LEFT JOIN item_tbl itmt ON srt.item_id=itmt.item_id 
            LEFT JOIN project_tbl prjt ON srt.project_id=prjt.project_id 
            WHERE srt.invoice_no=? AND srt.company_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $invoice_no, $_SESSION['company_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_all(MYSQLI_ASSOC);
        $payment_method = $row[0]['payment_method'];
        $status = $payment_method == 'Cash' ? "Paid" : "UnPaid";
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css"
    integrity="sha256-2XFplPlrFClt0bIdPgpz8H7ojnk10H69xRqd9+uTShA=" crossorigin="anonymous" />

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice-title">
                        <h4 class="float-end font-size-15">Invoice: <?= $invoice_no ?><span
                                class="badge bg-success font-size-12 ms-2"><?= $status ?></span></h4>
                        <div class="mb-4">
                            <h2 class="mb-1 text-muted"><?= $_SESSION['company_name'] ?></h2>
                        </div>
                        <div class="text-muted">
                            <p class="mb-1"><?= $company_registered_address ?></p>
                            <p class="mb-1"><i class="uil uil-envelope-alt me-1"></i><?= $email ?></p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="text-muted">
                                <h5 class="font-size-16 mb-3">Billed To: </h5>
                                <h5 class="font-size-15 mb-2"><?= $row[0]['customer_name'] ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted text-sm-end">
                                <div>
                                    <h5 class="font-size-15 mb-1">Invoice No:</h5>
                                    <p><?= $invoice_no ?></p>
                                </div>
                                <div class="mt-4">
                                    <h5 class="font-size-15 mb-1">Invoice Date:</h5>
                                    <p><?= $row[0]['date_made'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="py-2">
                        <h5 class="font-size-15">Order Summary</h5>

                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">No.</th>
                                        <th>Item/Project</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end" style="width: 120px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_tax = 0;
                                    $total_discount = 0;
                                    $sub_total = 0;

                                    foreach ($row as $index => $item) {
                                        $total_price = ($item['price'] ?? $item['sold_price']) * $item['qty'];
                                        ?>
                                        <tr>
                                            <th scope="row"><?= $index + 1 ?></th>
                                            <td>
                                                <div>
                                                    <h5 class="text-truncate font-size-14 mb-1">
                                                        <?= !empty($item['item_name']) ? $item['item_name'] : $item['project_name'] ?>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td><?= $_SESSION['currency_code'] ?>
                                                <?= $item['price'] ?? $item['sold_price'] ?>
                                            </td>
                                            <td><?= $item['qty'] . " " . (!empty($item['unit']) ? $item['unit'] : 'pcs') ?>
                                            </td>
                                            <td class="text-end"><?= $_SESSION['currency_code'] ?>     <?= $total_price ?></td>
                                        </tr>
                                        <?php
                                        $total_tax += $item['tax'];
                                        $total_discount += $item['discount'];
                                        $sub_total += $total_price;
                                    }
                                    ?>
                                    <tr>
                                        <th scope="row" colspan="4" class="text-end">Sub Total</th>
                                        <td class="text-end"><?= $_SESSION['currency_code'] ?> <?= $sub_total ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="4" class="border-0 text-end">Discount :</th>
                                        <td class="border-0 text-end">- <?= $_SESSION['currency_code'] ?>
                                            <?= $total_discount ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="4" class="border-0 text-end">Tax</th>
                                        <td class="border-0 text-end"><?= $_SESSION['currency_code'] ?>
                                            <?= $total_tax ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="4" class="border-0 text-end">Total</th>
                                        <td class="border-0 text-end">
                                            <h4 class="m-0 fw-semibold"><?= $_SESSION['currency_code'] ?>
                                                <?= ($sub_total + $total_tax) - $total_discount ?>
                                            </h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-print-none mt-4">
                            <div class="float-end">
                                <a href="javascript:window.print()" class="btn btn-success me-1" title="Print"><i
                                        class="fa fa-print"></i></a>
                                <a href="../../backend/data/generate-invoice.php?invoice_id=<?= $invoice_no ?>"
                                    class="btn btn-primary w-md" title="Download"><i class="fas fa-download"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).prop('title', 'Sales Receipt | Ginum');

        // Navbar
        $("#dashboard").removeClass("active");
        $("#sales").addClass("active");
    });
</script>