<?php
try {
    require_once 'backend/connection/conn.php';
    $db = new DBConnection();
    $conn = $db->conn;
} catch (Exception $e) {
    die($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ========== Meta Tags ========== -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Company Registration Form">
    <!-- ========== Page Title ========== -->
    <title>Register | Ginum</title>
    <link rel="shortcut icon" href="./assets/imgs/logos/ginum-logo.png" type="image/x-icon">
    <!-- ========== Stylesheets ========== -->
    <!-- Bootstarp CSS cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet">

    <style>
        body {
            background-color: #e1ecf1;
        }

        .registration-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 50px auto;
        }

        .container {
            padding-bottom: 20px;
            /* Adds space below the entire form container */
        }


        .form-control {
            border-radius: 5px;
        }

        .form-control,
        .form-select {
            height: 38px;
            /* Adjust to match the desired size */
        }

        .input-group-text {
            background-color: white;
            border-left: 0;
        }

        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="registration-form">

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
                        icon: 'warning',
                        title: 'Oops...',
                        text: "<?= $_GET['error'] ?>"
                    })
                </script>
            <?php } ?>
            <h2 class="text-center">Company Registration</h2>

            <form method="post" action="./backend/data/company-register-data.php"
                enctype="multipart/form-data">

                <!-- Company Name -->
                <div class="mb-3">
                    <label for="comName" class="form-label">Company Name</label>
                    <input type="text" class="form-control" name="comName" id="comName" placeholder="Enter company name" required>
                </div>

                <!-- Company Category (Dropdown with Icon) -->
                <div class="mb-3">
                    <label for="com_category" class="form-label">Company Category</label>
                    <div class="input-group">
                        <select name="com_category" class="form-select" id="com_category" required>
                            <?php
                            $sql = "SELECT * FROM company_category_tbl";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <option value="<?= $row['company_category_id'] ?>"><?= $row['category_name'] ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>


                <!-- Registration Details -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="regNo" class="form-label">Registration No.</label>
                        <input type="text" name="regNo" class="form-control" id="regNo">
                    </div>
                    <div class="col-md-4">
                        <label for="tinNo" class="form-label">TIN No.</label>
                        <input type="text" name="tinNo" class="form-control" id="tinNo">
                    </div>
                    <div class=" col-md-4">
                        <label for="vatNo" class="form-label">VAT No.</label>
                        <input type="text" name="vatNo" class="form-control" id="vatNo">
                    </div>
                    <div class="col-md-6">
                        <label for="phone_no" class="form-label">Phone No. (Land Line)</label>
                        <input type="text" name="phone_no" class="form-control" id="phone_no">
                    </div>
                    <div class="col-md-6">
                        <label for="mobile_no" class="form-label">Mobile No.</label>
                        <input type="text" name="mobile_no" class="form-control" id="mobile_no">
                    </div>
                    <div class="col-md-6">
                        <label for="reg_address" class="form-label">Registered Address</label>
                        <textarea name="reg_address" id="reg_address" rows="5" class="form-control"
                            required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="fac_address" class="form-label">Factory Address</label>
                        <textarea name="fac_address" id="fac_address" rows="5"
                            class="form-control"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="country" class="form-label">Country</label>
                        <select name="country_id" id="country" class="form-select">
                            <?php
                            $sql = "SELECT * FROM country_tbl ORDER BY country_name ASC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <option value="<?= $row['country_id'] ?>" selected><?= $row['country_name'] ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="currency" class="form-label">Currency</label>
                        <select name="currency_id" id="currency" class="form-select">
                            <?php
                            $sql = "SELECT * FROM currency_tbl ORDER BY currency_name ASC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <option value="<?= $row['currency_id'] ?>" selected>
                                        <?= $row['currency_name'] ?> (<?= $row['currency_code'] ?>)
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" name="website" class="form-control" id="website">
                    </div>

                    <!-- Password & Confirm Password -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="password">
                                <span class="input-group-text password-toggle" onclick="togglePassword('password')">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="con_password" class="form-control" id="confirm-password">
                                <span class="input-group-text password-toggle" onclick="togglePassword('confirm-password')">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-12">
                                    <label for="logo" class="form-label">Upload Company Logo</label>
                                    <input type="file" class="form-control" name="logo" id="logo" accept=".jpeg, .png"
                                        area-describedby="logoHelp" required>
                                    <div id="logoHelp" class="form-text">Please upload a 160x160 image file.
                                    </div>
                                </div> -->

                    <!-- Submit Button -->
                    <!-- Submit Button and Account Link -->
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="submit" class="btn rounded-pill px-4 shadow-sm" style="background-color: #52c7f3; color: white;"
                            onmouseover="this.style.backgroundColor='#022b6d'" onmouseout="this.style.backgroundColor='#52c7f3'">Sign In</button>
                        <p class="mt-3 mb-0 ms-3"><a href="./login.php">I already have an account</a></p>
                    </div>

            </form>
        </div>
    </div>

    <!-- JavaScript for Password Visibility -->
    <script>
        function togglePassword(id) {
            let field = document.getElementById(id);
            let icon = field.nextElementSibling.querySelector('i');
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

</body>

</html>