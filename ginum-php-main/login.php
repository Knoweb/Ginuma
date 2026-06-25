<?php
session_start(); // Start the session

// Check if the user is logged in by checking if the session variable is set
if (isset($_SESSION['company_id'])) {
    // User is not logged in, redirect to the login page
    header("Location: ./user_profile.php");
    exit(); // Always use exit after header redirection
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
    <title>Ginum | Login</title>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- ========== End Stylesheet ========== -->

    <style>
        .password-container {
            position: relative;
        }

        .password-container input {
            padding-right: 40px;
        }

        .password-container i {
            position: absolute;
            right: 19px;
            top: 20px;
            bottom: 20px;
            cursor: pointer;
        }

        .small-alert {
            font-size: 14px;
            padding: 10px 15px;
            width: 250px;
            /* Make the alert narrower */
        }
    </style>

</head>

<body class="bg-theme-small">

    <!-- Start Breadcrumb 
    ============================================= -->
    <?php if (isset($_GET['error'])): ?>
        <script>
            alert("<?php echo $_GET['error']; ?>");
        </script>
    <?php endif; ?>
    <div class="login-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 offset-lg-4">
                    <div class="login-box">
                        <div class="login">
                            <div class="content">
                                <a href="./"><img src="assets/img/ginum_logo.png" alt="Logo"></a>
                                <form action="./process_login.php" method="POST">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <input type="email" name="email" id="email" placeholder="Email*"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group password-container">
                                                <input type="password" name="password" id="password"
                                                    placeholder="Password*" class="form-control" required>
                                                <i class="fa fa-eye position-absolute" id="togglePassword"
                                                    style="top: 40%; right: 10px; cursor: pointer"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="submit">
                                                Login
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="sign-up">
                                    <p>
                                        Don't have an account? <a href="./app/register.php">Sign up now</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
    <script>
        // Check if there's a status message in the query parameters
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const type = urlParams.get('type'); // 'error' or 'success'

        if (message) {
            Swal.fire({
                toast: true,
                position: 'top-end', // Display in the top right corner
                icon: type ? type : 'error', // 'error' by default
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
                customClass: {
                    popup: 'small-alert' // Optional: Add custom styling if needed
                }
            });
        }
    </script>
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