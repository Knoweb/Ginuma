<?php
// Start session
session_start();

include './db.php'; // Database connection

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($email) || empty($password)) {
        // Redirect back to login page with error message
        header('Location: login.php?error=Please+enter+both+email+and+password');
        exit;
    }

    // Sanitize input
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT company_id, company_name, password, img_path, country_id FROM company_tbl WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($company_id, $company_name, $hashed_password, $img_path, $country_id);

        if ($stmt->num_rows > 0) {
            // Fetch the result
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Store data in session
                $_SESSION['company_id'] = $company_id;
                $_SESSION['company_name'] = $company_name;
                $_SESSION['img'] = $img_path;
                $_SESSION['email'] = $email;
                $_SESSION['country_id'] = $country_id;

                // Regenerate session ID for security (prevents session fixation)
                session_regenerate_id(true);

                // Redirect to profile page
                header('Location: user_profile.php');
                exit;
            } else {
                // Redirect back to login page with error message
                header("Location: login.php?message=Invalid login credentials&type=error");
                exit;
            }
        } else {
            // Redirect back to login page with error message (invalid email or password)
            header("Location: login.php?message=Invalid login credentials&type=error");
            exit;
        }
    } else {
        // Handle SQL prepare error
        die("Database query failed: " . $conn->error);
    }
}

$conn->close();
