<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];
    if ($type == 'Employee') {
        $sql = "SELECT * FROM employee_tbl et INNER JOIN designation_tbl dst ON (et.designation_id=dst.designation_id) INNER JOIN department_tbl dpt ON (dst.department_id=dpt.department_id) WHERE et.employee_id='" . $id . "' AND et.company_id='" . $_SESSION['company_id'] . "'";
    } elseif ($type == 'Customer') {
        $sql = "SELECT * FROM customer_tbl WHERE customer_id = '$id'";
    } elseif ($type == 'Supplier') {
        $sql = "SELECT * FROM supplier_tbl WHERE supplier_id = '$id'";
    }
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $type == 'Employee' ? $row['first_name'] . " " . $row['last_name'] : ($type == 'Supplier' ? $row['supplier_name'] : $row['name']);
        $email = $row['email'];
        $address = $row['address'];
        $title = $type == 'Employee' ? $row['designation_name'] : ($type == 'Supplier' ? 'Supplier' : 'Customer');
        $gender = $type == 'Employee' ? $row['gender'] : ($type == 'Supplier' ? 'Supplier' : 'Customer');
        $phone = $type == 'Employee' ? $row['mobileNo'] : $row['phone_no'];
        $user_type = $type == 'Supplier' ? $row['supplier_type'] : ($type == 'Customer' ? $row['customer_type'] : '');
    }
    ?>
    <div class="container mt-4">
        <div class="main-body">
            <div class="row gutters-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="<?= $gender == 'Female' ? 'https://bootdey.com/img/Content/avatar/avatar3.png' : 'https://bootdey.com/img/Content/avatar/avatar7.png' ?>"
                                    alt="Admin" class="rounded-circle" width="150">
                                <div class="mt-3">
                                    <h4><?= $name ?></h4>
                                    <p class="text-secondary mb-1"><?= $title ?></p>
                                    <p class="text-muted font-size-sm"><?= $address ?></p>
                                    <a class="btn btn-warning"
                                        href="./Company.php?page=<?= $type == 'Employee' ? 'edit-employee' : ($type == 'Supplier' ? 'edit-supplier' : 'edit-customer') ?>&id=<?= $id ?>">Edit</a>
                                    <?php
                                    if ($_SESSION['role'] == 'Company Admin') {
                                        ?>
                                        <a class="btn btn-danger" onclick="confirm('<?= $type ?>', '<?= $id ?>')">Delete</a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Full Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $name ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $email ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Phone</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $phone ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">
                                        <?= $type == 'Supplier' ? 'Supplier Type' : ($type == 'Customer' ? 'Customer Type' : 'Department') ?>
                                    </h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $type == 'Supplier' ? $row['supplier_type'] : ($type == 'Customer' ? $row['customer_type'] : $row['department_name']) ?>
                                </div>
                            </div>
                            <hr class="hrs">
                            <div class="row" id="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0" id="title"></h6>
                                </div>
                                <div class="col-sm-9 text-secondary" id="value"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).prop('title', '<?= $type ?> Profile | Ginum');
        $(document).ready(function () {
            if ("<?= $type ?>" == "Employee") {
                $("#title").text("Date Joined")
                $("#value").text("<?= $type == 'Employee' ? $row['date_joined'] : "" ?>")
            } else {
                $("#row").hide();
                $(".hrs").hide();
            }
        });

        function confirm(type, id) {
            Swal.fire({
                icon: 'question',
                title: "Do you want to delete the " + type.toLowerCase() + "?",
                showCancelButton: true,
            confirmButtonColor:  '#d33',
            cancelButtonColor:'#3085d6' ,
             confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    if (type == 'Employee') {
                        window.location.href = "../../backend/data/delete-employee-data.php?id=" + id + "";
                    } else if (type == 'Supplier') {
                        window.location.href = "../../backend/data/delete-supplier-data.php?id=" + id + "";
                    } else {
                        window.location.href = "../../backend/data/delete-customer-data.php?id=" + id + "";
                    }
                } else if (result.isDenied) {
                    Swal.fire("Changes are not saved", "", "info");
                }
            });
        }
    </script>

    <?php

} else {
    // redirect to parent page
}
?>