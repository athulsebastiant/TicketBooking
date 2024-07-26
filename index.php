<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "evm");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch events
$query = "SELECT * FROM events WHERE date >= CURDATE() ORDER BY date ASC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Booking Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .event-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .event-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: box-shadow 0.3s ease;
        }

        .event-card:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .event-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <h1>Upcoming Events</h1>
    <div class="event-list">
        <?php while ($event = $result->fetch_assoc()) : ?>
            <div class="event-card" onclick="bookEvent(<?php echo $event['event_id']; ?>)">
                <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                <h2><?php echo htmlspecialchars($event['title']); ?></h2>
                <p><?php echo htmlspecialchars($event['description']); ?></p>
                <p>Date: <?php echo htmlspecialchars($event['date']); ?></p>
                <p>Time: <?php echo htmlspecialchars($event['time']); ?></p>
                <p>Location: <?php echo htmlspecialchars($event['location']); ?></p>
                <p>Price: $<?php echo htmlspecialchars($event['price']); ?></p>
                <p>Available Tickets: <?php echo htmlspecialchars($event['available_tickets']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>

    <script>
        function bookEvent(eventId) {
            window.location.href = 'booking.php?event_id=' + eventId;
        }
    </script>
</body>

</html>

<?php
$mysqli->close();
?>