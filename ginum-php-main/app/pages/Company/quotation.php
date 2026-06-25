<style>
    .quotation-container {
        max-width: 800px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .header,
    .footer {
        text-align: center;
    }

    .header img {
        width: 100px;
    }

    .details,
    .items,
    .summary,
    .footer-content {
        width: 100%;
        margin-bottom: 20px;
    }

    .details td,
    .items th,
    .items td,
    .summary td,
    .footer-content td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    .items th {
        background-color: #f2f2f2;
    }

    .total {
        font-weight: bold;
    }
</style>

<?php
if (isset($_GET['id'])) {
    // database connection instance
    require_once '../../backend/connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    $quotation_no = $_GET['id'];
    $company_id = $_SESSION['company_id'];

    // fetch the company information from the database
    $sql = "SELECT * FROM company_tbl WHERE company_id=$company_id";
    $result = $conn->query($sql);
    $company_row = $result->fetch_assoc();

    // fetch the quotation information from the database
    $sql = "SELECT * FROM quotation_tbl WHERE quotation_id='$quotation_no'";
    $result = $conn->query($sql);
    $quotation_row = $result->fetch_assoc();
    $exploaded_quotation_no = explode("-", $quotation_row['date_created']);
    $quotation_number = $exploaded_quotation_no[0] . '-' . $exploaded_quotation_no[1] . '_' . $quotation_no;

    // fetch the quotation items from the database
    $sql = "SELECT * FROM quotation_item_tbl WHERE quotation_id='$quotation_no'";
    $result = $conn->query($sql);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    // fetch the bank account details of the company
    $sql = "SELECT * FROM bank_details_tbl WHERE company_id=$company_id LIMIT 1";
    $result = $conn->query($sql);
    $bank_account_row = $result->fetch_assoc();

    $sub_total = 0;

    ?>

    <div class="quotation-container">
        <div class="header">
            <h1>Quotation</h1>
            <p><?= $company_row['company_name'] ?></p>
            <p><?= $company_row['company_registered_address'] ?></p>
            <p>Email:
                <?= $company_row['email'] ?>
                <?php echo $company_row['website_url'] ? " | Web: " . $company_row['website_url'] : "" ?>
            </p>
        </div>

        <table class="details">
            <tr>
                <td>Date: <span><?= $quotation_row['date_created'] ?></span></td>
                <td>Quotation No: <span><?= $quotation_number ?></span></td>
            </tr>
            <tr>
                <td>Contact Person: <span><?= $quotation_row['customer_name'] ?></span></td>
                <td>Contact No: <span><?= $quotation_row['phone_no'] ?></span></td>
            </tr>
            <tr>
                <td>Expiration Date: <span><?= $quotation_row['expiration_date'] ?></span></td>
                <td>Delivery Date: <span><?= $quotation_row['delivery_date'] ?></span></td>
            </tr>
            <tr>
                <td>Warranty Period: <span><?= $quotation_row['warranty_period'] ?></span></td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>QTY</th>
                    <th>Unit Price (<?= $_SESSION['currency_code'] ?>)</th>
                    <th>Total (<?= $_SESSION['currency_code'] ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($items as $item) {
                    $sub_total += (float) $item['quotation_item_quantity'] * (float) $item['quotation_item_price'];
                    echo "<tr>";
                    echo "<th>" . $item['quotation_item_description'] . "</th>";
                    echo "<td>" . $item['quotation_item_quantity'] . "</td>";
                    echo "<td>" . $item['quotation_item_price'] . "</td>";
                    echo "<td>" . (float) $item['quotation_item_quantity'] * (float) $item['quotation_item_price'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td>Sub Total</td>
                <td><?= $_SESSION['currency_code'] . ' ' . $sub_total ?></td>
            </tr>
            <tr>
                <td>TAX (<?= $quotation_row['tax_rate'] ?>%)</td>
                <td><?= $_SESSION['currency_code'] . ' ' . $quotation_row['tax_amount'] ?></td>
            </tr>
            <tr>
                <td>Discount (<?= $quotation_row['discount_rate'] ?>%)</td>
                <td><?= $_SESSION['currency_code'] . ' ' . $quotation_row['discount'] ?></td>
            </tr>
            <tr class="total">
                <td>Total</td>
                <td><?= $_SESSION['currency_code'] . ' ' . $sub_total + $quotation_row['tax_amount'] - $quotation_row['discount'] ?>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>Bank Name: <b><?= $bank_account_row['bank_name'] ?></b></p>
            <p>Branch: <b><?= $bank_account_row['branch'] ?></b></p>
            <p>Account No: <b><?= $bank_account_row['account_number'] ?></b></p>
            <p>Account Name: <b><?= $bank_account_row['account_name'] ?></b></p>
            <p>Cheques should be crossed and made payable to <?= $bank_account_row['account_name'] ?></p>
            <p>We are looking forward to your valued purchase order.</p>
            <p>Please feel free to contact us for any further clarifications.</p>
            <p>By signing and accepting below you are acknowledging that you have read and agree to the specific product
                features in this document.</p>
            <p>Date: ____________ </p>
            <p>Signed by: ____________</p>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $(document).prop('title', 'Quotation Preview | Ginum');

            //navbar
            $("#dashboard").removeClass("active");
            $("#quotations").addClass("active");
        })
    </script>
    <?php
} else {
    echo "No Data Available";
}
?>