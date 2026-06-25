<?php
session_start();
require './app/vendor/autoload.php'; // Make sure Stripe SDK is loaded
require './db.php'; // Assuming you have a separate file for DB connection

use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

if (!isset($_SESSION['company_id'])) {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

// Set your Stripe secret key
Stripe::setApiKey('sk_test_51Q69E1Q6Fgaw8nLrNPZqDzHMICXKcXIprDGimRjYHNmrwJjRm8txr7lPfyu1a7Y0hSTEM4jNpJQr1zG4ItsZalQg00Ja77fCcZ');

$error_messages = []; // Initialize an array to store error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $package = filter_input(INPUT_POST, 'package', FILTER_SANITIZE_STRING);
    $promo_code = filter_input(INPUT_POST, 'promo_code', FILTER_SANITIZE_STRING);

    $packageDetails = [
        'starter' => ['price' => 15, 'id' => 1],
        'basic' => ['price' => 25, 'id' => 2],
        'standard' => ['price' => 59, 'id' => 3],
        'premium' => ['price' => 99, 'id' => 4],
    ];

    if (isset($packageDetails[$package])) {
        $packagePrice = $packageDetails[$package]['price'];
        $package_id = $packageDetails[$package]['id'];

        // Check for promo code
        if ($promo_code) {
            // Check usage count for "save8"
            if (strtolower($promo_code) === "save8") {
                $usage_stmt = $conn->prepare("SELECT used_count FROM promo_code_usage_tbl WHERE company_id = ? AND promo_code = ?");
                $usage_stmt->bind_param('is', $company_id, $promo_code);
                $usage_stmt->execute();
                $usage_result = $usage_stmt->get_result();
                $usage_data = $usage_result->fetch_assoc();

                // If the promo code has been used 12 or more times, prevent its usage
                if ($usage_data && $usage_data['used_count'] >= 12) {
                    $error_messages[] = 'Promo code has reached its usage limit for your company.';
                }
            }

            $stmt = $conn->prepare("SELECT discount_percent, valid_until FROM promo_codes_tbl WHERE promo_code = ? LIMIT 1");
            $stmt->bind_param('s', $promo_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $promo = $result->fetch_assoc();

                // Validate promo code expiry
                if (strtotime($promo['valid_until']) >= time()) {
                    $discount = $promo['discount_percent'];
                    $packagePrice = $packagePrice - ($packagePrice * $discount / 100);
                    // Save promo code in session
                    $_SESSION['promo_code'] = $promo_code;
                } else {
                    $error_messages[] = 'Promo code has expired.';
                }
            } else {
                $error_messages[] = 'Invalid promo code.';
            }
        }

        if (!empty($error_messages)) {
            // Redirect to payment failed page with all errors
            header("Location: payment_failed.php?" . http_build_query(['error_message' => $error_messages]));
            exit();
        }

        $packagePriceInCents = $packagePrice * 100;

        try {
            $_SESSION['package_id'] = $package_id;

            $session = CheckoutSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => ucfirst($package) . ' Package',
                        ],
                        'unit_amount' => $packagePriceInCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'https://ginumapps.com/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'https://ginumapps.com/payment_failed.php',
            ]);

            header("Location: " . $session->url);
            exit();
        } catch (Exception $e) {
            error_log('Stripe error: ' . $e->getMessage());
            $error_messages[] = 'An error occurred during payment. Please try again.';
            header("Location: payment_failed.php?" . http_build_query(['error_message' => $error_messages]));
            exit();
        }
    } else {
        $error_messages[] = 'Invalid package selection.';
        header("Location: payment_failed.php?" . http_build_query(['error_message' => $error_messages]));
        exit();
    }
}
