<?php
// start the session for all the dashboard pages
session_start();

// import the database connection class
require_once '../../backend/connection/conn.php';
// create a new instance of the database connection class
$db = new DBConnection();
// call the $conn attribute on the database connection class instance
$conn = $db->conn;

// Enable error display and error reporting for all the dashboard pages
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

// check if the user is logged in and the company_id is set in the session.
if (!($_SESSION['email']) || !$_SESSION['company_id']) {
    // redirect to the login page if the user is not logged in or the company_id is not set in the session.
    header("Location: ../../login.php");
}

// get the current status of the inventory
// return the number of rows in the inventory table in the database.
function getInventoryStatus(mysqli $conn): int|string
{
    // check inventory status
    // get the sub_account_id of Inventory account from the sub_account_tbl
    $sql = "SELECT sub_account_id FROM sub_account_tbl sat INNER JOIN account_tbl at ON (sat.account_id = at.account_id) WHERE sat.sub_account_name='Inventory'";
    $result = $conn->query($sql);
    if ($result->num_rows < 1) {
        // throw new Exception("Could not find the asset account in the database.");
    }
    $inventory_sub_account_id = $result->fetch_assoc()['sub_account_id'];
    // $result->close();
    // now we need to check if the Inventory account's balance is higher than 0
    $sql = "SELECT balance FROM company_sub_account_balance WHERE sub_account_id='$inventory_sub_account_id' AND company_id='" . $_SESSION['company_id'] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows <= 0) {
        // throw new Exception("Could not find the inventory account in the database.");
    }
    // $result->close();
    $inventory_sub_account_balance = $result->fetch_assoc()["balance"];
    if ($inventory_sub_account_balance > 0) {
        // now we need to check if the table inventory_tbl or item_tbl has some records or not.
        $sql = "SELECT * FROM inventory_tbl invt INNER JOIN item_tbl it ON (it.item_id=invt.item_id) WHERE invt.company_id='" . $_SESSION['company_id'] . "'";
        $result = $conn->query($sql);
        return $result->num_rows;
    }
    return 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Fonts -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    </style>

    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- css -->
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <!-- Jquery -->
    <script src="../../assets/js/jquery.min.js"></script>
    <!-- sweetalert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- datatable css -->
    <link href="https://cdn.datatables.net/v/bs5/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <!-- datatable js -->
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.4/datatables.min.js"></script>

    <!-- favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/imgs/logos/ginum-logo.png">

    <title>Admin | Ginum</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@200&display=swap');

        .notifications {
            width: 300px;
            height: 0px;
            opacity: 0;
            position: absolute;
            top: 63px;
            right: 62px;
            border-radius: 5px 0px 5px 5px;
            background-color: #fff;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)
        }

        .notifications h2 {
            font-size: 14px;
            padding: 10px;
            border-bottom: 1px solid #eee;
            color: #999
        }

        .notifications h2 span {
            color: #f00
        }

        .notifications-item {
            display: flex;
            border-bottom: 1px solid #eee;
            padding: 6px 9px;
            margin-bottom: 0px;
            cursor: pointer
        }

        .notifications-item:hover {
            background-color: #eee
        }

        .notifications-item img {
            display: block;
            width: 50px;
            height: 50px;
            margin-right: 9px;
            border-radius: 50%;
            margin-top: 2px
        }

        .notifications-item .text h4 {
            color: #777;
            font-size: 16px;
            margin-top: 3px
        }

        .notifications-item .text p {
            color: #aaa;
            font-size: 12px
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="./Company.php?page=dashboard" class="brand text-center">
            <img src="../../assets/imgs/logos/ginum_logo.png" alt="ginum logo" width="170" style="margin-top: 15px;">
        </a>
        <ul class="side-menu">
            <li><a href="./Company.php?page=dashboard" class="active" id="dashboard"><i
                        class='bx bxs-dashboard icon'></i>
                    <b>Dashboard</b>
                </a></li>
            <li class="divider" data-text="Company">
                <b>Company</b>
            </li>
            <li>
                <a href="#" id="employee"><i class='bx bxs-user icon'></i>
                    <b>Employee</b> <i class='bx bx-chevron-right icon-right'></i>
                </a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=add-employee">Add Employee</a></li>
                    <li><a href="./Company.php?page=show-employees">Show Employees</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="department"><i class='bx bxs-building icon'></i><b>Department</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=add-department">Add Department</a></li>
                    <li><a href="./Company.php?page=add-designation">Add Designations</a></li>
                    <li><a href="./Company.php?page=show-departments">Show Department</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="supplier"><i class="fa-solid fa-truck-field icon"></i><b>Supplier</b><i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=show-suppliers">Show Suppliers</a></li>
                    <li><a href="./Company.php?page=add-supplier">Add Supplier</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="customer"><i class='bx bxs-inbox icon'></i><b>Customer</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=show-customers">Show Customers</a></li>
                    <li><a href="./Company.php?page=add-customer">Add Customer</a></li>
                </ul>
            </li>

            <li>
                <a href="#" id="payrolls"><i class="fa-solid fa-receipt icon"></i><b>Payrolls</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=employee-payroll">Create Payroll</a></li>
                    <!-- <li><a href="./Company.php?page=payslip">Payslip</a></li> -->
                </ul>
            </li>
            <li>
                <a href="#" id="sales"><i class="fa-solid fa-money-bill icon"></i><b>Sales</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=create-bill">New Sales Receipt</a></li>
                    <li><a href="./Company.php?page=show-all-receipts">Show Sales Receipt</a></li>
                    <li><a href="./Company.php?page=show-all-income">All Income</a></li>
                    <li><a href="./Company.php?page=new-income">New Other Income</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="purchase"><i class="fa-regular fa-money-bill-1 icon"></i><b>Purchases</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=show-all-expenses">All Expenses</a></li>
                    <li><a href="./Company.php?page=new-expense">New Expense</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="orders"><i class="fa-solid fa-cart-shopping icon"></i><b>Orders</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=show-orders">All Orders</a></li>
                    <li><a href="./Company.php?page=add-order">New Order</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="items"><i class="fa-solid fa-bag-shopping icon"></i><b>Inventory</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=show-items">All Items</a></li>
                    <li><a href="./Company.php?page=update-inventory-item" id="update_inventory">Update Inventory for
                            Item</a></li>
                    <li><a href="./Company.php?page=add-item">Add Item</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="transactions"><i class="fa-brands fa-nfc-directional icon"></i><b>Transactions</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=add-transactions">Record Transactions</a></li>
                    <li><a href="./Company.php?page=show-transactions"> General Ledger</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="reports"><i class="fa-solid fa-flag icon"></i><b>Reports</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=balance-sheet">Balance Sheet</a></li>
                    <li><a href="./Company.php?page=income-statement">Income Statement</a></li>
                    <li><a href="./Company.php?page=trial-balance-sheet">Trial Balance</a></li>
                    <li><a href="./Company.php?page=sales-day-report">Daily Sales Report</a></li>
                    <li><a href="./Company.php?page=revenue-day-report">Daily Revenue Report</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="quotations"><i class="fa-solid fa-quote-left icon"></i><b>Quotations</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=create-quotation">Create Quotation</a></li>
                    <li><a href="./Company.php?page=show-quotations">All Quotations</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="depreciation"><i class="fa-solid fa-chart-area icon"></i><b>Depreciation</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=add-depreciation">Add depreciation</a></li>
                    <li><a href="./Company.php?page=show-depreciation">Show Assets</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="bank-statements"><i class="fa-solid fa-building-columns icon"></i><b>Bank</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=add-bank-account">Add Bank Account</a></li>
                    <li><a href="./Company.php?page=update-bank-account-balance">Bank Account</a></li>
                    <li><a href="./Company.php?page=add-bank-statement">Add Bank Statement</a></li>
                    <li><a href="./Company.php?page=all-bank-statements">Bank Statements</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="account"><i class="fa-solid fa-file-invoice-dollar icon"></i><b>Accounts</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=add-account">Add Account</a></li>
                    <li><a href="./Company.php?page=add-sub-account">Add Sub Account</a></li>
                    <li><a href="./Company.php?page=show-accounts">All Acounts</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="advanced"><i class="fa-solid fa-gears icon"></i><b>Advanced</b> <i
                        class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="./Company.php?page=add-user">Add Users</a></li>
                    <li><a href="./Company.php?page=show-users">Show Users</a></li>
                </ul>
            </li>
            <li class="divider" data-text="Extensions">Extensions</li>
            <li><a href="./Company.php?page=add-opening-balance" id="opening_balance"><i
                        class='fa-solid fa-dollar-sign icon'></i><b>Opening
                        Balances</b></a>
                <?php
                $request_count = 0;
                if ($_SESSION['role'] == 'Company Admin') {
                    $sql = "SELECT * FROM edit_requests WHERE status='pending' AND company_id='" . $_SESSION['company_id'] . "'";
                    $result = $conn->query($sql);
                    $request_count = $result->num_rows;

                ?>
            <li><a href="./Company.php?page=show-notifications" id="notifications"><i
                        class='fa-regular fa-comment icon'></i><b>Requests</b> &nbsp; <span
                        class="badge text-bg-danger"><?= $request_count ?: 0 ?></span></a></li>
            <br><span> Logged in as: <br /><strong><?= $_SESSION['role'] ?></strong></span>
        <?php
                }
        ?>

        <!-- <li> -->
        </ul>
    </section>
    <!-- SIDEBAR -->
    <!-- NAVBAR -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu toggle-sidebar'></i>
            <form action="#">

            </form>
            <!-- <?php

                    if ($_SESSION['role'] == 'Company Admin') {
                        $sql = "SELECT * FROM edit_requests WHERE company_id='" . $_SESSION['company_id'] . "'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            $count = 0;
                    ?>
                    <a href="#" class="nav-link">
                        <i class='bx bxs-bell icon' id="bell"></i>
                        <span class="badge"><?= $result->num_rows ?></span>
                    </a>
                    <div class="notifications" id="box">
                        <h2>Notifications - <span><?= $result->num_rows ?></span></h2>
                        <?php
                            while ($row = $result->fetch_assoc()) {
                                $sql2 = "SELECT * FROM sub_logins_tbl slt INNER JOIN employee_tbl et ON (slt.employee_id=et.employee_id) WHERE slt.sub_login_id='" . $row['sub_login_id'] . "' AND slt.company_id='" . $_SESSION['company_id'] . "'";
                                $result2 = $conn->query($sql2);
                                if ($result2->num_rows > 0) {
                                    $row2 = $result2->fetch_assoc();
                                }
                        ?>

                            <div class="notifications-item">
                                <div class="text">
                                    <h4>Details edit request</h4>
                                    <p><strong><?= $row2['first_name'] . " " . $row2['last_name'] ?>
                                        </strong> is asking to edit details in
                                        <?= $row['section'] ?>
                                    </p>
                                </div>
                            </div>

                            <?php
                            }
                            ?>
                    </div>
                    <?php
                        }
                    }
                    ?> -->

            <span class="divider"></span>
            <div class="profile">
                <img src="<?= $_SESSION['img'] ? $_SESSION['img'] : "../../assets/imgs/skyscraper.png" ?>"
                    alt="Company Logo">
                <ul class="profile-link">
                    <li><a href="./Company.php?page=company-profile"><i class='bx bxs-user-circle icon'></i>
                            Profile</a>
                    </li>
                    <li><a href="./Company.php?page=settings"><i class='bx bxs-cog'></i> Settings</a></li>
                    <li><a href="../logout.php?msg=Logged Out Successfully"><i
                                class='bx bxs-log-out-circle'></i>Logout</a></li>
                </ul>
            </div>
        </nav>
        <!-- NAVBAR -->
        <!-- MAIN -->
        <main>
            <!-- Page content -->
            <?php
            // routers for each pages in the application (company dashboard only)
            // check if the page attribute is in the site url
            if (isset($_GET['page'])) {
                // get the current page name from the site url
                $page = $_GET['page'];
                // check the page name and load the appropriate page content into the current page
                switch ($page) {
                    case 'dashboard':
                        include("./dashboard.php");
                        break;
                    case 'add-customer':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./add-customer.php");
                        }
                        break;
                    case 'add-employee':
                        if ($_SESSION['privilege_name'] == 'Inventory') {
                            include("./dashboard.php");
                        } else {
                            include("./add-employee.php");
                        }
                        break;
                    case 'add-supplier':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./add-supplier.php");
                        }
                        break;
                    case 'edit-customer':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./edit-customer.php");
                        }
                        break;
                    case 'edit-employee':
                        if ($_SESSION['privilege_name'] == 'Inventory') {
                            include("./dashboard.php");
                        } else {
                            include("./edit-employee.php");
                        }
                        break;
                    case 'edit-supplier':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./edit-supplier.php");
                        }
                        break;
                    case 'show-customers':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./show-customer.php");
                        }
                        break;
                    case 'show-suppliers':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./show-supplier.php");
                        }
                        break;
                    case 'show-employees':
                        if ($_SESSION['privilege_name'] == 'Inventory') {
                            include("./dashboard.php");
                        } else {
                            include("./show-employee.php");
                        }
                        break;
                    case 'add-department':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-department.php");
                        }
                        break;
                    case 'edit-department':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./edit-department.php");
                        }
                        break;
                    case 'show-departments':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-departments.php");
                        }
                        break;
                    case 'add-designation':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-designations.php");
                        }
                        break;
                    case 'user-profile':
                        include("./user-profile.php");
                        break;
                    case 'employee-payroll':
                        if ($_SESSION['privilege_name'] == 'Inventory') {
                            include("./dashboard.php");
                        } else {
                            include("./employees-for-payroll.php");
                        }
                        break;
                    case 'create-payroll':
                        if ($_SESSION['privilege_name'] == 'Inventory') {
                            include("./dashboard.php");
                        } else {
                            include("./create-payroll.php");
                        }
                        break;
                    case 'payslip':
                        if ($_SESSION['privilege_name'] == 'Inventory') {
                            include("./dashboard.php");
                        } else {
                            include("./payslip.php");
                        }
                        break;

                    case 'create-bill':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./sales-receipt.php");
                        }
                        break;
                    case 'sales-receipt-preview':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./sale-receipt-preview.php");
                        }
                        break;
                    case 'show-all-receipts':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./show-all-receipts.php");
                        }
                        break;
                    case 'new-expense':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./new-expense.php");
                        }
                        break;
                    case 'show-all-expenses':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-expenses.php");
                        }
                        break;
                    case 'add-order':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-order.php");
                        }
                        break;
                    case 'edit-order':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./edit-order.php");
                        }
                        break;
                    case 'show-orders':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-orders.php");
                        }
                        break;
                    case 'add-item':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./add-item.php");
                        }
                        break;
                    case 'show-items':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./show-items.php");
                        }
                        break;
                    case 'edit-item':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./edit-item.php");
                        }
                        break;
                    case 'item':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./item-data.php");
                        }
                    case 'add-transactions':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-properties.php");
                        }
                        break;
                    case 'show-accounts':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-accounts.php");
                        }
                        break;
                    case 'add-account':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-account.php");
                        }
                        break;
                    case 'settings':
                        include("./settings.php");
                        break;
                    case 'new-income':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./new-income.php");
                        }
                        break;
                    case 'show-all-income':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-income.php");
                        }
                        break;
                    case 'show-transactions':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-all-transactions.php");
                        }
                        break;
                    case 'trial-balance-sheet':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./trial-balance-sheet.php");
                        }
                        break;
                    case 'sales-day-report':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./sales-day-report.php");
                        }
                        break;
                    case 'revenue-day-report':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./revenue-day-report.php");
                        }
                        break;
                    case 'add-bank-account':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-bank-account.php");
                        }
                        break;
                    case 'create-quotation':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./create_quotation.php");
                        }
                        break;
                    case 'show-quotations':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-quotations.php");
                        }
                        break;
                    case 'quotation-preview':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./quotation.php");
                        }
                        break;
                    case 'add-bank-statement':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./bank-statement.php");
                        }
                        break;
                    case 'all-bank-statements':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-bank-statements.php");
                        }
                        break;
                    case 'show-bank-statement':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-bank-statement.php");
                        }
                        break;
                    case 'update-bank-account-balance':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./update-bank-account-balance.php");
                        }
                        break;
                    case 'add-opening-balance':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-opening-balance.php");
                        }
                        break;
                    case 'update-inventory-item':
                        if ($_SESSION['privilege_name'] == 'Payroll') {
                            include("./dashboard.php");
                        } else {
                            include("./update-inventory-item.php");
                        }
                        break;
                    case 'balance-sheet':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./balance-sheet.php");
                        }
                        break;
                    case 'income-statement':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./income-statement.php");
                        }
                        break;
                    case 'company-profile':
                        include("./company-profile.php");
                        break;
                    case 'add-depreciation':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-depreciation.php");
                        }
                        break;
                    case 'show-depreciation':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./show-depreciation.php");
                        }
                        break;
                    case 'edit-depreciation':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./edit-depreciation.php");
                        }
                        break;
                    case 'add-sub-account':
                        if ($_SESSION['privilege_name'] != 'All') {
                            include("./dashboard.php");
                        } else {
                            include("./add-sub-account.php");
                        }
                        break;
                    case 'add-user':
                        include('./add-user.php');
                        break;
                    case 'edit-user':
                        include('./edit-user.php');
                        break;
                    case 'show-users':
                        include('./show-users.php');
                        break;
                    case 'show-notifications':
                        include("./show-notifications.php");
                        break;
                    case 'notification':
                        include('./notification-info.php');
                        break;
                    default:
                        include("./dashboard.php");
                }
            } else {
                // if the page attribute is not set in the site url, then load the default page as the company dashboard page into the current page.
                include("./dashboard.php");
            }
            ?>
        </main>
        <!-- MAIN -->
    </section>
    <!-- NAVBAR -->
    <!-- load the js files into the dashboard pages -->
    <script src="../../assets/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- setup the google translate instance for all the dashboard pages -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en'
            }, 'google_translate_element');
        }

        $(document).ready(function() {
            var down = false;

            $('#bell').click(function(e) {
                var color = $(this).text();
                if (down) {
                    $('#box').css('height', '0px');
                    $('#box').css('opacity', '0');
                    down = false;
                } else {
                    $('#box').css('height', 'auto');
                    $('#box').css('opacity', '1');
                    down = true;
                }
            });
        });

        $(document).ready(function() {
            var privilege = "<?php echo $_SESSION['privilege_name']; ?>";

            function handlePrivilege() {
                if (privilege === 'Payroll') {
                    $("#department").hide();
                    $("#supplier").hide();
                    $("#customer").hide();
                    $("#sales").hide();
                    $("#purchase").hide();
                    $("#orders").hide();
                    $("#supplier").hide();
                    $("#transactions").hide();
                    $("#reports").hide();
                    $("#quotations").hide();
                    $("#bank-statements").hide();
                    $("#account").hide();
                    $("#items").hide();
                } else if (privilege === 'Inventory') {
                    $("#department").hide();
                    $("#supplier").hide();
                    $("#customer").show();
                    $("#purchase").hide();
                    $("#orders").hide();
                    $("#transactions").hide();
                    $("#reports").hide();
                    $("#quotations").hide();
                    $("#bank-statements").hide();
                    $("#account").hide();
                    $("#payrolls").hide();
                    $("#employee").hide();
                }
            }

            // Call the function to handle privilege level
            handlePrivilege();
        });

        // prevent go back from the current page if the user/ company admin is logged in.
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.location.href = "./Company.php?page=dashboard"; // Redirect to dashboard if back button is pressed
        };
    </script>

    <script type="text/javascript"
        src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>

</html>