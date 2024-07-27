<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "evm");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['order_id'])) {
    $order_id = $mysqli->real_escape_string($_GET['order_id']);

    // Update payment status
    $update_payment = "UPDATE payment SET order_status = 'success' WHERE order_id = ?";
    $stmt = $mysqli->prepare($update_payment);
    $stmt->bind_param("s", $order_id);

    if ($stmt->execute()) {
        echo "<h2>Payment Successful!</h2>";
        echo "<p>Order ID: " . htmlspecialchars($order_id) . "</p>";

        // Fetch payment details
        $fetch_payment = "SELECT name, email, mobile_number, payment_amount FROM payment WHERE order_id = ?";
        $stmt = $mysqli->prepare($fetch_payment);
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($payment = $result->fetch_assoc()) {
            echo "<p>Name: " . htmlspecialchars($payment['name']) . "</p>";
            echo "<p>Email: " . htmlspecialchars($payment['email']) . "</p>";
            echo "<p>Mobile: " . htmlspecialchars($payment['mobile_number']) . "</p>";
            echo "<p>Amount Paid: $" . htmlspecialchars($payment['payment_amount']) . "</p>";
        }

        // Update available tickets in events table
        // First, get the booking_id from the payment table
        $get_booking_id = "SELECT booking_id FROM payment WHERE order_id = ?";
        $stmt = $mysqli->prepare($get_booking_id);
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($payment_row = $result->fetch_assoc()) {
            $payment_id = $payment_row['booking_id'];

            // Now update the tickets
            $update_tickets = "UPDATE events e
                       JOIN bookings b ON e.event_id = b.event_id
                       SET e.available_tickets = e.available_tickets - b.num_tickets
                       WHERE b.booking_id = ?";
            $stmt = $mysqli->prepare($update_tickets);
            $stmt->bind_param("i", $payment_id);

            if ($stmt->execute()) {
                echo "<p>Available tickets have been updated.</p>";
            } else {
                echo "<p>Error updating available tickets: " . $mysqli->error . "</p>";
            }
        } else {
            echo "<p>Error: Payment record not found.</p>";
        }

        echo "<p>Your tickets have been reserved. Thank you for your purchase!</p>";
    } else {
        echo "<h2>Error Updating Payment Status</h2>";
        echo "<p>There was an error updating your payment status. Please contact support.</p>";
    }

    if (isset($_GET['rp_payment_id'])) {
        echo "<p>Razorpay Payment ID: " . htmlspecialchars($_GET['rp_payment_id']) . "</p>";
    }

    if (isset($_GET['rp_signature'])) {
        echo "<p>Razorpay Signature: " . htmlspecialchars($_GET['rp_signature']) . "</p>";
    }
} else {
    echo "<h2>Error</h2>";
    echo "<p>Missing order ID parameter.</p>";
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f0f8ff;
            /* Light blue background */
            text-align: center;
        }

        h2 {
            color: #4CAF50;
            /* Success green */
            margin-bottom: 20px;
        }

        p {
            color: #666;
            /* Darker text color */
            margin-bottom: 10px;
        }

        a {
            color: #4285f4;
            /* Blue for links */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <a href="generate_ticket.php?order_id=<?php echo htmlspecialchars($order_id); ?>">Download Ticket</a> |
    <a href="index.php">Back to Home</a>
</body>

</html>