<style>
    .container {
        max-width: 800px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input,
    textarea {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
    }

    button {
        padding: 10px 15px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #218838;
    }

    .item-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }

    .item-row div {
        flex: 1 1 48%;
    }

    .item-row button {
        background-color: #dc3545;
    }

    .item-row button:hover {
        background-color: #c82333;
    }

    .item-row textarea {
        flex: 1 1 100%;
    }
</style>
<?php
require_once '../../backend/connection/conn.php';
require_once '../../backend/data/pre-check-bank-details.php'; // Include the bank check script
$db = new DBConnection();
$conn = $db->conn;

function createQuotationNumber($conn)
{
    // get the latest qutation ID
    $sql = "SELECT quotation_id FROM quotation_tbl WHERE company_id='" . $_SESSION['company_id'] . "' ORDER BY `quotation_id` ASC";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $last_id = $row == null ? 1 : (int) $row['quotation_id'] + 1;
    return date("Y-m") . "_" . $last_id;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quotation</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container">
    <h2>Create Quotation</h2>
    <form id="quotationForm" action="../../backend/data/record-quotation-data.php" method="post">
        <div class="form-group">
            <label for="quotation_number">Quotation Number</label>
            <input type="text" id="quotation_number" name="quotation_number"
                value="<?php echo createQuotationNumber($conn); ?>" readonly required>
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?= date("Y-m-d") ?>" required>
        </div>

        <div class="form-group">
            <label for="customer_name">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>

        <div class="form-group">
            <label for="customer_phone">Customer Phone</label>
            <input type="text" id="customer_phone" name="customer_phone" required>
        </div>

        <div class="form-group">
            <label for="warranty_period">Warranty Period</label>
            <input type="text" id="warranty_period" name="warranty_period">
        </div>

        <div class="form-group">
            <label for="delivery_date">Delivery Date</label>
            <input type="date" id="delivery_date" name="delivery_date" required>
        </div>

        <div class="form-group">
            <label for="expiration_date">Expiration Date</label>
            <input type="date" id="expiration_date" name="expiration_date" required>
        </div>

        <div id="item-container"></div>
        <button type="button" onclick="addItem()">Add Item</button>

        <div class="form-group">
            <label for="total_amount">Total Amount</label>
            <input type="number" id="total_amount" name="total_amount" readonly required>
        </div>

        <div class="form-group">
            <label for="tax">Tax Rate (%)</label>
            <input type="number" id="tax" name="tax" oninput="calculateTotal()" required>
        </div>

        <div class="form-group">
            <label for="discount">Discount (%)</label>
            <input type="number" id="discount" name="discount" required oninput="calculateTotal()">
        </div>

        <button type="submit">Create Quotation</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script>
    $(document).prop('title', 'Create Quotation | Ginum');

    //navbar
    $("#dashboard").removeClass("active");
    $("#quotations").addClass("active");

    function addItem() {
        const itemContainer = document.getElementById('item-container');
        const itemRow = document.createElement('div');
        itemRow.className = 'item-row';
        itemRow.innerHTML = `
            <div><input type="text" name="item_names[]" placeholder="Item Name" required></div>
            <div><input type="text" name="item_features[]" placeholder="Item Features"></div>
            <div><input type="number" name="item_quantities[]" placeholder="Quantity" required oninput="calculateTotal()"></div>
            <div><input type="number" name="item_prices[]" placeholder="Price" required oninput="calculateTotal()"></div>
            <div><textarea name="item_detailed_descriptions[]" placeholder="Detailed Description" rows="2"></textarea></div>
            <button type="button" onclick="removeItem(this)">Remove</button>
        `;
        itemContainer.appendChild(itemRow);
    }

    function removeItem(button) {
        const itemContainer = document.getElementById('item-container');
        itemContainer.removeChild(button.parentNode);
        calculateTotal();
    }

    function calculateTotal() {
        const quantities = document.getElementsByName('item_quantities[]');
        const prices = document.getElementsByName('item_prices[]');
        let total = 0;
        for (let i = 0; i < quantities.length; i++) {
            const quantity = parseFloat(quantities[i].value) || 0;
            const price = parseFloat(prices[i].value) || 0;
            total += quantity * price;
        }
        document.getElementById('total_amount').value = total.toFixed(2);
    }
</script>