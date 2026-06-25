<div class="container py-5 h-100">
    <h2>Sales Receipt</h2>
    <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <form action="../../backend/data/record-sales-data.php" method="post">
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
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" id="name" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="<?= date("Y-m-d") ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="text" class="form-label">Invoice No.</label>
                        <input type="text" name="invoice_no" id="text" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-select">
                    <option value="Cash" selected>Cash</option>
                    <option value="Credit">Debit</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="Items" class="form-label">Items</label>
                <div class="table-responsive">
                    <table class="table table-bordered" id="Items">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Item</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Selling Price</th>
                                <th class="text-center">Qty.</th>
                                <th class="text-center">Subtotal</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody">

                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-md btn-primary" id="addBtn" type="button">
                                <i class='bx bx-plus'></i>
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="form-inline">
                                <label for="taxRatio" class="mr-2">Tax Ratio:</label>
                                <input type="text" name="tax" id="taxRatio" class="form-control mr-2"
                                    placeholder="Tax Ratio (%)">
                                <label for="discount" class="mr-2">Discount:</label>
                                <input type="text" name="discount" id="discount" class="form-control mr-2"
                                    placeholder="Discount">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Subtotal (<?= $_SESSION['currency_code'] ?>): <span id="subtotal">0.00</span></h5>
                            <h5>Tax (<?= $_SESSION['currency_code'] ?>): <span id="tax">0.00</span></h5>
                            <h4>Final Total (<?= $_SESSION['currency_code'] ?>): <span id="finalTotal">0.00</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 mt-4">
                <button class="btn btn-md btn-primary" id="generateBtn" type="submit">
                    Generate
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).prop('title', 'Sales Receipt | Ginum');

        // Navbar
        $("#dashboard").removeClass("active");
        $("#sales").addClass("active");

        // For table
        let count = 1; // Initialize row count

        // Adding row on click to Add New Row button
        $('#addBtn').click(function () {
            let dynamicRowHTML = `
        <tr class="rowClass"> 
            <td class="row-index text-center"> 
                ${count}
            </td>
            <td class="text-center"> 
                <select name="item_ids[]" class="form-select itemName">
                    <option value="">-- Choose an Item/ Order --</option>
                    <?php
                    $sql = "
                    SELECT 
                        proj.project_id, 
                        proj.project_code, 
                        proj.project_name, 
                        proj.department_id, 
                        proj.billing_method, 
                        proj.work_status, 
                        proj.description AS project_description, 
                        proj.priority, 
                        proj.start_date, 
                        proj.end_date, 
                        proj.budget, 
                        proj.customer_id,
                        it.item_id, 
                        it.item_name, 
                        invt.qty, 
                        invt.unit_price
                    FROM 
                        project_tbl proj
                    LEFT JOIN 
                        item_tbl it ON proj.company_id = it.company_id
                    LEFT JOIN 
                        inventory_tbl invt ON it.item_id = invt.item_id AND invt.qty > 0
                    WHERE 
                        proj.company_id = '" . $_SESSION['company_id'] . "' 
                        AND proj.work_status = 'Completed'
                    ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                                                                    <option value="<?= $row['item_id'] ?? $row['project_id'] ?>"><?= $row['item_name'] ?? $row['project_name'] ?></option>
                                                                    <?php
                        }
                    }
                    ?>
                </select>
            </td> 
            <td class="text-center"> 
                <input type='text' name='unitPrices[]' class='form-control unitPrice' title='Unit Price' required>
            </td> 
            <td class="text-center"> 
                <input type='text' name='sellingPrices[]' class='form-control sellingPrice' title='Selling Price' required>
            </td> 
            <td class="text-center"> 
                <input type='text' name='qtys[]' class='form-control quantity'  title='Quantity' required>
            </td> 
            <td class="text-center"> 
                <input type='text' name='totalOfEachItems[]' class='form-control totalPrice' title='Total price of the item' readonly required>
            </td> 
            <td class="text-center"> 
                <button class="btn btn-danger remove" type="button"><i class='bx bx-x'></i></button> 
            </td> 
        </tr>`;
            $('#tbody').append(dynamicRowHTML);
            count++;
        });

        // Calculate total price on change of unit price or quantity
        $('#tbody').on('input', '.unitPrice, .quantity', function () {
            let row = $(this).closest('tr');
            let unitPrice = parseFloat(row.find('.unitPrice').val());
            let quantity = parseFloat(row.find('.quantity').val());
            let totalPrice = unitPrice * quantity;
            if (!isNaN(totalPrice)) {
                row.find('.totalPrice').val(totalPrice.toFixed(2));
                calculateTotal();
            } else {
                row.find('.totalPrice').val('');
            }
        });

        // Remove row on click of Remove button
        $('#tbody').on('click', '.remove', function () {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        // Calculate total
        function calculateTotal() {
            let subtotal = 0;
            $('.totalPrice').each(function () {
                let totalPrice = parseFloat($(this).val());
                if (!isNaN(totalPrice)) {
                    subtotal += totalPrice;
                }
            });

            let taxRatio = parseFloat($('#taxRatio').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;
            let tax = (subtotal * taxRatio) / 100;
            discount = (subtotal * discount) / 100;
            let finalTotal = subtotal + tax - discount;

            $('#subtotal').text(subtotal.toFixed(2));
            $('#sellingPrice').text(subtotal.toFixed(2));
            $('#tax').text(tax.toFixed(2));
            $('#finalTotal').text(finalTotal.toFixed(2));
        }

        // Recalculate total on change of unit price, quantity, or selling price
        $('#tbody').on('input', '.unitPrice, .quantity, .sellingPrice', function () {
            let row = $(this).closest('tr');
            let unitPrice = parseFloat(row.find('.unitPrice').val()) || 0;
            let quantity = parseFloat(row.find('.quantity').val()) || 0;
            let sellingPrice = parseFloat(row.find('.sellingPrice').val()) || 0;

            // Calculate total based on selling price if provided
            let totalPrice = sellingPrice > 0 ? sellingPrice * quantity : unitPrice * quantity;

            if (!isNaN(totalPrice)) {
                row.find('.totalPrice').val(totalPrice.toFixed(2));
                calculateTotal();
            } else {
                row.find('.totalPrice').val(''); // Clear total price if unit price or quantity is not valid
            }
        });


        // Event delegation for change of itemName
        $('#tbody').on('change', '.itemName', function () {
            let id = $(this).val();
            let row = $(this).closest('tr'); // Get the current row

            $.ajax({
                url: "../../backend/data/get-item-data.php",
                method: "POST",
                data: {
                    id: id
                },
                success: function (data) {
                    let itemData = JSON.parse(data); // Parse the JSON response
                    row.find('.unitPrice').val(itemData.unitPrice); // Set unit price
                    row.find('.sellingPrice').val(itemData.unitPrice); // Set unit price
                    row.find('.quantity').val(1);
                    row.find('.totalPrice').val(itemData.unitPrice); // Set initial total price as unit price
                    calculateTotal(); // Update the totals
                }
            });
        });

        // Recalculate total on change of tax ratio or discount
        $('#taxRatio, #discount').on('input', function () {
            calculateTotal();
        });
    });
</script>