<?php
// Include the Razorpay PHP library
require('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;

// Initialize Razorpay with your key and secret
$api_key = 'rzp_test_bPVqwKHk97qTjm';
$api_secret = 'X4TOM7igrBCvxJK2FhAitanz';

$api = new Api($api_key, $api_secret);

// Check if payment is successful
$success = true;

$error = null;

// Get the payment ID and the signature from the callback
$payment_id = $_POST['razorpay_payment_id'];
$razorpay_signature = $_POST['razorpay_signature'];

try {
    // Verify the payment
    $attributes = array(
        'razorpay_order_id' => $_POST['razorpay_order_id'],
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature' => $razorpay_signature
    );
    $api->utility->verifyPaymentSignature($attributes);
} catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
    $success = false;
    $error = 'Razorpay Signature Verification Failed';
}

if ($success) {
    // Payment is successful, update your database or perform other actions

    // Fetch the payment details
    $payment = $api->payment->fetch($payment_id);

    // You can access payment details like $payment->amount, $payment->status, etc.
    $amount_paid = $payment->amount / 100; // Convert amount from paise to rupees

    echo "Payment Successful! Amount: $amount_paid INR";
    //put code to update status in db table
    $myorder_id = $_GET['order_id'];
    header("Location: http://localhost/evm/payment-success.php?order_id=" . $myorder_id);
    exit;
} else {
    // Payment failed, handle accordingly
    echo "Payment Failed! Error: $error";
}
