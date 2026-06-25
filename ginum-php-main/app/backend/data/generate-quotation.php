<?php
session_start();
if (isset($_GET['id'])) {
    require_once '../../vendor/autoload.php';
    require_once '../connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;

    try {
        $quotation_no = $_GET['id'];
        $company_id = $_SESSION['company_id'];

        // Fetch company information
        $sql = "SELECT * FROM company_tbl WHERE company_id=$company_id";
        $result = $conn->query($sql);
        $company_row = $result->fetch_assoc();

        // Fetch quotation information
        $sql = "SELECT * FROM quotation_tbl WHERE quotation_id='$quotation_no'";
        $result = $conn->query($sql);
        $quotation_row = $result->fetch_assoc();
        $quotation_number = explode("-", $quotation_row['date_created'])[0] . '-' . explode("-", $quotation_row['date_created'])[1] . '_' . $quotation_no;

        // Fetch quotation items
        $sql = "SELECT * FROM quotation_item_tbl WHERE quotation_id='$quotation_no'";
        $result = $conn->query($sql);
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        // Fetch bank account details
        $sql = "SELECT * FROM bank_details_tbl WHERE company_id=$company_id LIMIT 1";
        $result = $conn->query($sql);
        $bank_account_row = $result->fetch_assoc();

        $sub_total = 0;

        $pdf = new TCPDF();
        $pdf->AddPage();

        // Styling and layout
        $html = '<style>
            h1, h2, h3 {
                color:rgb(46, 164, 233);
                text-align: center;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 18px;
            }
            th {
                background-color:rgb(175, 172, 172);
                color: #333;
                text-align: center;
            }
            .summary {
                font-size: 14px;
               
            }
            .footer {
               
                text-align: center;
                color: #555;
            }
        </style>';

        // Header
        $html .= '<h1>Quotation</h1>';
        $html .= '<h2>' . $company_row['company_name'] . '</h2>';
        $html .= '<p>' . $company_row['company_registered_address'] . '</p>';
        $html .= '<p>Email: ' . $company_row['email'] . '</p>';

        // Quotation Details
        $html .= '<table>
            <tr><td><b>Date:</b> ' . $quotation_row['date_created'] . '</td><td><b>Quotation No:</b> ' . $quotation_number . '</td></tr>
            <tr><td><b>Customer Name:</b> ' . $quotation_row['customer_name'] . '</td><td><b>Contact No:</b> ' . $quotation_row['phone_no'] . '</td></tr>
            <tr><td><b>Expiration Date:</b> ' . $quotation_row['expiration_date'] . '</td><td><b>Delivery Date:</b> ' . $quotation_row['delivery_date'] . '</td></tr>
            <tr><td colspan="2"><b>Warranty Period:</b> ' . $quotation_row['warranty_period'] . '</td></tr>
        </table>';

        // Items Table
        $html .= '<h3>Quotation Items</h3>';
        $html .= '<table>
            <thead>
                <tr><th>Description</th><th>QTY</th><th>Unit Price (' . $_SESSION['currency_code'] . ')</th><th>Total (' . $_SESSION['currency_code'] . ')</th></tr>
            </thead>
            <tbody>';

        foreach ($items as $item) {
            $total = (float)$item['quotation_item_quantity'] * (float)$item['quotation_item_price'];
            $sub_total += $total;
            $html .= '<tr>
                <td>' . $item['quotation_item_description'] . '</td>
                <td>' . $item['quotation_item_quantity'] . '</td>
                <td>' . $item['quotation_item_price'] . '</td>
                <td>' . $total . '</td>
            </tr>';
        }

        $html .= '</tbody>
        </table>';

        // Summary Section
        $html .= '<div class="summary">
            <p><b>Sub Total:</b> ' . $_SESSION['currency_code'] . ' ' . $sub_total . '</p>
            <p><b>Discount (' . $quotation_row['discount_rate'] . '%):</b> ' . $_SESSION['currency_code'] . ' ' . $quotation_row['discount'] . '</p>
            <p><b>VAT (' . $quotation_row['tax_rate'] . '%):</b> ' . $_SESSION['currency_code'] . ' ' . $quotation_row['tax_amount'] . '</p>
            <p><b>Total:</b> ' . $_SESSION['currency_code'] . ' ' . ($sub_total + $quotation_row['tax_amount'] - $quotation_row['discount']) . '</p>
        </div>';

        // Bank Details
        $html .= '<div class="footer">
            <p><b>Bank Name:</b> ' . $bank_account_row['bank_name'] . '</p>
            <p><b>Branch:</b> ' . $bank_account_row['branch'] . '</p>
            <p><b>Account No:</b> ' . $bank_account_row['account_number'] . '</p>
            <p><b>Account Name:</b> ' . $bank_account_row['account_name'] . '</p>
            <p>Cheques should be crossed and made payable to ' . $bank_account_row['account_name'] . '</p>
        </div>';

        $html .= '<div class="footer">
            <p>We are looking forward to your valued purchase order.</p>
            <p>Please contact us for any further clarifications.</p>
            <p>By signing below, you acknowledge agreement with this quotation.</p>
            <p>Date: ' . $quotation_row['date_created'] . ' Signed by: ______________</p>
        </div>';

        $pdf->writeHTML($html);
        $pdf->Output("quotation-$quotation_number.pdf", "D");

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>
