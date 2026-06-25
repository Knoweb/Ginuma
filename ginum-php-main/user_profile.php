<?php
session_start(); // Start the session

// Check if the user is logged in by checking if the session variable is set
if (!isset($_SESSION['company_id'])) {
    // User is not logged in, redirect to the login page
    header("Location: login.php");
    exit(); // Always use exit after header redirection
}

include_once "./db.php"; // Include database connection

// Fetch company details, including country_name, currency_name, and category_name
$company_id = $_SESSION['company_id'];
$query = "
    SELECT 
        c.company_name, 
        c.email, 
        c.phone_no, 
        c.company_registered_address, 
        c.img_path,
        c.status,
        ct.country_name, 
        cr.currency_name,
        cc.category_name,
        p.package_name
    FROM 
        company_tbl c
    LEFT JOIN 
        country_tbl ct ON c.country_id = ct.country_id
    LEFT JOIN 
        currency_tbl cr ON c.currency_id = cr.currency_id
    LEFT JOIN 
        company_category_tbl cc ON c.company_category_id = cc.company_category_id
    LEFT JOIN 
        packages_tbl p ON c.package_id = p.package_id
    WHERE 
        c.company_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$company_data = $result->fetch_assoc();

if (!$company_data) {
    echo "Error: Company not found.";
    exit();
}
$status = $company_data['status'];
// Determine the status name and badge class based on the status code
switch ($status) {
    case 0:
        $statusName = "Pending";
        $badgeClass = "badge rounded-pill text-bg-info";
        break;
    case 1:
        $statusName = "Active";
        $badgeClass = "badge rounded-pill text-bg-success";
        break;
    case 2:
        $statusName = "Rejected";
        $badgeClass = "badge rounded-pill text-bg-danger";
        break;
    case 3:
        $statusName = "Not Paid";
        $badgeClass = "badge rounded-pill text-bg-warning";
        break;
    default:
        $statusName = "Unknown";
        $badgeClass = "badge rounded-pill text-bg-secondary";
        break;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ========== Meta Tags ========== -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ginum is the all-in-one accounting software designed for businesses of all
                                        sizes. Manage your finances effortlessly and make informed decisions with
                                        real-time insights.">

    <!-- ========== Page Title ========== -->
    <title>Ginum</title>

    <!-- ========== Favicon Icon ========== -->
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

    <!-- ========== Start Stylesheet ========== -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
    <link href="assets/css/elegant-icons.css" rel="stylesheet" />
    <link href="assets/css/flaticon-set.css" rel="stylesheet" />
    <link href="assets/css/magnific-popup.css" rel="stylesheet" />
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet" />
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet" />
    <link href="assets/css/animate.css" rel="stylesheet" />
    <link href="assets/css/helper.css" rel="stylesheet" />
    <link href="assets/css/validnavs.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet">
    <link href="assets/css/responsive.css" rel="stylesheet" />
    <!-- ========== End Stylesheet ========== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


</head>

<body>

    <!-- Start Preloader 
    ============================================= -->
    <div id="preloader">
        <div id="softing-preloader" class="softing-preloader">
            <div class="animation-preloader">
                <div class="spinner"></div>
                <div class="txt-loading">
                    <span data-text-preloader="G" class="letters-loading">
                        G
                    </span>
                    <span data-text-preloader="I" class="letters-loading">
                        I
                    </span>
                    <span data-text-preloader="N" class="letters-loading">
                        N
                    </span>
                    <span data-text-preloader="U" class="letters-loading">
                        U
                    </span>
                    <span data-text-preloader="M" class="letters-loading">
                        M
                    </span>
                </div>
            </div>
            <div class="loader">
                <div class="row">
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Preloader -->
    <!-- Header 
    ============================================= -->
    <header>
        <!-- Start Navigation -->
        <nav class="navbar mobile-sidenav navbar-sticky navbar-default dark validnavs navbar-fixed no-background">

            <div class="container d-flex justify-content-between align-items-center">


                <!-- Start Header Navigation -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">

                    </button>
                    <a class="navbar-brand" href="index.php">
                        <img src="assets/img/ginum_logo.png" class="logo" alt="Logo">
                    </a>
                </div>
                <!-- End Header Navigation -->

                <!-- Collect the nav links, forms, and other content for toggling -->

                <div class="attr-right">
                    <!-- Start Atribute Navigation -->
                    <div class="attr-nav">
                        <?php if (isset($_SESSION['img']) && isset($_SESSION['company_name'])): ?>
                            <!-- Display the restaurant logo and name -->
                            <div class="d-flex align-items-center">
                                <!-- Profile Picture -->
                                <a href="./user_profile.php" class="d-flex align-items-center text-decoration-none">
                                    <img src="./app/assets/imgs/<?= $_SESSION['img'] ? $_SESSION['img'] : '../../../app/assets/imgs/skyscraper.png' ?>"
                                        alt="Company Logo" class="rounded-circle border shadow-sm me-2" width="50"
                                        height="50">
                                    <!-- Restaurant Name -->
                                    <span class="fw-bold text-dark ">
                                        <?php echo htmlspecialchars($_SESSION['company_name']); ?></span>
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Show Register and Login buttons -->
                            <li class="button dark">
                                <a href="./login.php">Login</a>
                            </li>
                            <li class="button">
                                <a href="./register.php">Register</a>
                            </li>
                        <?php endif; ?>

                    </div>
                    <!-- End Atribute Navigation -->

                </div>

            </div>
            <!-- Overlay screen for menu -->
            <div class="overlay-screen"></div>
            <!-- End Overlay screen for menu -->
        </nav>
        <!-- End Navigation -->
    </header>
    <!-- End Header -->

    <!-- Main Content -->
    <div id="about" class="about-area default-padding" style="padding-bottom: 0; margin-bottom: 0;">
        <div class="container" style="padding-bottom: 0; margin-bottom: 0;">
            <div class="row" style="padding-bottom: 0; margin-bottom: 0;">
                <div class="col-lg-8 offset-lg-2" style="padding-bottom: 10px;">
                    <div class="site-heading text-center" style="padding-bottom: 0; margin-bottom: 0;">
                        <h4 style="margin-bottom: 0;">User Profile</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div>
        <section class="bg-white py-5">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <nav aria-label="breadcrumb">

                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card mb-4 shadow-lg border-0 rounded">
                            <div class="card-body text-center p-4 rounded">
                                <img src="./app/assets/imgs/<?= $_SESSION['img'] ? $_SESSION['img'] : '../../../app/assets/imgs/skyscraper.png' ?>"
                                    alt="avatar" class="rounded-circle img-fluid" style="width: 200px;">
                                <h5 class="my-3"><?php echo htmlspecialchars($_SESSION['company_name']); ?></h5>
                                <div class="d-flex justify-content-center gap-2">
                                    <?php if ($status == 1): ?>
                                        <!-- Launch Ginum Button -->
                                        <a href="./app">
                                            <button type="button" class="btn rounded-pill px-4 shadow-sm"
                                                style="background-color: #52c7f3; color: white;"
                                                onmouseover="this.style.backgroundColor='#022b6d'"
                                                onmouseout="this.style.backgroundColor='#52c7f3'">
                                                Launch Ginum
                                            </button>
                                        </a>
                                    <?php else: ?>
                                        <!-- Buy Package Button -->
                                        <a href="./index.php#pricing">
                                            <button type="button" class="btn rounded-pill px-4 shadow-sm"
                                                style="background-color: #d32f2f; color: white;"
                                                onmouseover="this.style.backgroundColor='#9b1d1d'"
                                                onmouseout="this.style.backgroundColor='#d32f2f'">
                                                Buy Package
                                            </button>
                                        </a>
                                    <?php endif; ?>
                                    <a href="logout.php">
                                        <button type="button" class="btn rounded-pill px-4 shadow-sm" style="background-color: white; color: #d32f2f; border: 1px solid #d32f2f; 
            transition: background-color 0.3s, color 0.3s;"
                                            onmouseover="this.style.backgroundColor='#d32f2f'; this.style.color='white'"
                                            onmouseout="this.style.backgroundColor='white'; this.style.color='#d32f2f'">
                                            Logout
                                        </button>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card mb-4 shadow-lg border-0 rounded">
                            <div class="card-body p-4 rounded">
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Company Name</div>
                                    <div class="col-sm-9 text-muted">
                                        <?php echo htmlspecialchars($_SESSION['company_name']); ?></div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Email</div>
                                    <div class="col-sm-9 text-muted"><?php echo htmlspecialchars($_SESSION['email']); ?>
                                    </div>
                                </div>

                                <hr>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Country</div>
                                    <div class="col-sm-9 text-muted">
                                        <?php echo htmlspecialchars($company_data['country_name']); ?></div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Currency</div>
                                    <div class="col-sm-9 text-muted">
                                        <?php echo htmlspecialchars($company_data['currency_name']); ?></div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Package</div>
                                    <div class="col-sm-9 text-muted">
                                        <?php echo htmlspecialchars($company_data['package_name'] ?? 'Not assigned'); ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Payment Status</div>
                                    <div class="col-sm-9 text-muted"><span class="<?= $badgeClass ?>"><?= $statusName ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Company Category</div>
                                    <div class="col-sm-9 text-muted">IT and Technology</div>
                                </div>
                                <hr>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>




    <!-- jQuery Frameworks
    ============================================= -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.appear.js"></script>
    <script src="assets/js/jquery.easing.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/count-to.js"></script>
    <script src="assets/js/validnavs.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>