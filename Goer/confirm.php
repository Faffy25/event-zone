<?php
require('fpdf/fpdf.php');

$conn = mysqli_connect("localhost", "root", "", "event_zone");
if (!$conn) {
    die("DB connection failed");
}

$goer_id = $_GET['goer_id'] ?? 0;
if (!$goer_id) {
    die("Goer ID missing");
}

$sql = "SELECT eg.full_name, e.event_name, e.start_datetime, e.event_location, t.ticket_price, eg.email 
        FROM event_goers eg
        JOIN events e ON eg.event_id = e.event_id
        JOIN tickets t ON t.event_id = e.event_id
        WHERE eg.id = $goer_id";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("No data found");
}

class PDF extends FPDF
{
    function Header()
    {
     
    $this->SetFont('Arial', 'B', 16);
    $this->SetTextColor(102, 0, 0); 

    $this->Cell(0, 12, 'EVENT TICKET', 0, 1, 'C');

    }

    function Footer()
    {
    $this->SetY(-10);
    $this->SetFont('Arial', 'I', 10);
    $this->SetTextColor(0, 0, 0);
    $this->Cell(0, 8, 'Thank you for your purchase!', 0, 0, 'C');
}

function TicketBody($data)
{
    $this->SetFont('Arial', '', 10);
    $this->SetTextColor(0, 0, 0);

    $this->Ln(2);
    $this->Cell(0, 8, 'Event: ' . $data['event_name'], 0, 1, 'C');
    $this->Cell(0, 8, 'Attendee: ' . $data['full_name'], 0, 1, 'C');
    $this->Cell(0, 8, 'Date: ' . date("M j, Y h:i A", strtotime($data['start_datetime'])), 0, 1, 'C');
    $this->Cell(0, 8, 'Location: ' . $data['event_location'], 0, 1, 'C');
    $this->SetFont('Arial', 'B', 11);
    $this->Cell(0, 8, 'Price: ZMW ' . number_format($data['ticket_price'], 2), 0, 1, 'C');
    $this->Ln(3);

    $this->SetFont('Arial', 'I', 8);
}
}

$pdf = new PDF('P', 'mm', array(100, 70));
$pdf->AddPage();
$pdf->TicketBody($data);
$pdf_content = $pdf->Output('', 'S');

$to = $data['email'];
$subject = "Ticket for " . $data['event_name'];
$from = "martinluns1@gmail.com";
$boundary = md5(time());

$headers = "From: $from\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

$message = "--$boundary\r\n";
$message .= "Content-Type: text/plain\r\n\r\n";
$message .= "Hi " . $data['full_name'] . ",\n\nThank you for registering Were excited to see at this event. \n\nYou can download your ticket below\n\n";

$message .= "--$boundary\r\n";
$message .= "Content-Type: application/pdf; name=\"ticket.pdf\"\r\n";
$message .= "Content-Transfer-Encoding: base64\r\n";
$message .= "Content-Disposition: attachment; filename=\"ticket.pdf\"\r\n\r\n";
$message .= chunk_split(base64_encode($pdf_content)) . "\r\n";
$message .= "--$boundary--";

if (mail($to, $subject, $message, $headers)) {
    echo "Email sent to $to";
} else {
    echo "Email failed.";
}

mysqli_close($conn);
?>