<?php
session_start(); // Start the session
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
                        <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="index.php">
                        <img src="assets/img/ginum_logo.png" class="logo" alt="Logo">
                    </a>
                </div>
                <!-- End Header Navigation -->

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-menu">

                    <img src="assets/img/ginum_logo.png" alt="Logo">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                        <i class="fa fa-times"></i>
                    </button>

                    <ul class="nav navbar-nav navbar-center" data-in="fadeInDown" data-out="fadeOutUp">
                        <li>
                            <a href="index.php" href="#home" class="active">Home</a>
                        </li>

                        <li>
                            <a class="smooth-menu" href="#features">Features</a>
                        </li>
                        <li>
                            <a class="smooth-menu" href="#overview">Overview</a>
                        </li>
                        <li>
                            <a class="smooth-menu" href="#pricing">Pricing</a>
                        </li>
                        <li>
                            <a class="smooth-menu" href="#reviews">Reviews</a>
                        </li>
                        <li>
                            <a class="smooth-menu" href="#faq">FAQ</a>
                        </li>
                        <li>
                            <a class="smooth-menu" href="#contact">contact</a>
                        </li>

                    </ul>
                </div><!-- /.navbar-collapse -->

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
                                <a href="./app/register.php">Register</a>
                            </li>
                        <?php endif; ?>

                    </div>
                    <!-- End Atribute Navigation -->

                </div>
                <!-- Overlay screen for menu -->
                <div class="overlay-screen"></div>
                <!-- End Overlay screen for menu -->
        </nav>
        <!-- End Navigation -->
    </header>
    <!-- End Header -->

    <!-- Start Banner 
    ============================================= -->
    <div
        class="banner-area with-carousel bg-gray-responsive overflow-inherit content-double transparent-nav text-large">
        <!-- Fixed Shape -->
        <div class="fixed-shape" style="background-image: url(assets/img/shape/4.png);"></div>
        <!-- Fixed Shape -->
        <div class="box-table">
            <div class="box-cell">
                <div class="container">
                    <div class="double-items">
                        <div class="row align-center">
                            <div class="col-lg-6 left-info simple-video">
                                <div class="content" data-animation="animated fadeInUpBig">
                                    <h1> <span>Ginum</span> <br> Accounting Software </h1>
                                    <p>
                                        Ginum is the all-in-one accounting software designed for businesses of all
                                        sizes. Manage your finances effortlessly and make informed decisions with
                                        real-time insights.
                                    </p>
                                    <div class="button">
                                        <a class="btn circle btn-theme border btn-md" href="./app/register.php">Get
                                            Started</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 right-info width-big">
                                <img src="assets/img/app/app-2.png" alt="Thumb">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wavesshape">
                    <img src="assets/img/waves-shape.svg" alt="Shape">
                </div>
            </div>
        </div>
    </div>
    <!-- End Banner -->

    <!-- Start About 
    ============================================= -->
    <div id="about" class="about-area bg-gray default-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 default info pr-60 pr-md-15 pr-xs-15">
                    <h4>Our Story, Your Success</h4>
                    <h2>Empower Your Business <br>with Smart Accounting</h2>
                    <p>
                        At Ginum, we redefine how businesses manage finances.
                        Built with precision and simplicity, our app empowers you to take control of accounting,
                        from automation to insightful analytics.
                        Whether you're a startup or an enterprise, Ginum grows with your needs,
                        delivering innovation at every step.
                    </p>
                    <div class="bottom-info">
                        <h3>Why Choose Ginum?</h3>
                        <ul>
                            <li>
                                <i class="fas fa-check"></i> <span>Effortless Accounting Excellence</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i> <span>Unmatched Data Security</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i> <span>Time-Saving Automation</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i> <span>Real-Time Financial Insights</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 services-info">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 single-item">
                            <div class="item">

                                <i class="fas fa-file-contract"></i>
                                <h4>Easy Invoicing</h4>
                                <p>
                                    Create, send, and track invoices in just a few clicks. Automate reminders for
                                    overdue payments. </p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 single-item">
                            <div class="item">
                                <i class="fas fa-dollar-sign"></i>


                                <h4>Multi-Currency Support</h4>
                                <p>
                                    Work seamlessly across borders with built-in multi-currency options. </p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 single-item">
                            <div class="item">
                                <i class="fas fa-credit-card"></i>

                                <h4>Tax Compliance</h4>
                                <p>
                                    Ensure accurate tax calculations and generate reports for VAT, GST, and other
                                    compliance needs. </p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 single-item">
                            <div class="item">
                                <i class="fas fa-signal"></i>
                                <h4>Real-Time Financial Reporting</h4>
                                <p>
                                    et actionable insights into your cash flow, expenses, and profits anytime. </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End About -->

    <!-- Start Features 
    ============================================= -->
    <div id="features" class="features-area default-padding bottom-small">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="site-heading text-center">
                        <h2>Our Features</h2>
                        <p>
                            Why Choose Ginum Accounting Software?
                        </p>
                    </div>
                </div>
            </div>
            <div class="features-items">
                <div class="row">
                    <div class="col-lg-4 col-md-6 single-item">
                        <div class="item">
                            <div class="icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="info">
                                <h4>Simplified Invoicing & Billing</h4>
                                <p>
                                    Generate professional invoices in seconds.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 single-item">
                        <div class="item">
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="info">
                                <h4>Expense Tracking</h4>
                                <p>
                                    Stay on top of your cash flow with detailed expense monitoring.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 single-item">
                        <div class="item">
                            <div class="icon">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div class="info">
                                <h4>Tax Management</h4>
                                <p>
                                    Automatic tax calculations and compliance support.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 single-item">
                        <div class="item">
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info">
                                <h4>Real-Time Reporting</h4>
                                <p>
                                    Access financial reports instantly for smarter decisions.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 single-item">
                        <div class="item">
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="info">
                                <h4>Multi-User Access</h4>
                                <p>
                                    Collaborate seamlessly with your team.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 single-item">
                        <div class="item">
                            <div class="icon">
                                <i class="fas fa-cloud"></i>
                            </div>
                            <div class="info">
                                <h4>Cloud Backup & Security</h4>
                                <p>
                                    Your data is safe, accessible anytime, anywhere.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Features -->

    <!-- Start Overview 
    ============================================= -->
    <div id="overview" class="overview-area default-padding bg-gray">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="site-heading text-center">
                        <h2>Ginum at a Glance</h2>
                        <p>
                            Ginum is a user-friendly accounting software that simplifies financial management with
                            features like expense tracking, invoicing, and detailed reporting, helping businesses stay
                            organized and efficient.
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center overview-items">
                    <div class="overview-carousel owl-carousel owl-theme">
                        <img src="assets/img/app/dashboard.png" alt="Thumb">
                        <img src="assets/img/app/payroll.png" alt="Thumb">
                        <img src="assets/img/app/payslip.png" alt="Thumb">
                        <img src="assets/img/app/new_income.png" alt="Thumb">
                        <img src="assets/img/app/new_expense.png" alt="Thumb">
                        <img src="assets/img/app/accounts.png" alt="Thumb">
                        <img src="assets/img/app/add_opening_balance.png" alt="Thumb">
                        <img src="assets/img/app/income_statement.png" alt="Thumb">
                        <img src="assets/img/app/quotation.png" alt="Thumb">
                        <img src="assets/img/app/new_transactions.png" alt="Thumb">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Overview -->

    <style>
        /* Add CSS to left-align text */
        .text-left {
            text-align: left;
        }

        /* Use --color-primary for header prices */
        .price-header {
            color: var(--color-primary);
        }

        @media (max-width: 768px) {
            .pricing-area .card {
                margin-bottom: 20px;
            }

            .pricing-area .card-header {
                font-size: 18px;
                padding: 15px;
            }

            .pricing-area .list-group-item {
                font-size: 14px;
            }

            .pricing-area .btn {
                width: 100%;
            }
        }
    </style>

    <!-- Start Pricing Area
============================================= -->
    <div id="pricing" class="pricing-area default-padding bottom-less">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="site-heading text-center">
                        <h2>Our Packages</h2>
                        <p>Affordable Plans for Every Business.</p>
                    </div>
                </div>
            </div>
            <div class="pricing pricing-simple text-center">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Scrollable Table Container -->
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-striped pricing-area">
                                <thead>
                                    <tr>
                                        <th>
                                            <h4>FEATURES</h4>
                                        </th>
                                        <th>
                                            <h4>STARTER <br><a href="./checkout.php?package=starter"
                                                    class="price-header">15/mo</a></h4>
                                        </th>
                                        <th>
                                            <h4>BASIC <br><a href="./checkout.php?package=basic"
                                                    class="price-header">29/mo</a></h4>
                                        </th>
                                        <th>
                                            <h4>STANDARD <br><a href="./checkout.php?package=standard"
                                                    class="price-header">59/mo</a></h4>
                                        </th>
                                        <th>
                                            <h4>PREMIUM <br><a href="./checkout.php?package=premium"
                                                    class="price-header">99/mo</a></h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Double-entry bookkeeping</td>
                                        <td><i class="fas fa-check "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Profit & Loss (P&L)</td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Trial Balance (TB)</td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Balance Sheet</td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Sales and Expenses Tracking</td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Quotations and Invoices</td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Basic Dashboard</td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Project-wise expenses tracking</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Period-wise P&L, TB, Balance Sheet</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Bank reconciliation</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Asset register</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Employee management</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Payroll management</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Inventory management</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Depreciation tracking</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Period-wise report generation</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Advanced dashboard & reporting tools</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Unlimited projects and employees</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Advanced inventory management</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Advanced sales tracking</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Advanced Tax Calculation</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                    <tr>
                                        <td>Priority customer support</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check  "></i></td>
                                    </tr>
                                </tbody>

                                <tr>
                                    <td></td>
                                    <td class="fw-bold">
                                        <h3>$15/mo </h3>
                                    </td>
                                    <td class="fw-bold">
                                        <h3>$29/mo</h3>
                                    </td>
                                    <td class="fw-bold">
                                        <h3>$59/mo</h3>
                                    </td>
                                    <td class="fw-bold">
                                        <h3>$99/mo</h3>
                                    </td>
                                </tr>
                                <tfoot class="thead-inverse">
                                    <tr>
                                        <th class="w-25"></th>
                                        <th> <a class="btn circle btn-dark border btn-sm"
                                                href="checkout.php?package=starter">Buy
                                                Plan</a></th>
                                        <th><a class="btn circle btn-theme effect btn-sm"
                                                href="checkout.php?package=basic">Buy
                                                Plan</a></th>
                                        <th><a class="btn circle btn-dark border btn-sm"
                                                href="checkout.php?package=standard">Buy Plan</a></th>
                                        <th><a class="btn circle btn-theme effect btn-sm"
                                                href="checkout.php?package=premium">Buy Plan</a></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Pricing Area -->
    </div>


    <!-- Start Testimonials 
    ============================================= -->
    <div id="reviews" class="testimonials-area bg-gray default-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="site-heading single text-center">
                        <h2>Customer Review</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="testimonial-items text-center">
                        <div class="carousel slide" class="carousel slide" data-bs-ride="carousel"
                            id="testimonial-carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <span class="quote"></span>
                                    <p>
                                        Ginum has streamlined our financial processes like never before.
                                        Its intuitive interface and powerful features make managing accounts
                                        effortless.
                                    </p>
                                    <h4>Mark Thompson</h4>
                                    <span>CFO of TechSolutions</span>
                                </div>
                                <div class="carousel-item">
                                    <span class="quote"></span>
                                    <p>
                                        Ginum has completely transformed the way we handle accounting.
                                        It’s easy to use and saves us hours each week!
                                    </p>
                                    <h4>John Doe</h4>
                                    <span>CEO of FinancePro</span>
                                </div>
                                <div class="carousel-item">
                                    <span class="quote"></span>
                                    <p>
                                        The tax automation feature is a game-changer.
                                        Highly recommend it to anyone looking for reliable accounting software.
                                    </p>
                                    <h4>Sara Lee</h4>
                                    <span>Small Business Owner</span>
                                </div>
                            </div>
                            <!-- End Carousel Content -->

                            <!-- Carousel Indicators -->
                            <ol class="carousel-indicators">
                                <li data-bs-target="#testimonial-carousel" data-bs-slide-to="0" class="active"
                                    aria-current="true" aria-label="Slide 1">
                                    <img src="assets/img/reviews/sara.png" alt="Thumb">
                                </li>
                                <li data-bs-target="#testimonial-carousel" data-bs-slide-to="1" aria-label="Slide 2">
                                    <img src="assets/img/reviews/john.jpg" alt="Thumb">
                                </li>
                                <li data-bs-target="#testimonial-carousel" data-bs-slide-to="2" aria-label="Slide 3">
                                    <img src="assets/img/reviews/mark.jpg" alt="Thumb">
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Testimonials -->

    <!-- Start Subscribe 
    ============================================= -->
    <div class="subscribe-area bg-fixed shadow dark text-light default-padding text-center"
        style="background-image: url(assets/img/banner/ginum.png);">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <h3>Ready to Simplify Your Accounting? Start Your Free Trial Today!</h3>

                    <div class="subscribe">
                        <li class="button dark">
                            <a href="#pricing" class="btn circle btn-theme effect btn-sm">Get Started</a>
                        </li>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Subscribe -->


    <!-- Start Faq  
    ============================================= -->
    <div id="faq" class="faq-area bg-gray default-padding-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="site-heading text-center">
                        <h2>FAQ</h2>
                        <p>
                            Navigate the world of accounting effortlessly with our FAQ page,
                            designed to simplify complex concepts and provide clear, actionable answers.
                            From financial statements to tax tips, explore expert insights tailored to your
                            needs.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-lg-6 faq-items default-padding-bottom order-lg-last">
                    <!-- Start Accordion -->
                    <div class="faq-content">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item card">
                                <div class="accordion-header card-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Do I need technical expertise to use Ginum?
                                    </button>
                                </div>

                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="card-body">
                                        <p>
                                            Not at all! Ginum is designed with simplicity in mind, making it
                                            accessible for everyone, regardless of technical skill. The user
                                            interface is intuitive and easy to navigate, allowing you to manage
                                            your accounting tasks effortlessly. Whether you’re new to accounting
                                            or experienced,
                                            Ginum ensures a seamless experience without any technical knowledge
                                            required.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item card">
                                <div class="accordion-header card-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Is my data secure?
                                    </button>
                                </div>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionExample">
                                    <div class="card-body">
                                        <p>
                                            Yes, your data is fully secure with Ginum.
                                            We use 256-bit SSL encryption to protect your information during
                                            transmission,
                                            ensuring that it remains private and safe from unauthorized access.
                                            Additionally, we follow strict GDPR compliance, giving you peace of
                                            mind that your data is handled with the highest level of security
                                            and privacy standards
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item card">
                                <div class="accordion-header card-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        Can I cancel my subscription anytime?
                                    </button>
                                </div>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="collapseThree" data-bs-parent="#accordionExample">
                                    <div class="card-body">
                                        <p>
                                            Absolutely! We believe in providing flexibility, so there are no
                                            lock-in contracts with Ginum.
                                            You can cancel your subscription at any time directly from your
                                            account settings without any penalties.
                                            We aim to make the process as straightforward and hassle-free as
                                            possible.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item card">
                                <div class="accordion-header card-header" id="headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                        Are there any hidden fees with Ginum?
                                    </button>
                                </div>
                                <div id="collapseFour" class="accordion-collapse collapse"
                                    aria-labelledby="collapseFour" data-bs-parent="#accordionExample">
                                    <div class="card-body">
                                        <p>
                                            No, Ginum prides itself on transparency. There are no hidden fees,
                                            and you only pay for the features and subscription plan you choose.
                                            Any additional charges, if applicable, are clearly communicated
                                            upfront, ensuring no surprises on your billing statement.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Accordion -->
                </div>
                <div class="col-lg-6 thumb">
                    <img src="assets/img/banner/contact.png" alt="Thumb">
                </div>
            </div>
        </div>
    </div>
    <!-- End Faq  -->
    <!-- Start Contact Area  
    ============================================= -->
    <div id="contact" class="contact-us-area default-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="site-heading text-center">
                        <h2>Contact Us</h2>
                        <p>
                            Got a question or need help?
                            Our Ginum support team is always ready to assist,
                            offering personalized, expert guidance with a quick response to meet your needs.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 address">
                    <div class="address-items">
                        <h4>Our Office Address</h4>
                        <ul class="info">
                            <li>
                                <i class="fas fa-map-marked-alt"></i>
                                <span>No 421, 4th floor,<br> sanvik plaza, Wakwella Rd, Galle, <br> Sri Lanka</span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <span>+94-74-070-9989</span>
                            </li>
                            <li>
                                <i class="fas fa-envelope-open"></i>
                                <span>info@ginumapps.com</span>
                            </li>
                        </ul>
                        <div class="social-address">
                            <h4>Social Address</h4>
                            <ul class="social">
                                <li class="facebook">
                                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                                </li>
                                <li class="twitter">
                                    <a href="#"><i class="fab fa-linkedin"></i></a>
                                </li>
                                <li class="instagram">
                                    <a href="#"><i class="fab fa-instagram"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 contact-form">
                    <h2>We'd Love to Hear From You!</h2>
                    <form action="assets/mail/contact.php" method="POST" class="contact-form">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <input class="form-control" id="name" name="name" placeholder="Name" type="text">
                                    <span class="alert-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input class="form-control" id="email" name="email" placeholder="Email*"
                                        type="email">
                                    <span class="alert-error"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input class="form-control" id="phone" name="phone" placeholder="Phone" type="text">
                                    <span class="alert-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group comments">
                                    <textarea class="form-control" id="comments" name="comments"
                                        placeholder="Tell Us About your Concern *"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="submit" id="submit">
                                    Send Message <i class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Alert Message -->
                        <div class="col-lg-12 alert-notification">
                            <div id="message" class="alert-msg"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Contact -->

    <!-- Start Footer 
    ============================================= -->
    <footer class="default-padding bg-gray">
        <div class="container">
            <div class="f-items">
                <div class="row">
                    <div class="col-lg-4 col-md-6 item">
                        <div class="f-item">
                            <img src="assets/img/ginum_logo.png" alt="Logo">
                            <p>
                                A seamless accounting experience for businesses of all sizes. Automate your
                                finances, track progress, and achieve your goals faster.
                            </p>
                            <a href="./login.php" class="btn circle btn-theme effect btn-sm">Get Started</a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 item">
                        <div class="f-item link">
                            <h4>Quick LInk</h4>
                            <ul>
                                <li>
                                    <a href="#"> Home</a>
                                </li>
                                <li>
                                    <a href="#about"> About us</a>
                                </li>
                                <li>
                                    <a href="#pricing"> Packages</a>
                                </li>
                                <li>
                                    <a href="#faq"> FAQ</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 item">
                        <div class="f-item link">
                            <h4>Community</h4>
                            <ul>
                                <li>
                                    <a href="./terms.php"> Terms & Conditions</a>
                                </li>
                                <li>
                                    <a href="#reviews"> Reviews</a>
                                </li>
                                <li>
                                    <a target="_blank" href="#"> Compnay</a>
                                </li>
                                <li>
                                    <a href="#overview"> Overview</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 item">
                        <div class="f-item contact-widget">
                            <div class="address">
                                <ul>
                                    <li>
                                        <div class="icon">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div class="info">
                                            <h5>Website:</h5>
                                            <span>www.ginumapps.com</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="info">
                                            <h5>Email:</h5>
                                            <span>info@ginumapps.com</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="info">
                                            <h5>Phone:</h5>
                                            <span>+94-74-070-9989</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Start Footer Bottom -->
            <div class="footer-bottom">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <p>&copy; <?php echo date("Y"); ?> <a href="#">Ginum </a> Accounting Software. All
                                Rights Reserved </p>
                        </div>
                        <div class="col-lg-6 text-end link">
                            <ul>
                                <li>
                                    <a href="./terms.php">Terms & Conditions</a>
                                </li>

                                <li>
                                    <a href="#contact">Support</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Footer Bottom -->
        </div>
    </footer>
    <!-- End Footer -->

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