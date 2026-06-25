<?php
session_start(); // Start the session

// Check if the user is logged in by checking if the session variable is set
if (!isset($_SESSION['company_id'])) {
    // User is not logged in, redirect to the login page
    header("Location: login.php");
    exit(); // 
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ========== End Stylesheet ========== -->
</head>

<body>
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


                <div class="attr-right">
                    <!-- Start Atribute Navigation -->
                    <div class="attr-nav">
                        <?php if (isset($_SESSION['img']) && isset($_SESSION['company_name'])): ?>
                            <!-- Display the company logo and name -->
                            <div class="d-flex align-items-center">
                                <!-- Profile Picture -->
                                <a href="./user_profile.php" class="d-flex align-items-center text-decoration-none">
                                    <img src="./app/assets/imgs/<?= $_SESSION['img'] ? $_SESSION['img'] : '../../../app/assets/imgs/skyscraper.png' ?>"
                                        alt="Company Logo" class="rounded-circle border shadow-sm me-2" width="50"
                                        height="50">
                                    <!-- company Name -->
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


    <!-- Start  
    ============================================= -->
    <div class="default-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="site-heading text-center">
                        <h4>Choose Your Plan</h4>
                        <h2>Checkout and Get Started!</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 default info pr-60 pr-md-15 pr-xs-15">
                    <h3>Package</h3>
                    <form id="checkout-form" class="contact-form-two py-45 wow fadeInRight delay-0-2s"
                        action="process_checkout.php" method="post">
                        <select id="package" name="package" class="form-control" onchange="updatePackageDetails()"
                            required
                            style="width: 100%; padding: 8px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="" disabled selected>Select Package</option>
                            <option value="starter" data-price="15">Starter Plan</option>
                            <option value="basic" data-price="29">Basic Plan</option>
                            <option value="standard" data-price="59">Standard Plan</option>
                            <option value="premium" data-price="99">Premium Plan</option>
                        </select>
                        <h3>Enter Promo Code</h3>
                        <div class="form-group">
                            <div class="input-group" style=" gap: 10px;">
                                <input type="text" name="promo_code" id="promo_code" placeholder="Enter promo code"
                                    class="form-control">
                                <button type="button" id="apply_promo" class="btn circle btn-theme effect btn-md">Apply
                                    Promo</button>
                            </div>
                            <div id="promo_message" style="margin-top: 10px;"></div>
                            <!-- This is where the promo code message will be shown -->
                        </div>
                        <button type="submit" class="btn circle btn-theme effect btn-md">Complete Purchase</button>

                    </form>
                </div>
                <div class="col-lg-6 services-info py-45">
                    <div class="checkout-summary">
                        <div class="summary-header">
                            <h3>Order Summary</h3>
                        </div>
                        <div class="summary-details">
                            <p>
                                Selected Package: <span id="selected-package">None</span>
                            </p>
                            <p>Package Price: $<span id="package-price">0.00</span></p>
                            <p>
                                Discounts / Promotions: -($<span id="discount-price">0.00</span>)
                            </p>
                            <hr />

                        </div>
                        <div class="summary-footer">
                            <h4>Total: $<span id="total">0.00</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Function to update the total price
            function updateTotalPrice(discountPercent = 0) {
                const packageSelect = $("#package");
                const selectedOption = packageSelect.find(":selected");
                const packagePrice = parseFloat(selectedOption.data("price")) || 0; // Package price
                const discountAmount = (packagePrice * discountPercent / 100).toFixed(2);
                const discountedPrice = (packagePrice - discountAmount).toFixed(2);

                // Update UI with price, discount, and total
                $("#selected-package").text(selectedOption.text());
                $("#package-price").text(packagePrice.toFixed(2));
                $("#discount-price").text(discountAmount); // Display discount
                $("#total").text(`${discountedPrice}`);
            }

            // Validate promo code
            function validatePromoCode(promoCode) {
                $.ajax({
                    url: "validate_promo_code.php",
                    type: "POST",
                    data: {
                        promo_code: promoCode
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            $("#promo_message").text(response.message).css("color", "green");
                            const discountPercent = response.discount || 0;
                            updateTotalPrice(discountPercent);
                        } else {
                            $("#promo_message").text(response.message).css("color", "red");
                            updateTotalPrice(); // Reset to original price
                        }
                    },
                    error: function() {
                        $("#promo_message").text("Error validating promo code.").css("color", "red");
                    }
                });
            }

            // Pre-fill promo code and package from URL
            const urlParams = new URLSearchParams(window.location.search);
            const promoCode = urlParams.get("promo");
            const selectedPackage = urlParams.get("package");

            if (selectedPackage) {
                $("#package").val(selectedPackage);
                updateTotalPrice(); // Update price based on selected package
            }

            if (promoCode) {
                $("#promo_code").val(promoCode);
                validatePromoCode(promoCode); // Apply discount based on promo code
            }

            // Apply promo code when button is clicked
            $("#apply_promo").click(function() {
                const promoCode = $("#promo_code").val().trim();
                if (promoCode) {
                    validatePromoCode(promoCode);
                } else {
                    $("#promo_message").text("Please enter a promo code.").css("color", "red");
                }
            });

            // Update total price when package changes
            $("#package").change(function() {
                const promoCode = $("#promo_code").val().trim();
                if (promoCode) {
                    validatePromoCode(promoCode); // Apply promo code if entered
                } else {
                    updateTotalPrice(); // No promo code applied, just update price
                }
            });
        });
    </script>

</body>

</html>