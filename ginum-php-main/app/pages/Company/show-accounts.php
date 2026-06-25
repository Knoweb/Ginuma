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
<div>
    <div class="table_header">
        <h2>Accounts Details</h2>
    </div>
    <div class="table_section">
        <table id="accData" class="table table-striped">
            <thead>
                <th>No.</th>
                <th>Account Name</th>
                <th>Account Type</th>
                <th>Amount</th>
            </thead>
            <tbody id="tableData">
                <?php
                $sql = "SELECT 
                            ma.account_name AS account_name, 
                            sa.sub_account_name AS actual_sub_account_name, 
                            sat.type_name AS display_sub_account_name, 
                            SUM(IFNULL(csab.balance, 0)) AS balance
                        FROM 
                            account_tbl ma 
                        JOIN 
                            sub_account_tbl sa ON ma.account_id = sa.account_id 
                        LEFT JOIN 
                            sub_account_type_tbl sat ON sa.sub_account_id = sat.sub_account_id 
                            AND sat.company_id = '" . $_SESSION['company_id'] . "'
                        LEFT JOIN 
                            company_sub_account_balance csab ON (csab.sub_account_id = sa.sub_account_id 
                            AND csab.sub_account_type_id IS NULL 
                            AND csab.company_id = '" . $_SESSION['company_id'] . "') 
                            OR (csab.sub_account_type_id = sat.sub_account_type_id 
                            AND csab.company_id = '" . $_SESSION['company_id'] . "')
                        GROUP BY 
                            ma.account_name, sa.sub_account_name, sat.type_name
                        ORDER BY 
                            ma.account_name, sa.sub_account_name, sat.type_name"
                ;

                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['account_name'] ?></td>
                            <td>
                                <?php
                                echo $row['actual_sub_account_name'] .
                                    ($row['display_sub_account_name'] != null ? " (" . $row['display_sub_account_name'] . ")" : "");

                                ?>
                            </td>
                            <td><?= $row['balance'] ?></td>
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


</section>

<script>
    $(document).prop('title', 'All Accounts | Ginum');

    $(document).ready(function () {
        // navbar
        $("#dashboard").removeClass("active");
        $("#account").addClass("active");

        $('#accData').DataTable();
    });

    // function to confirm if the admin want to delete the selected user
    function confirm(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to delete the customer?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Save",
            denyButtonText: `Don't save`
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                window.location.href = "../../backend/data/delete-customer-data.php?id=" + id + "";
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
</script>