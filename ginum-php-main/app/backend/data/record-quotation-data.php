<?php
session_start();
require_once '../connection/conn.php';
require_once '../../vendor/autoload.php';

$db = new DBConnection();
$conn = $db->conn;

$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $quotation_number = $_POST['quotation_number'];
        $date = $_POST['date'];
        $customer_name = $_POST['customer_name'];
        $customer_phone = $_POST['customer_phone'];
        $warranty_period = $_POST['warranty_period'];
        $delivery_date = $_POST['delivery_date'];
        $expiration_date = $_POST['expiration_date'];
        $tax_root = isset($_POST['tax']) ? (float) $_POST['tax'] : 0;
        $discount_root = isset($_POST['discount']) ? (float) $_POST['discount'] : 0;
        $total_amount = isset($_POST['total_amount']) ? (float) $_POST['total_amount'] : 0;

        $discount = ($discount_root / 100) * $total_amount;
        $subtotalAfterDiscount = $total_amount - $discount;
        $tax = ($tax_root / 100) * $subtotalAfterDiscount;
        $total_amount_after_tax = $subtotalAfterDiscount + $tax;

        $item_names = $_POST['item_names'];
        $item_features = $_POST['item_features'];
        $item_quantities = $_POST['item_quantities'];
        $item_prices = $_POST['item_prices'];
        $item_detailed_descriptions = $_POST['item_detailed_descriptions'];

        if (filter_var($customer_name, FILTER_SANITIZE_STRING) != $customer_name) {
            throw new Exception("Invalid characters in the customer name field");
        }

        for ($i = 0; $i < count($item_names); $i++) {
            if (filter_var($item_names[$i], FILTER_SANITIZE_STRING) != $item_names[$i]) {
                throw new Exception("Invalid characters in the item name field");
            }
            if (filter_var($item_features[$i], FILTER_SANITIZE_STRING) != $item_features[$i]) {
                throw new Exception("Invalid characters in the item feature field");
            }
            if (!is_numeric($item_quantities[$i]) || $item_quantities[$i] <= 0) {
                throw new Exception("Invalid quantity or quantity is not a number");
            }
            if (!is_numeric($item_prices[$i]) || $item_prices[$i] <= 0) {
                throw new Exception("Invalid price or price is not a number");
            }
            if (filter_var($item_detailed_descriptions[$i], FILTER_SANITIZE_STRING) != $item_detailed_descriptions[$i]) {
                throw new Exception("Invalid characters in the item detailed description field");
            }
        }

        $conn->begin_transaction();

        // Insert quotation details
        $query = "INSERT INTO quotation_tbl (date_created, customer_name, phone_no, warranty_period, delivery_date, expiration_date, tax_amount, discount, company_id, status, tax_rate, discount_rate) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($query);
        $status = 1;
        $stmt->bind_param("ssssssddiidd", $date, $customer_name, $customer_phone, $warranty_period, $delivery_date, $expiration_date, $tax, $discount, $_SESSION['company_id'], $status, $tax_root, $discount_root);
        $stmt->execute();
        $last_id = $conn->insert_id;

        // Insert quotation items
        $query = "INSERT INTO quotation_item_tbl (quotation_id, quotation_item_name, quotation_item_features, quotation_item_quantity, quotation_item_price, quotation_item_description) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($query);

        for ($i = 0; $i < count($item_names); $i++) {
            $stmt->bind_param("issids", $last_id, $item_names[$i], $item_features[$i], $item_quantities[$i], $item_prices[$i], $item_detailed_descriptions[$i]);
            $stmt->execute();
        }

        

        // fetch the company information from the database
        $company_id = $_SESSION['company_id'];
        $sql = "SELECT * FROM company_tbl WHERE company_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $company_row = $result->fetch_assoc();

        // fetch the bank account details of the company
        $sql = "SELECT * FROM bank_details_tbl WHERE company_id=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bank_account_row = $result->fetch_assoc();

        // if (!$bank_account_row) {
        //     throw new Exception("Bank details are not set up. Please add bank details before creating a quotation.");
        // }

        // Commit transaction only if all validations are successful
        $conn->commit();
        $stmt->close();    

        $pdf = new TCPDF();
        $pdf->AddPage();

        $html = '<h1>Quotation</h1>';
        $html .= '<h2>' . htmlspecialchars($company_row['company_name']) . '</h2>';
        $html .= '<p>' . htmlspecialchars($company_row['company_registered_address']) . '</p>';
        $html .= '<p>Email: ' . htmlspecialchars($company_row['email']) . '</p>';
        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr><td>Date: ' . htmlspecialchars($date) . '</td><td>Quotation No: ' . htmlspecialchars($quotation_number) . '</td></tr>';
        $html .= '<tr><td>Customer Name: ' . htmlspecialchars($customer_name) . '</td><td>Customer Contact No: ' . htmlspecialchars($customer_phone) . '</td></tr>';
        $html .= '<tr><td>Expiration Date: ' . htmlspecialchars($expiration_date) . '</td><td>Delivery Date: ' . htmlspecialchars($delivery_date) . '</td></tr>';
        $html .= '<tr><td>Warranty Period: ' . htmlspecialchars($warranty_period) . '</td></tr>';
        $html .= '</table>';

        $html .= '<table border="1" cellpadding="5">';
        $html .= '<thead><tr><th>Description</th><th>Features</th><th>QTY</th><th>Unit Price (' . htmlspecialchars($_SESSION['currency_code']) . ')</th><th>Total (' . htmlspecialchars($_SESSION['currency_code']) . ')</th></tr></thead>';
        $html .= '<tbody>';

        for ($i = 0; $i < count($item_names); $i++) {
            $total_price = $item_quantities[$i] * $item_prices[$i];
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item_names[$i]) . '</td>';
            $html .= '<td>' . htmlspecialchars($item_features[$i]) . '</td>';
            $html .= '<td>' . htmlspecialchars($item_quantities[$i]) . '</td>';
            $html .= '<td>' . htmlspecialchars($item_prices[$i]) . '</td>';
            $html .= '<td>' . htmlspecialchars($total_price) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $html .= '<p>Sub Total: ' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($total_amount) . '</p>';
        $html .= '<p>Discount ' . htmlspecialchars($discount_root) . '% : ' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($discount) . '</p>';
        $html .= '<p>VAT ' . htmlspecialchars($tax_root) . '%: ' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($tax) . '</p>';
        $html .= '<p>Total: ' . htmlspecialchars($_SESSION['currency_code']) . ' ' . htmlspecialchars($total_amount_after_tax) . '</p>';

        $html .= '<p>Bank Name: <b>' . htmlspecialchars($bank_account_row['bank_name']) . '</b></p>';
        $html .= '<p>Branch: <b>' . htmlspecialchars($bank_account_row['branch']) . '</b></p>';
        $html .= '<p>Account No: <b>' . htmlspecialchars($bank_account_row['account_number']) . '</b></p>';
        $html .= '<p>Account Name: <b>' . htmlspecialchars($bank_account_row['account_name']) . '</b></p>';
        $html .= '<p>Cheques should be crossed and made payable to Sanota Pvt Limited</p>';
        $html .= '<p>We are looking forward to your valued purchase order.</p>';
        $html .= '<p>Please feel free to contact us for any further clarifications.</p>';
        $html .= '<p>By signing and accepting below you are acknowledging that you have read and agree to the specific product features in this document.</p>';
        $html .= '<p>Date: ' . htmlspecialchars($date) .' </p>';
        $html .= '<p>Signed by: ______________</p>';

        $pdf->writeHTML($html);
        $pdf->Output("quotation-" . htmlspecialchars($customer_name) . "-" . htmlspecialchars($date) . ".pdf", "D");

        $response['success'] = true;

        $sm = "Quotation saved successfully";
        header("Location: ../../pages/Company/Company.php?page=show-quotations&success=$sm");
        exit();
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    $conn->rollback();
    $em = "Error: " . $e->getMessage();
    header("Location: ../../pages/Company/Company.php?page=show-quotations&error=$em");
    exit();
} finally {
    $conn->close(); 
}
?>