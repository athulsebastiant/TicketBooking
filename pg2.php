<?php
// Database connection
require('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;

$mysqli = new mysqli("localhost", "root", "", "evm");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}



$api_key = ''; //Your Test Key
$api_secret = ''; //Your Test Secret Key



//$razorpay_key = $razorpay_test_key;

//$authAPIkey = "Basic " . base64_encode($razorpay_test_key . ":" . $razorpay_test_secret_key);


// Set transaction details
$myorder_id = $_GET['order_id'];


$payAmount = $_GET['payAmount'];


$api = new Api($api_key, $api_secret);
// Create an order
$order = $api->order->create([
    "amount" => (int)($payAmount * 100),
    "currency" => "INR",
    "receipt" => $myorder_id,
]);
$order_id = $order->id;

// Set your callback URL
$callback_url = "http://localhost/evm/sp2.php?order_id=" . $myorder_id;

// Include Razorpay Checkout.js library
echo '<script src="https://checkout.razorpay.com/v1/checkout.js"></script>';

// Create a payment button with Checkout.js
//echo '<button onclick="startPayment()">Pay with Razorpay</button>';

// Add a script to handle the payment
echo '<script>
    function startPayment() {
        var options = {
            key: "' . $api_key . '",
            amount: ' . $order->amount . ',
            currency: "' . $order->currency . '",
            name: "Your Company Name",
            description: "Payment for your order",
            image: "https://cdn.razorpay.com/logos/GhRQcyean79PqE_medium.png",
            order_id: "' . $order_id . '",
            theme:
            {
                "color": "#738276"
            },
            callback_url: "' . $callback_url . '"
        };
        var rzp = new Razorpay(options);
        rzp.open();
    }

    window.onload = startPayment;
</script>';
