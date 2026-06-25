<link rel="stylesheet" href="../../assets/css/tables.css">
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
        <h2>Bank Statements</h2>
    </div>
    <div class="table_section">
        <table id="quotationData">
            <thead>
                <th>No.</th>
                <th>Bank Name</th>
                <th>Account No.</th>
                <th>Date Created</th>
                <th>Actions</th>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT 
                            bd.bank_name AS `Bank Name`, 
                            bd.account_number AS `Account No.`, 
                            SUM(bs.amount) AS `Total Amount`,
                            DATE_FORMAT(bs.date_created, '%Y-%m') AS `Month`
                        FROM 
                            bank_statement_tbl bs
                        JOIN 
                            bank_account_tbl ba ON bs.bank_id = ba.bank_id
                        JOIN 
                            bank_details_tbl bd ON ba.bank_details_id = bd.bank_details_id
                        WHERE 
                            YEAR(bs.date_created) = ? AND
                            bs.company_id = ?
                        GROUP BY 
                            bd.bank_name, 
                            bd.account_number, 
                            DATE_FORMAT(bs.date_created, '%Y-%m')
                        ORDER BY 
                            DATE_FORMAT(bs.date_created, '%Y-%m') ASC;
                        ";
                $date = date("Y");
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $date, $_SESSION['company_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = 1;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $count . "</td>";
                        echo "<td>" . $row['Bank Name'] . "</td>";
                        echo "<td>" . $row['Account No.'] . "</td>";
                        echo "<td>" . $row['Month'] . "</td>";
                        echo "<td><a href='./Company.php?page=show-bank-statement&date=" . $row['Month'] . "' class='btn btn-primary'><i class='fas fa-eye'></i></a> | 
                                  <a href='../../backend/data/generate-bank-statement-sheet.php?date=" . $row['Month'] . "' class='btn btn-primary'><i class='fas fa-print'></i></a></td>";
                        echo "</tr>";
                        $count++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(document).prop('title', 'All Bank Statements | Ginum');
        // navbar
        $("#dashboard").removeClass("active");
        $("#bank-statements").addClass("active");
        $('#quotationData').DataTable();
    });
</script>