<div class="row d-flex justify-content-center align-items-center h-100">
    <div class="card" style="border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <h2 class="">Settings</h2>
            <hr class="mb-4 pb-2 pb-md-0 mb-md-5">

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

            <!-- content -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">Company Info.</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                        aria-controls="profile" aria-selected="false">Deductions</a>
                </li>
                <?php
                if ($_SESSION['role'] == 'Company Admin') {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab"
                            aria-controls="contact" aria-selected="false">Advanced</a>
                    </li>
                    <?php
                }
                ?>
            </ul>

            <div class="tab-content mt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h3>Change Company Information</h3>
                    <form action="../../backend/data/change-company-info.php" method="post" class="mt-3 row">
                        <?php
                        $sql = "SELECT * FROM company_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                        $result = $conn->query($sql);
                        if ($result->num_rows == 1) {
                            $row2 = $result->fetch_assoc();
                            ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="comName" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" name="comName" id="comName"
                                        value="<?= $row2['company_name'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="com_category" class="form-label">Company Category</label>
                                    <select name="com_category" class="form-select" id="com_category" required>
                                        <?php
                                        $sql = "SELECT * FROM company_category_tbl";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                ?>
                                                <option value="<?= $row['company_category_id'] ?>" <?php echo $row2['company_category_id'] == $row['company_category_id'] ? "selected" : "" ?>>
                                                    <?= $row['category_name'] ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="regNo" class="form-label">Registration No.</label>
                                    <input type="text" name="regNo" class="form-control" id="regNo"
                                        value="<?= $row2['company_reg_no'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tinNo" class="form-label">TIN No.</label>
                                    <input type="text" name="tinNo" class="form-control" id="tinNo"
                                        value="<?= $row2['tin_no'] ?>" required>
                                </div>
                            </div>
                            <div class=" col-md-4">
                                <div class="mb-3">
                                    <label for="vatNo" class="form-label">VAT No.</label>
                                    <input type="text" name="vatNo" class="form-control" id="vatNo"
                                        value="<?= $row2['vat_no'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone_no" class="form-label">Phone No. (Land Line)</label>
                                    <input type="text" name="phone_no" class="form-control" id="phone_no"
                                        value="<?= $row2['phone_no'] ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mobile_no" class="form-label">Mobile No.</label>
                                    <input type="text" name="mobile_no" class="form-control" id="mobile_no"
                                        value="<?= $row2['mobile_no'] ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reg_address" class="form-label">Registered Address</label>
                                    <textarea name="reg_address" id="reg_address" rows="5" class="form-control"
                                        required><?= $row2['company_registered_address'] ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fac_address" class="form-label">Factory Address</label>
                                    <textarea name="fac_address" id="fac_address" rows="5"
                                        class="form-control"><?= $row2['company_factory_address'] ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select name="country" id="country" class="form-select" required>
                                        <option>-- Choose your Country --</option>
                                        <?php
                                        $sql = "SELECT * FROM country_tbl ORDER BY country_name ASC";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                if ($_SESSION['country_id'] == $row['country_id']) {
                                                    ?>
                                                    <option value="<?= $row['country_code'] ?>" selected>
                                                        <?= $row['country_name'] ?> (<?= $row['country_code'] ?>)
                                                    </option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value="<?= $row['country_code'] ?>">
                                                        <?= $row['country_name'] ?> (<?= $row['country_code'] ?>)
                                                    </option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select name="currency_id" id="currency" class="form-select">
                                        <option>-- Choose your Currency --</option>
                                        <?php
                                        $sql = "SELECT * FROM currency_tbl ORDER BY currency_name ASC";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                ?>
                                                <option value="<?= $row['currency_id'] ?>" <?php echo $row2['currency_id'] == $row['currency_id'] ? "selected" : "" ?>>
                                                    <?= $row['currency_name'] ?> (<?= $row['currency_code'] ?>)
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" value="save">Save</button>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <h3>Add Deduction Criteria</h3>
                    <form action="../../backend/data/config-data.php" method="post" class="mt-3" id="form-components1">
                        <?php
                        $sql = "SELECT * FROM taxes_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <div class="row form-row1">
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label for="deduct_name" class="form-label">Deduction Name</label>
                                            <input type="text" id="deduct_name" value="<?= $row['tax_name'] ?>"
                                                name="deduction_names[]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label" for="ratio">Ratio</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="ratio" aria-describedby="basic-addon3"
                                                value="<?= $row['tax_percentage'] ?>" name="deduction_ratios[]">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label" for="remove">Remove</label><br>
                                            <button class="btn btn-outline-danger" type="button" id="removeTax">-</button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-secondary" id="add1">+</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <form action="../../backend/data/save-extended-tax-data.php" method="post" class="mt-5"
                        id="form-components2">
                        <h3>APIT</h3>
                        <?php
                        $sql = "SELECT * FROM extended_tax_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <div class="row form-row2">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="starting_value" class="form-label">Starting Value</label>
                                            <input type="text" id="starting_value" name="starting_values[]" class="form-control"
                                                value="<?= $row['starting_range'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="ending_value" class="form-label">Ending Value</label>
                                            <input type="text" id="ending_value" name="ending_values[]" class="form-control"
                                                value="<?= $row['ending_range'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="ratio">Tax Ratio</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="ratio" aria-describedby="basic-addon3"
                                                value="<?= $row['tax_rate'] ?>" name="tax_ratios[]">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label" for="remove">Remove</label><br>
                                            <button class="btn btn-outline-danger" type="button" id="remove">-</button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-secondary" id="add2">+</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <h3>Advanced Settings</h3>
                    <div id="google_translate_element"></div>
                    <?php
                    $sql = "SELECT * FROM company_tbl WHERE company_id='" . $_SESSION['company_id'] . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $company_id = $row['company_id'];
                        $email = $row['email'];
                        ?>
                        <form action="../../backend/data/company-adv-settings-data.php?id=<?= $company_id ?>" method="post"
                            class="mt-3">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="old_password" class="form-label">Old Password</label>
                                <input type="password" class="form-control" id="old_password" name="old_password">
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <div class="mb-3">
                                <label for="con_new_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="con_new_password" name="con_new_password">
                            </div>
                            <div class="mb-3">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Save Settings</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).prop('title', 'Settings | Ginum');

        // Template for dynamic elements
        const elements1 = `
        <div class="row form-row1">
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="deduct_name" class="form-label">Deduction Name</label>
                    <input type="text" class="form-control deduct-name" required>
                </div>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="ratio">Ratio</label>
                <div class="input-group">
                    <input type="text" class="form-control ratio" aria-describedby="basic-addon3">
                    <span class="input-group-text" id="basic-addon3">%</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label" for="remove">Remove</label><br>
                    <button class="btn btn-outline-danger removeTax" type="button">-</button>
                </div>
            </div>
        </div>
    `;

        const elements2 = `
        <div class="row form-row2">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="starting_value" class="form-label">Starting Value</label>
                    <input type="text" class="form-control starting-value" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="ending_value" class="form-label">Ending Value</label>
                    <input type="text" class="form-control ending-value" required>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="ratio">Tax Ratio</label>
                <div class="input-group">
                    <input type="text" class="form-control tax-ratio" aria-describedby="basic-addon3">
                    <span class="input-group-text" id="basic-addon3">%</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label" for="remove">Remove</label><br>
                    <button class="btn btn-outline-danger removeAPIT" type="button">-</button>
                </div>
            </div>
        </div>
    `;

        // Add new row
        $("#add1").click(function () {
            // Remove any existing submit button
            $("#form-components1 .submit-btn1").remove();

            // Append the new row
            $("#form-components1").append(elements1);

            // Append the submit button
            const submitButton1 = `
            <div class="mb-3 submit-btn1">
                <button class="btn btn-primary" type="submit" value="add1">Save</button>
            </div>
        `;
            $("#form-components1").append(submitButton1);
        });

        // Remove row
        $('#form-components1').on('click', '.removeTax', function () {
            $(this).closest('.form-row1').remove();

            // Remove the submit button if there are no more rows
            if ($('#form-components1 .form-row1').length === 0) {
                $("#form-components1 .submit-btn1").remove();
            }
        });

        // Add new row
        $("#add2").click(function () {
            // Remove any existing submit button
            $("#form-components2 .submit-btn2").remove();

            // Append the new row
            $("#form-components2").append(elements2);

            // Append the submit button
            const submitButton2 = `
            <div class="mb-3 submit-btn2">
                <button class="btn btn-primary" type="submit" value="add2">Save</button>
            </div>
        `;
            $("#form-components2").append(submitButton2);
        });

        // Remove row
        $('#form-components2').on('click', '.removeAPIT', function () {
            $(this).closest('.form-row2').remove();

            // Remove the submit button if there are no more rows
            if ($('#form-components2 .form-row2').length === 0) {
                $("#form-components2 .submit-btn2").remove();
            }
        });
    });

</script>