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

    <!--[if lte IE 9]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->


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
                <!-- Main Nav -->

            </div>
            <!-- Overlay screen for menu -->
            <div class="overlay-screen"></div>
            <!-- End Overlay screen for menu -->
        </nav>
        <!-- End Navigation -->
    </header>
    <!-- End Header -->

    <!-- Main Content -->
    <div id="about" class="about-area default-padding" style="padding-bottom: 0;">
        <div class="container" style="padding-bottom: 0;">
            <div class="row" style="padding-bottom: 0;">
                <div class="col-lg-8 offset-lg-2">
                    <div class="site-heading text-center">
                        <h2>Terms and Conditions</h2>
                        <p>Welcome to Ginum Accounting Software.
                            By using our software, you agree to comply with and be bound
                            by the following terms and conditions of use. Please review these terms carefully before
                            using our services.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <main>
        <div class="container">

            <h3>1. Acceptance of Terms</h3>
            <p>By accessing or using Ginum, you agree to be bound by these terms. If you do not agree to these terms,
                you may not access or use our software.</p>

            <h3>2. License to Use</h3>
            <p>We grant you a non-exclusive, non-transferable license to use Ginum in accordance with these terms.
                Unauthorized use of our software is prohibited.</p>

            <h3>3. User Responsibilities</h3>
            <ul>
                <li>You agree to use Ginum solely for lawful purposes.</li>
                <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                <li>You must not attempt to access our software through unauthorized means.</li>
            </ul>

            <h3>4. Intellectual Property</h3>
            <p>All content, trademarks, and data on Ginum, including but not limited to text, software, graphics, and
                logos, are the property of Ginum or its licensors. Unauthorized use of any material is prohibited.</p>

            <h3>5. Limitation of Liability</h3>
            <p>Ginum is provided on an "as-is" basis. We make no guarantees as to the accuracy or reliability of our
                software. To the extent permitted by law, we shall not be liable for any damages arising from your use
                of our software.</p>

            <h3>6. Modifications</h3>
            <p>We reserve the right to update or modify these terms at any time. Continued use of Ginum after changes
                are posted constitutes acceptance of the revised terms.</p>

            <h3>7. Termination</h3>
            <p>We reserve the right to terminate or suspend your access to Ginum for any reason, including violation of
                these terms.</p>

            <h3>8. Governing Law</h3>
            <p>These terms shall be governed by and construed in accordance with the laws of the jurisdiction in which
                Ginum operates.</p>

            <h3>9. Contact Us</h3>
            <p>If you have any questions about these terms, please contact us at <a
                    href="mailto:info@ginumapps.com">info@ginumapps.com</a>.</p>

            <!-- Add padding only after this specific section -->
            <div style="padding-bottom: 50px;"></div>
        </div>
    </main>



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
                            <a href="#" class="btn circle btn-theme effect btn-sm">Get Started</a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 item">
                        <div class="f-item link">
                            <h4>Quick LInk</h4>
                            <ul>
                                <li>
                                    <a href="./index.php"> Home</a>
                                </li>
                                <li>
                                    <a href="./index.php#about"> About us</a>
                                </li>
                                <li>
                                    <a href="./index.php#pricing"> Packages</a>
                                </li>
                                <li>
                                    <a href="./index.php#faq"> FAQ</a>
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
                                    <a href="./index.php#reviews"> Reviews</a>
                                </li>
                                <li>
                                    <a target="_blank" href="#"> Compnay</a>
                                </li>
                                <li>
                                    <a href="./index.php#overview"> Overview</a>
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
                                    <a href="./index.php#contact">Support</a>
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