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
             background-color: #f0f8ff;
             /* Light blue background */
         }

         h1 {
             text-align: center;
             color: #333;
             margin-bottom: 30px;
         }

         .event-list {
             display: grid;
             grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
             gap: 30px;
             justify-content: center;
         }

         .event-card {
             background-color: #fff;
             box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
             border-radius: 8px;
             padding: 20px;
             text-align: center;
             border: 2px solid #e6e6e6;
             /* Light grey border */
         }

         .event-image {
             width: 100%;
             height: 200px;
             object-fit: cover;
             border-radius: 8px;
             margin-bottom: 15px;
         }

         h2 {
             font-size: 18px;
             color: #4285f4;
             /* Blue for headings */
             margin-bottom: 10px;
         }

         p {
             color: #666;
             /* Darker text color */
             margin: 5px 0;
         }

         .event-card:hover {
             box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
             transform: translateY(-2px);
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