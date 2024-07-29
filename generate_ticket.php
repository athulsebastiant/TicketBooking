<?php

ob_start();
require('fpdf/fpdf.php');

// Database connection
$mysqli = new mysqli("localhost", "root", "", "evm");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['order_id'])) {
    $order_id = $mysqli->real_escape_string($_GET['order_id']);

    // Fetch payment and booking details
    $query = "SELECT p.name, p.email, p.mobile_number, p.payment_amount, b.num_tickets, e.title, e.date, e.time, e.location 
              FROM payment p 
              JOIN bookings b ON p.booking_id = b.booking_id 
              JOIN events e ON b.event_id = e.event_id 
              WHERE p.order_id = ?";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($ticket = $result->fetch_assoc()) {
        // Generate QR code
        $qrValue = "Order ID: {$order_id}\nName: {$ticket['name']}\nEvent: {$ticket['title']}";
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=170x170&data=" . urlencode($qrValue);

        // Create PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Set colors
        $pdf->SetTextColor(50, 50, 50); // Dark gray text
        $pdf->SetFillColor(230, 230, 250); // Lavender background
        $pdf->SetDrawColor(100, 149, 237); // Cornflower blue border

        // Add a border and fill color
        $pdf->Rect(5, 5, 200, 287, 'DF');

        // Set font
        $pdf->SetFont('Arial', 'B', 20);

        // Title with a colored background
        $pdf->SetFillColor(100, 149, 237); // Cornflower blue background for title
        $pdf->Cell(0, 20, 'Event Ticket', 0, 1, 'C', true);

        // Event details
        $pdf->SetFont('Arial', '', 14);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, "Event: {$ticket['title']}", 0, 1, 'C');
        $pdf->Cell(0, 10, "Date: {$ticket['date']} Time: {$ticket['time']}", 0, 1, 'C');
        $pdf->Cell(0, 10, "Location: {$ticket['location']}", 0, 1, 'C');

        // Add a line break
        $pdf->Ln(10);

        // Customer details
        $pdf->Cell(0, 10, "Name: {$ticket['name']}", 0, 1, 'C');
        $pdf->Cell(0, 10, "Email: {$ticket['email']}", 0, 1, 'C');
        $pdf->Cell(0, 10, "Mobile: {$ticket['mobile_number']}", 0, 1, 'C');

        // Add a line break
        $pdf->Ln(10);

        // Order details
        $pdf->Cell(0, 10, "Order ID: {$order_id}", 0, 1, 'C');
        $pdf->Cell(0, 10, "Number of Tickets: {$ticket['num_tickets']}", 0, 1, 'C');
        $pdf->Cell(0, 10, "Total Amount: {$ticket['payment_amount']}", 0, 1, 'C');

        // Add a line break
        $pdf->Ln(10);

        // Add QR Code
        $pdf->Image($qrCodeUrl, 80, 200, 50, 50, 'PNG');

        // Output PDF
        ob_end_clean(); // Clear output buffer before sending PDF
        $pdf->Output('D', "ticket_{$order_id}.pdf");
    } else {
        echo "Ticket not found.";
    }
} else {
    echo "Missing order ID parameter.";
}

// After generating the PDF...

// Save PDF to a file
$pdfFilePath = "ticket_{$order_id}.pdf";
$pdf->Output('F', $pdfFilePath);

// Prepare email
$to = $ticket['email'];
$subject = "Your Ticket for {$ticket['title']}";

// Boundary for multipart message
$boundary = md5(time());

// Headers
$headers = "From: Event Organizer <athulsebastianth@gmail.com>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";

// Email body
$message = "--" . $boundary . "\r\n";
$message .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$message .= "
    <h2>Thank you for your booking!</h2>
    <p>Dear {$ticket['name']},</p>
    <p>Your ticket for {$ticket['title']} is attached to this email.</p>
    <p>Event Details:</p>
    <ul>
        <li>Date: {$ticket['date']}</li>
        <li>Time: {$ticket['time']}</li>
        <li>Location: {$ticket['location']}</li>
    </ul>
    <p>Order Details:</p>
    <ul>
        <li>Order ID: {$order_id}</li>
        <li>Number of Tickets: {$ticket['num_tickets']}</li>
        <li>Total Amount: {$ticket['payment_amount']}</li>
    </ul>
    <p>We look forward to seeing you at the event!</p>
    <p>Best regards,<br>Event Organizer Team</p>
\r\n";

// Attachment
$attachment = chunk_split(base64_encode(file_get_contents($pdfFilePath)));
$message .= "--" . $boundary . "\r\n";
$message .= "Content-Type: application/pdf; name=\"ticket_{$order_id}.pdf\"\r\n";
$message .= "Content-Disposition: attachment; filename=\"ticket_{$order_id}.pdf\"\r\n";
$message .= "Content-Transfer-Encoding: base64\r\n\r\n";
$message .= $attachment . "\r\n";
$message .= "--" . $boundary . "--";

// Send email
if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Email sending failed.";
}

// Delete the temporary PDF file
unlink($pdfFilePath);

$mysqli->close();
