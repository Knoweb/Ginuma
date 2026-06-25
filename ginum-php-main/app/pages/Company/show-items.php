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

    <div class="table_header ">
        <h2>Items</h2>
    </div>
    <div class="table_section">
        <table id="itemData" class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Unit Price (<?= $_SESSION['currency_code'] ?>)</th>
                    <!-- <th>Total (<?= $_SESSION['currency_code'] ?>)</th> -->
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM item_tbl it INNER JOIN inventory_tbl invt ON (invt.item_id=it.item_id) WHERE it.company_id='" . $_SESSION['company_id'] . "' AND status=1";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['item_id'];
                        $item_name = $row['item_name'];
                        $date_added = $row['date_added'];
                        $unit_price = (float) $row['unit_price'];
                        $qty = (float) $row['qty'];
                        ?>
                        <tr>
                            <th><?= $i ?></th>
                            <td><?= $item_name ?></td>
                            <td><?= $qty ?></td>
                            <td><?= $unit_price ?></td>
                            <td><?= $date_added ?></td>
                            <td>
                                <div class="text-center">
                                    <!-- <button class="btn btn-outline-info fa-solid fa-magnifying-glass"
                                        onclick="window.location.href=''" title="See details"></button> -->
                                    <button class="btn btn-outline-warning fa-solid fa-pen-to-square"
                                        onclick="window.location.href='./Company.php?page=edit-item&id=<?= $id ?>'"
                                        title="Edit Item Details"></button>
                                    <?php
                                    if ($_SESSION['role'] == 'Company Admin') {
                                        ?>
                                        <button class="btn btn-outline-danger fa-solid fa-trash" onclick="confirm(<?= $id ?>)"
                                            title="Delete Item"></button>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            $(document).prop('title', 'Show Items | Ginum');
            // navbar
            $("#dashboard").removeClass("active");
            $("#items").addClass("active");

            $('#itemData').DataTable();
        })

        // function to confirm if the admin want to delete the selected user
        function confirm(id) {
            Swal.fire({
                icon: 'question',
                title: "Do you want to delete the project?",
                showCancelButton: true,
            confirmButtonColor:  '#d33',
            cancelButtonColor:'#3085d6' ,
             confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    Swal.fire("OK", "", "info");
                    // window.location.href = "../../backend/data/delete-department-data.php?id=" + id + "";
                } else if (result.isDenied) {
                    Swal.fire("Changes are not saved", "", "info");
                }
            });
        }
    </script>

</div>