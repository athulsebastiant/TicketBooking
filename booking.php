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
    $order_id = uniqid('ORDER');
    $insert_payment = "INSERT INTO payment (name, email, mobile_number, payment_amount, order_id, order_status) VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $mysqli->prepare($insert_payment);
    $stmt->bind_param("sssds", $name, $email, $mobile, $total_price, $order_id);
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
        }

        .event-details,
        .booking-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
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