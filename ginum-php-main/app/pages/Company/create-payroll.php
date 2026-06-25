<?php

require_once '../../backend/connection/conn.php';
$db = new DBConnection();
$conn = $db->conn;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM employee_tbl WHERE employee_id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
        $epfNo = $row['epf_no'];
    }
    ?>

    <div class="container py-2">
        <?php if (isset($_GET['success'])) { ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Done',
                    text: "<?php echo $_GET['success']; ?>"
                });
            </script>
        <?php } ?>
        <?php if (isset($_GET['error'])) { ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "<?php echo $_GET['error']; ?>"
                });
            </script>
        <?php } ?>
        <?php if (isset($_GET['warning'])) { ?>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Careful!',
                    text: "<?php echo $_GET['warning']; ?>"
                });
            </script>
        <?php } ?>

        <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
            <form action="../../backend/data/record-payroll-data.php" method="post">
                <h2>Payroll</h2>
                <hr>
                <h3 class="lead">Basic Info</h3>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="emp_name" class="form-label">Employee Name</label>
                            <input type="text" name="emp_name" value="<?php echo $firstName . " " . $lastName; ?>"
                                id="emp_name" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="epf_no" class="form-label">EPF No.</label>
                            <input type="text" name="epf_no" id="epf_no" value="<?php echo $epfNo; ?>" class="form-control"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="month" class="form-label">Month</label>
                            <select name="month" class="form-select" required>
                                <option value="">-- Select Month --</option>
                                <?php
                                for ($month = 1; $month <= 12; $month++) {
                                    $monthName = date("F", mktime(0, 0, 0, $month, 1));
                                    echo "<option value='$month'>$monthName</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="year" class="form-label">Year</label>
                            <select name="year" class="form-select" required>
                                <option value="">-- Select Year --</option>
                                <option value="<?= date("Y") - 1 ?>"><?= date("Y") - 1 ?></option>
                                <option value="<?= date("Y") ?>" selected><?= date("Y") ?></option>
                                <option value="<?= date("Y") + 1 ?>"><?= date("Y") + 1 ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="emp_no" class="form-label">Employee No.</label>
                            <input type="text" name="emp_no" value="<?php echo $id; ?>" id="emp_no" class="form-control"
                                readonly>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="lead">Earnings</h3>
                        <div class="mb-3 mt-4">
                            <label for="basic_salery" class="form-label">Basic Salary</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="basic_salary" id="basic_salery"
                                    aria-describedby="basic-addon3" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="allowance" class="form-label">Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="allowance" id="allowance"
                                    aria-describedby="basic-addon3" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ot_pay" class="form-label">Over Time Pay</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="ot_pay" id="ot_pay"
                                    aria-describedby="basic-addon3" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bonus" class="form-label">Bonus</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="bonus" id="bonus"
                                    aria-describedby="basic-addon3" value="0">
                            </div>
                        </div>
                        <div class="mb-3 mt-5">
                            <p class="lead text-success" id="totEarnings">Total Earnings: </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3 class="lead">Deduction</h3>
                        <div class="mb-3 mt-4">
                            <label for="epf_8" class="form-label">EPF (8%)</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="epf_8" id="epf_8"
                                    aria-describedby="basic-addon3" value="0" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="apit" class="form-label">APPIT</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="apit" id="apit"
                                    aria-describedby="basic-addon3" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="loans" class="form-label">Loans</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="loans" id="loans"
                                    aria-describedby="basic-addon3" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="other" class="form-label">Other</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="other" id="other"
                                    aria-describedby="basic-addon3" value="0">
                            </div>
                        </div>
                        <div class="mb-3 mt-5">
                            <p class="lead text-danger" id="totDeduction">Total Deduction: </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="lead">EPF & ETF</h3>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="epf_cal_8" class="form-label">EPF (8%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">Rs.</span>
                                        <input type="text" class="form-control" name="epf_cal_8" id="epf_cal_8"
                                            aria-describedby="basic-addon3" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="epf_cal_12" class="form-label">EPF (12%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">Rs.</span>
                                        <input type="text" class="form-control" name="epf_cal_12" id="epf_cal_12"
                                            aria-describedby="basic-addon3" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="epf_cal_20" class="form-label">EPF (Total)</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">Rs.</span>
                                        <input type="text" class="form-control" name="epf_cal_20" id="epf_cal_20"
                                            aria-describedby="basic-addon3" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="etf_cal_3" class="form-label">ETF (3%)</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Rs.</span>
                                <input type="text" class="form-control" name="etf_cal_3" id="etf_cal_3"
                                    aria-describedby="basic-addon3" value="0" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="lead mt-3" id="netPay">Net Pay: </h3>
                <button class="btn btn-success">Create Payroll</button>
                <button style="margin-left: 10px;" class="btn btn-outline-warning"><i class='bx bx-receipt'></i> Download
                    Payslip</button>
            </form>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $(document).prop('title', 'Create Payroll | Ginum');

            //navbar
            $("#dashboard").removeClass("active");
            $("#payrolls").addClass("active");

            // Make an AJAX request to the backend server to get the calculated APIT amount and set it to the APIT field
            function getAPITAmount(basicSalary) {
                $.ajax({
                    url: "../../backend/data/fetch-apit-value.php",
                    method: 'POST',
                    data: {
                        basic_salary: basicSalary,
                    },
                    success: function (data) {
                        $("#apit").val(data);
                        calculateTotalDeduction();
                    },
                    error: function (data) {
                        console.error(data);
                    }
                });
            }

            let basicSalary = 0;
            let allowance = 0;
            let otPay = 0;
            let bonus = 0;
            let loans = 0;
            let others = 0;
            let totalEarnings = 0;
            let totalDeduction = 0;
            let epf8 = 0;

            $("#basic_salery").keyup(function () {
                basicSalary = Number($(this).val());
                // Get the APIT according to the basic salary
                getAPITAmount(basicSalary);

                epf8 = basicSalary * (8 / 100);
                const epf12 = basicSalary * (12 / 100);
                const etf3 = basicSalary * (3 / 100);
                $("#epf_8").val(epf8);
                $("#epf_cal_8").val(epf8);
                $("#epf_cal_12").val(epf12);
                $("#epf_cal_20").val(epf8 + epf12);
                $("#etf_cal_3").val(etf3);
                calculateTotalEarnings();
                calculateTotalDeduction();
            });

            $("#allowance").keyup(function () {
                allowance = Number($(this).val());
                calculateTotalEarnings();
            });

            $("#ot_pay").keyup(function () {
                otPay = Number($(this).val());
                calculateTotalEarnings();
            });

            $("#bonus").keyup(function () {
                bonus = Number($(this).val());
                calculateTotalEarnings();
            });

            function calculateTotalEarnings() {
                totalEarnings = basicSalary + allowance + otPay + bonus;
                $("#totEarnings").text("Total Earnings: Rs." + totalEarnings);
                calculateNetpay();
            }

            $("#loans").keyup(function () {
                loans = Number($(this).val());
                calculateTotalDeduction();
            });

            $("#other").keyup(function () {
                others = Number($(this).val());
                calculateTotalDeduction();
            });

            function calculateTotalDeduction() {
                let apit = Number($("#apit").val());
                totalDeduction = loans + others + epf8 + apit;
                $("#totDeduction").text("Total Deduction: Rs." + totalDeduction);
                calculateNetpay();
            }

            function calculateNetpay() {
                const netPay = totalEarnings - totalDeduction;
                $("#netPay").text("Net Pay: Rs." + netPay);
            }
        });
    </script>

    <?php
}
?>