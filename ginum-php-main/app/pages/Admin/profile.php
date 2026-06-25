<?php
if (isset($_GET['id'])) {
    $company_id = $_GET['id'];
    // import the database connection class
    require_once '../../backend/connection/conn.php';
    $db = new DBConnection();
    // get the database connection
    $conn = $db->conn;

    // fetch the company information from the database
    $sql = "SELECT * FROM company_tbl ct INNER JOIN currency_tbl crt ON (ct.currency_id=crt.currency_id) INNER JOIN country_tbl cunt ON (ct.country_id=cunt.country_id) INNER JOIN company_category_tbl cct ON (ct.company_category_id=cct.company_category_id) WHERE ct.company_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $company_data_row = $result->fetch_assoc();
    ?>

    <div class="container mt-4">
        <div class="main-body">
            <div class="row gutters-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="<?= $_SESSION['img'] ? $_SESSION['img'] : "../../assets/imgs/skyscraper.png" ?>"
                                    alt="Admin" class="rounded-circle" width="150">
                                <div class="mt-3">
                                    <h4><?= $company_data_row['company_name'] ?></h4>
                                    <p class="text-muted font-size-sm">
                                        <?= $company_data_row['company_registered_address'] ?>
                                    </p>
                                    <!-- <a class="btn btn-warning" href="./Company.php?page=settings">Edit</a> -->
                                    <?php
                                    /*
                                     * These are the status codes that company would have to
                                     * 0 - Pending -> Pending for accept or reject the request
                                     * 1 - Active -> Company registration form accepted by Administrator and Active 
                                     * 2 - Rejected -> Company registration form rejected by Administrator
                                     * 3 - Not Paid -> Company not paid for this application
                                     */
                                    if ($company_data_row['status'] == '0') {
                                        ?>
                                        <a class="btn btn-sm btn-success"
                                            href="../../backend/data/change-company-status.php?status=1&id=<?= $company_id ?>">Accept</a>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="confirmation(<?= $company_id ?>)">Reject</button>
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
                                    <h6 class="mb-0">Company Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $company_data_row['company_name'] ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $company_data_row['email'] ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Phone No.</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $company_data_row['phone_no'] ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Mobile No.</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $company_data_row['mobile_no'] ? $company_data_row['mobile_no'] : "-" ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">
                                        Company Category
                                    </h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?= $company_data_row['category_name'] ?>
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
            <div class="card container-fluid">
                <div class="row mt-3">
                    <h4 class="mb-4">Additional Details</h4>
                    <!-- 1st row -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="regNo" class="form-label">Company Registration No.</label>
                            <input type="text" class="form-control" name="regNo" id="regNo"
                                value="<?php echo $company_data_row['company_reg_no'] ?>" readonly />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="vatNo" class="form-label">VAT No.</label>
                            <input type="text" class="form-control" name="vatNo" id="vatNo"
                                value="<?php echo $company_data_row['vat_no'] ?>" readonly />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tinNo" class="form-label">TIN No.</label>
                            <input type="text" class="form-control" name="tinNo" id="tinNo"
                                value="<?php echo $company_data_row['tin_no'] ?>" readonly />
                        </div>
                    </div>
                    <!-- end of 1st row -->

                    <!-- 2nd row -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_reg_address" class="form-label">Company Registered Address</label>
                            <textarea class="form-control" id="company_reg_address"
                                readonly><?= $company_data_row['company_registered_address'] ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_reg_address" class="form-label">Company Factory Address</label>
                            <textarea class="form-control" id="company_reg_address"
                                readonly><?= $company_data_row['company_factory_address'] ?></textarea>
                        </div>
                    </div>
                    <!-- end of 2nd row -->

                    <!-- 3rd row -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input class="form-control" id="country" value="<?php echo $company_data_row['country_name'] . " (" .
                                $company_data_row['country_code'] . ")" ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency Unit</label>
                            <input class="form-control" id="currency"
                                value="<?php echo $company_data_row['currency_name'] . " (" . $company_data_row['currency_code'] . ")" ?>"
                                readonly>
                        </div>
                    </div>
                    <!-- end of 3rd row -->

                    <!-- 4th row -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_joined" class="form-label">Date Joined</label>
                            <input class="form-control" id="date_joined" value="<?= $company_data_row['date_joined'] ?>"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="last_updated" class="form-label">Last Updated</label>
                            <input class="form-control" id="last_updated" value="<?= $company_data_row['date_updated'] ?>"
                                readonly>
                        </div>
                    </div>
                    <!-- end of 4th row -->
                </div>
            </div>

            <?php
}
?>

        <script>
            //change the page title
            $(document).prop('title', 'Company Profile');

            function confirmation(id) {
                Swal.fire({
                    icon: 'question',
                    title: "Do you want to save the changes?",
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Save",
                    denyButtonText: `Don't save`
                }).then((result) => {
                    if (result.isConfirmed) {
                        // reject the request
                        // redirect to the backend controller
                        window.location.href = href = `../../backend/data/change-company-status.php?status=2&id=${id}`;
                        // Swal.fire("Saved!", "", "success");
                    } else if (result.isDenied) {
                        Swal.fire("Changes are not saved", "", "info");
                    }
                });
            }
        </script>