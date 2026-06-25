<?php
session_start(); // Start the session

// Check if the user is logged in by checking if the session variable is set
if (isset($_SESSION['privilege_name'])) {
    header("Location: ./pages/Company/Company.php");
    exit(); // Always use exit after header redirection
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Ginum</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap CSS -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <!-- FONT AWESOME ICONS -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />

    <link rel="stylesheet" type="text/css" href="assets/css/iofrm-style.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/iofrm-theme9.css" />

    <!-- favicon -->
    <link rel="shortcut icon" href="./assets/imgs/logos/ginum-logo.png" type="image/x-icon">

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
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['error'])) { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "<?= $_GET['error'] ?>"
            })
        </script>
    <?php } ?>
    <?php if (isset($_GET['success'])) { ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Okay...',
                text: "<?= $_GET['success'] ?>"
            })
        </script>
    <?php } ?>
    <?php if (isset($_GET['info'])) { ?>
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Alert',
                text: "<?= $_GET['info'] ?>"
            })
        </script>
    <?php } ?>
    <div class="form-body">
        <div class="iofrm-layout">
            <div class="img-holder">
                <div class="bg"></div>
                <div class="info-holder">
                    <img src="assets/images/graphic5.svg" alt="" />
                </div>
            </div>
            <div class="form-holder">
                <div class="form-content">
                    <div class="form-items with-bg">
                        <div class="website-logo-inside logo-normal">
                            <a href="../">
                                <div class="logo">
                                    <img
                                        class="logo-size"
                                        src="./assets/imgs/logos/ginum-logo.png"
                                        alt="ginum logo" />
                                </div>
                            </a>
                        </div>
                        <h3 class="font-md">Welcome to Ginum</h3>
                        <p>
                            where accounting meets simplicity. Log in to effortlessly manage
                            your finances and stay in control with secure, streamlined tools
                            designed just for you.
                        </p>
                        <form method="post" action="./backend/data/login-data.php"
                            enctype="multipart/form-data">
                            <input
                                class="form-control"
                                type="email" name="email"
                                placeholder="E-mail Address"
                                required />
                            <div class="password-container">
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    id="password"
                                    placeholder="Password"
                                    required />
                                <i class="far fa-eye" id="togglePassword"></i>
                            </div>
                            <div class="form-button d-flex">
                                <button id="submit" type="submit" class="btn btn-primary">
                                    Login
                                </button>
                                <a href="./register.php" class="btn btn-outline-primary">Create account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // password eye toggle
        const passwordInput = document.getElementById("password");
        const toggleButton = document.getElementById("togglePassword");

        toggleButton.addEventListener("click", function() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleButton.classList.remove("far", "fa-eye");
                toggleButton.classList.add("fas", "fa-eye-slash");
            } else {
                passwordInput.type = "password";
                toggleButton.classList.remove("fas", "fa-eye-slash");
                toggleButton.classList.add("far", "fa-eye");
            }
        });
    </script>
</body>

</html>