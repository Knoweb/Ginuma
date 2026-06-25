<?php
session_start();
require_once '../../backend/connection/conn.php';
$db = new DBConnection();

$conn = $db->conn;

if (!($_SESSION['s_admin_username']) || !$_SESSION['role'] == 'Admin') {
    header("Location: ../../login.php");
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/imgs/logos/ginum-logo.png">

    <title>Super Admin | Ginum</title>
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand"> <i class='bx bxs-smile icon'></i> Ginum</a>
        <ul class="side-menu">
            <li>
                <a href="./Admin.php?page=dashboard" class="active" id="dashboard"><i class='bx bxs-dashboard icon'></i>
                    <b>Dashboard</b>
                </a>
            </li>
            <li class="divider" data-text="Admin Pages">
                <b>Admin Pages</b>
            </li>
            <li>
                <a href="#" id="employee"><i class='bx bx-buildings icon'></i>
                    <b>Company</b> <i class='bx bx-chevron-right icon-right'></i>
                </a>
                <ul class="side-dropdown">
                    <li><a href="./Admin.php?page=registered-companies">Registered Companies</a></li>
                    <li><a href="./Admin.php?page=all-companies">All Companies</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="employee"><i class='bx bx-list-check icon'></i>
                    <b>Requests</b> <i class='bx bx-chevron-right icon-right'></i>
                </a>
                <ul class="side-dropdown">
                    <li><a href="./Admin.php?page=requested-companies">Requested Companies</a></li>
                </ul>
            </li>

            <li class="divider" data-text="Additional">Additional</li>
            <li><a href="./Admin.php?page=settings"><i class="fa-solid fa-gear icon"></i><b>Settings</b></a></li>
            <li><a href="../logout.php?msg=Logged Out Successfully"><i
                        class="fa-solid fa-right-from-bracket icon"></i><b>Logout</b></a></li>
        </ul>
    </section>
    <!-- SIDEBAR -->
    <!-- NAVBAR -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu toggle-sidebar'></i>
            <span class="divider"></span>
        </nav>
        <!-- NAVBAR -->
        <!-- MAIN -->
        <main>
            <!-- Page content -->
            <?php
            if (isset($_GET['page'])) {
                $page = $_GET['page'];

                // routings to the pages
                switch ($page) {
                    case 'dashboard':
                        include("./dashboard.php");
                        break;
                    case 'registered-companies':
                        include("./registered-companies.php");
                        break;
                    case 'all-companies':
                        include("./all-companies.php");
                        break;
                    case 'requested-companies':
                        include("./requested-companies.php");
                        break;
                    case 'settings':
                        include("./settings.php");
                        break;
                    case 'profile':
                        include("./profile.php");
                        break;
                    default:
                        include("./dashboard.php");
                }
            } else {
                include("./dashboard.php");
            }
            ?>
        </main>
        <!-- MAIN -->
    </section>
    <!-- NAVBAR -->

    <script>
        // prevent go back from the current page if the super admin is logged in.
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.location.href = "./Admin.php?page=dashboard"; // Redirect to dashboard if back button is pressed
        };
    </script>

    <script src="../../assets/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Google Fonts -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    </style>
</body>

</html>