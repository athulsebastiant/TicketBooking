<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Event Booking</title>
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

        .content {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: 2px solid #e6e6e6;
            /* Light grey border */
        }

        p {
            color: #666;
            /* Darker text color */
            margin: 15px 0;
        }

        .contact-info {
            margin-top: 20px;
        }

        .contact-info p {
            font-size: 16px;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .navbar a.active {
            background-color: #4285f4;
            color: white;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php" class="active">Contact</a>
    </div>

    <h1>Contact Us</h1>
    <div class="content">
        <p>We would love to hear from you! If you have any questions, feedback, or concerns, please don't hesitate to reach out to us using the contact information below.</p>

        <div class="contact-info">
            <p><strong>Email:</strong> <a href="mailto:athulsebastiant@gmail.com">athulsebastiant@gmail.com</a></p>
            <p><strong>Phone:</strong> +91-892xxxxxxx</p>
            <p><strong>Address:</strong> Idukki, Kerala, India</p>
        </div>
    </div>
</body>

</html>