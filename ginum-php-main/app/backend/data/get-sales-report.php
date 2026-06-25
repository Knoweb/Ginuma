<?php
if (isset($_POST['start_date']) && !empty($_POST['start_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = isset($_POST['end_date']) && !empty($_POST['end_date']) ? $_POST['end_date'] : date("Y-m-d");

    $sql = "SELECT it.item_id, srt.qty, it.item_name, invt.unit_price 
            FROM sales_receipt_tbl srt 
            INNER JOIN item_tbl it ON srt.item_id = it.item_id 
            INNER JOIN inventory_tbl invt ON it.item_id = invt.item_id 
            WHERE it.company_id = ? AND srt.date_made BETWEEN ? AND ?";

    $stmt = $conn->prepare($sql);
    $company_id = $_SESSION['company_id'];
    $stmt->bind_param("sss", $company_id, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $i = 1;
        $total_amount = 0;
        while ($row = $result->fetch_assoc()) {
            $item_total = $row['qty'] * $row['unit_price'];
            $total_amount += $item_total;
            ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['qty'] ?></td>
                <td><?= number_format($row['unit_price'], 2) ?></td>
                <td><?= number_format($item_total, 2) ?></td>
            </tr>
            <?php
            $i++;
        }
        ?>
        <tr>
            <td colspan="4"><strong>Total Amount</strong></td>
            <td><strong><?= number_format($total_amount, 2) ?></strong></td>
        </tr>
        <?php
    } else {
        echo "<tr><td colspan='5'>No sales records found for the selected date range.</td></tr>";
    }
} else {
    echo "Error: Start date is required.";
}
?>