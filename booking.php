<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "evm");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch event details
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$query = "SELECT * FROM events WHERE event_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die("Event not found");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $mysqli->real_escape_string($_POST['name']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $mobile = $mysqli->real_escape_string($_POST['mobile']);
    $num_tickets = intval($_POST['num_tickets']);
    $total_price = $num_tickets * $event['price'];

    // Insert booking
    $insert_booking = "INSERT INTO bookings (event_id, num_tickets, total_price, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($insert_booking);
    $stmt->bind_param("iid", $event_id, $num_tickets, $total_price);
    $stmt->execute();
    $booking_id = $stmt->insert_id;

    // Insert payment
    function generateOrderId()
    {
        $timestamp = time();
        $random = mt_rand(0, 999);
        $order_id = 'OR' . substr($timestamp, -5) . str_pad($random, 3, '0', STR_PAD_LEFT);
        return $order_id;
    }

    $order_id = generateOrderId();
    $insert_payment = "INSERT INTO payment (name, email, mobile_number, payment_amount, order_id, order_status,booking_id) VALUES (?, ?, ?, ?, ?, 'pending',?)";
    $stmt = $mysqli->prepare($insert_payment);
    $stmt->bind_param("sssdsi", $name, $email, $mobile, $total_price, $order_id, $booking_id);
    $stmt->execute();

    // Redirect to payment gateway (you'll need to implement this)
    header("Location: payment_gateway.php?order_id=" . $order_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Event: <?php echo htmlspecialchars($event['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f0f8ff;
            /* Light blue background */
        }

        .event-details,
        .booking-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #4285f4;
            /* Blue for heading */
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #666;
            /* Darker grey for labels */
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #e6e6e6;
            /* Light grey border */
            border-radius: 4px;
            color: #333;
            /* Darker text color */
        }

        button {
            background-color: #ff6384;
            /* Pink for button */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            width: 100%;
        }

        button:hover {
            background-color: #ff4d6a;
            /* Darker pink on hover */
        }
    </style>
</head>

<body>
    <div class="event-details">
        <h1>Book Event: <?php echo htmlspecialchars($event['title']); ?></h1>
        <p><?php echo htmlspecialchars($event['description']); ?></p>
        <p>Date: <?php echo htmlspecialchars($event['date']); ?></p>
        <p>Time: <?php echo htmlspecialchars($event['time']); ?></p>
        <p>Location: <?php echo htmlspecialchars($event['location']); ?></p>
        <p>Price: $<?php echo htmlspecialchars($event['price']); ?></p>
        <p>Available Tickets: <?php echo htmlspecialchars($event['available_tickets']); ?></p>
    </div>

    <div class="booking-form">
        <h2>Booking Details</h2>
        <form id="bookingForm" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile Number:</label>
                <input type="tel" id="mobile" name="mobile" required>
            </div>
            <div class="form-group">
                <label for="num_tickets">Number of Tickets:</label>
                <input type="number" id="num_tickets" name="num_tickets" min="1" max="<?php echo $event['available_tickets']; ?>" required>
            </div>
            <div class="form-group">
                <p>Total Price: $<span id="totalPrice">0</span></p>
            </div>
            <button type="submit">Book Now</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('bookingForm');
        const numTicketsInput = document.getElementById('num_tickets');
        const totalPriceSpan = document.getElementById('totalPrice');
        const ticketPrice = <?php echo $event['price']; ?>;

        numTicketsInput.addEventListener('input', updateTotalPrice);

        function updateTotalPrice() {
            const numTickets = parseInt(numTicketsInput.value) || 0;
            const totalPrice = numTickets * ticketPrice;
            totalPriceSpan.textContent = totalPrice.toFixed(2);
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to book these tickets?')) {
                this.submit();
            }
        });
    </script>
</body>

</html>

<?php
$mysqli->close();
?>