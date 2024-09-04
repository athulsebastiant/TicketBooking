<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "evm");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = "SELECT name, email, mobile_number, payment_amount, order_id FROM payment WHERE order_status = 'pending'";

$result = $mysqli->query($sql);

// Check if data is fetched successfully
if ($result->num_rows > 0) {
    $data = array();

    // Loop through each row and add data to an array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Encode data as JSON
    $json_data = json_encode($data);

    // Send JSON data to JavaScript
    echo "<script>var ordersData = " . $json_data . ";</script>";
} else {
    echo "No pending payments found in the database.";
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <h1>Payment Gateway</h1>

    <script>
        $(document).ready(function() {
            // Check if the ordersData variable exists
            if (typeof ordersData !== 'undefined' && ordersData.length > 0) {
                // Use the first order in the array (you might want to modify this based on your requirements)
                var order = ordersData[0];
                //https://your-logo-url.com/logo.png
                // Extract order information
                var billing_name = order.name;
                var billing_email = order.email;
                var billing_mobile = order.mobile_number;
                var paymentAmount = order.payment_amount;
                var orderId = order.order_id;

                var shipping_mobile = order.mobile_number;
                var shipping_email = order.email;
                var paymentOption = "netbanking";
                var shipping_name = order.name;

                var request_url = "submitpayment.php";
                var formData = {
                    billing_name: billing_name,
                    billing_mobile: billing_mobile,
                    billing_email: billing_email,
                    shipping_name: shipping_name,
                    shipping_mobile: shipping_mobile,
                    shipping_email: shipping_email,
                    paymentOption: paymentOption,
                    payAmount: paymentAmount,
                    order_id: orderId,
                    action: 'payOrder',
                }

                $.ajax({
                    type: 'POST',
                    url: request_url,
                    data: formData,
                    dataType: 'json',
                    encode: true,
                }).done(function(data) {
                    if (data.res == 'success') {
                        var orderID = data.order_number;
                        var options = {
                            "key": data.razorpay_key,
                            "amount": data.userData.amount,

                            "currency": "INR",
                            "name": "Seb Industries",
                            "description": data.userData.description,
                            "image": "https://www.whoswho.fr/usr/l/H/L/logo_seb.JPG",
                            "order_id": data.userData.rpay_order_id,
                            "handler": function(response) {
                                window.location.replace("payment-success.php?order_id=" + orderID + "&rp_payment_id=" + response.razorpay_payment_id + "&rp_signature=" + response.razorpay_signature);
                            },
                            "modal": {
                                "ondismiss": function() {
                                    window.location.replace("payment-success.php?order_id=" + orderID);
                                }
                            },
                            "prefill": {
                                "name": data.userData.name,
                                "email": data.userData.email,
                                "contact": data.userData.mobile
                            },
                            "notes": {
                                "address": "Your Business Address"
                            },
                            "theme": {
                                "color": "#3399cc"
                            }
                        };
                        console.log("Amount received in frontend:", data.userData.amount);
                        var rzp1 = new Razorpay(options);
                        rzp1.on('payment.failed', function(response) {
                            window.location.replace("payment-failed.php?order_id=" + orderID + "&reason=" + response.error.description + "&paymentid=" + response.error.metadata.payment_id);
                        });
                        rzp1.open();
                    }
                });
            } else {
                console.error("No pending payments found or ordersData variable not loaded correctly.");
            }
        });
    </script>
</body>

</html>