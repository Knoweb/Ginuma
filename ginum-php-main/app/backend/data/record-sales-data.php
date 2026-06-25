<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	include_once "../includes/Functions.php";
	require_once '../connection/conn.php';
	$db = new DBConnection();
	$conn = $db->conn;

	// Retrieve POST data
	$company_id = $_SESSION['company_id'];
	$customer_name = $_POST['customer_name'];
	$date = $_POST['date'];
	$invoice_no = $_POST['invoice_no'];
	$notes = $_POST['notes'];
	$payment_method = $_POST['payment_method'];
	$item_ids = $_POST['item_ids'];
	$unitPrices = $_POST['unitPrices'];
	$sellingPrices = $_POST['sellingPrices'];
	$qtys = $_POST['qtys'];
	$tax_rate = floatval($_POST['tax']) ?: 0;
	$discount_rate = floatval($_POST['discount']) ?: 0;

	// Begin transaction
	$conn->begin_transaction();
	try {
		// Account mapping
		$account_map = [
			'Sales' => 4,
			'Accounts Receivable' => 1,
			'Cost of Goods Sold' => 5,
			'Cash' => 1,
			'Inventory' => 1
		];

		// Fetch sub-account IDs
		$sub_account_ids = [];
		foreach ($account_map as $name => $account_id) {
			$stmt = $conn->prepare("SELECT sub_account_id FROM sub_account_tbl WHERE account_id=? AND sub_account_name=?");
			$stmt->bind_param("is", $account_id, $name);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			if (!$row) {
				throw new Exception("Sub-account '$name' not found.");
			}
			$sub_account_ids[$name] = $row['sub_account_id'];
			$stmt->close();
		}

		// Check and initialize balances for relevant accounts
		function checkAndInitBalance(mysqli $conn, int $company_id, int $sub_account_id): bool
		{
			$stmt = $conn->prepare("SELECT balance FROM company_sub_account_balance WHERE company_id=? AND sub_account_id=?");
			$stmt->bind_param("ii", $company_id, $sub_account_id);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result->num_rows < 1) {
				$stmt = $conn->prepare("INSERT INTO company_sub_account_balance (company_id, sub_account_id, balance) VALUES (?, ?, 0)");
				$stmt->bind_param("ii", $company_id, $sub_account_id);
				$stmt->execute();
			}
			$stmt->close();
			return true;
		}

		// Initialize balances for payment method
		$relevant_sub_account_ids = ($payment_method == 'Cash') ?
			['Cash', 'Sales', 'Cost of Goods Sold', 'Inventory'] :
			['Accounts Receivable', 'Sales', 'Cost of Goods Sold', 'Inventory'];

		foreach ($relevant_sub_account_ids as $account) {
			checkAndInitBalance($conn, $company_id, $sub_account_ids[$account]);
		}

		// Process each item in the sale
		$total_price = 0;
		$total_cogs = 0; // Total Cost of Goods Sold

		for ($i = 0; $i < count($item_ids); $i++) {
			$item_id = $item_ids[$i];
			$unit_price = floatval($unitPrices[$i]);
			$selling_price = floatval($sellingPrices[$i]);
			$qty = intval($qtys[$i]);

			// Fetch item details and inventory
			$stmt = $conn->prepare("SELECT it.item_name, invt.qty AS inventory_qty, invt.unit_price AS inventory_unit_price, invt.inventory_id 
                                    FROM item_tbl it 
                                    INNER JOIN inventory_tbl invt ON it.item_id=invt.item_id 
                                    WHERE it.item_id=? AND it.company_id=?");
			$stmt->bind_param("ii", $item_id, $company_id);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();

				// Check inventory balance
				if ($row['inventory_qty'] < $qty) {
					throw new Exception("Not enough items in inventory");
				}

				// Update inventory quantity
				$new_qty = $row['inventory_qty'] - $qty;
				$stmt = $conn->prepare("UPDATE inventory_tbl SET qty=? WHERE inventory_id=?");
				$stmt->bind_param("ii", $new_qty, $row['inventory_id']);
				$stmt->execute();
				$stmt->close();

				// Calculate item total price and COGS
				$item_total = $selling_price * $qty;
				$total_price += $item_total;

				// Calculate COGS
				$cogs = $row['inventory_unit_price'] * $qty;
				$total_cogs += $cogs;

				// Insert sales receipt
				$stmt = $conn->prepare("INSERT INTO sales_receipt_tbl (company_id, customer_name, date_made, invoice_no, note, item_id, qty, sold_price, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$status = 1;
				$stmt->bind_param("issssiidis", $company_id, $customer_name, $date, $invoice_no, $notes, $item_id, $qty, $unit_price, $status, $payment_method);
				$stmt->execute();
				$stmt->close();
			} else {
				// Handle projects
				$stmt2 = $conn->prepare("SELECT * FROM project_tbl WHERE project_id=?");
				$stmt2->bind_param("i", $item_id);
				$stmt2->execute();
				$result2 = $stmt2->get_result();

				if ($result2->num_rows > 0) {
					$row2 = $result2->fetch_assoc();
					$item_total = $selling_price * $qty;
					$total_price += $item_total;

					$stmt3 = $conn->prepare("INSERT INTO sales_receipt_tbl (company_id, customer_name, date_made, invoice_no, note, project_id, qty, sold_price, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$status = 1;
					$stmt3->bind_param("issssiidis", $company_id, $customer_name, $date, $invoice_no, $notes, $item_id, $qty, $selling_price, $status, $payment_method);
					$stmt3->execute();
					$stmt3->close();
				}
				$stmt2->close();
			}

			// Update account balances
			if ($payment_method == 'Cash') {
				// Update balances for cash transactions
				$stmt = $conn->prepare("UPDATE company_sub_account_balance SET balance = balance + ? WHERE company_id = ? AND sub_account_id = ?");
				$stmt->bind_param("dii", $total_price, $company_id, $sub_account_ids['Cash']);
				$stmt->execute();
				$stmt->close();
			} else {
				// Update balances for credit transactions
				$stmt = $conn->prepare("UPDATE company_sub_account_balance SET balance = balance + ? WHERE company_id = ? AND sub_account_id = ?");
				$stmt->bind_param("dii", $total_price, $company_id, $sub_account_ids['Accounts Receivable']);
				$stmt->execute();
				$stmt->close();
			}

			// Update Sales Revenue and COGS
			$stmt = $conn->prepare("UPDATE company_sub_account_balance SET balance = balance + ? WHERE company_id = ? AND sub_account_id = ?");
			$stmt->bind_param("dii", $total_price, $company_id, $sub_account_ids['Sales']);
			$stmt->execute();
			$stmt->close();

			$stmt = $conn->prepare("UPDATE company_sub_account_balance SET balance = balance + ? WHERE company_id = ? AND sub_account_id = ?");
			$stmt->bind_param("dii", $total_cogs, $company_id, $sub_account_ids['Cost of Goods Sold']);
			$stmt->execute();
			$stmt->close();

			// Update Inventory balance
			$stmt = $conn->prepare("UPDATE company_sub_account_balance SET balance = balance - ? WHERE company_id = ? AND sub_account_id = ?");
			$stmt->bind_param("dii", $total_cogs, $company_id, $sub_account_ids['Inventory']);
			$stmt->execute();
			$stmt->close();
		}

		// Calculate tax and discount
		$tax_amount = $total_price * ($tax_rate / 100);
		$discount_amount = $total_price * ($discount_rate / 100);
		$total_after_discount = $total_price - $discount_amount;
		$final_total = $total_after_discount + $tax_amount;

		// Update sales receipt with tax and discount
		$stmt = $conn->prepare("UPDATE sales_receipt_tbl SET tax = ?, discount = ? WHERE company_id = ? AND invoice_no = ?");
		$stmt->bind_param("ddis", $tax_amount, $discount_amount, $company_id, $invoice_no);
		$stmt->execute();
		$stmt->close();

		// Commit transaction
		$conn->commit();
		header("Location: ../../pages/Company/Company.php?page=sales-receipt-preview&invoice_id=$invoice_no");
	} catch (Exception $e) {
		// Rollback transaction and handle error
		$conn->rollback();
		$em = "Transaction failed: " . $e->getMessage();
		header("Location: ../../pages/Company/Company.php?page=create-bill&error=" . urlencode($em));
		exit();
	}
}
?>