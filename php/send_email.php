<?php
// Get form data
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';

// Email details
$to = "ankanbasu1234@gmail.com";
$subject = "RESCUE Contact Form: Message from " . $name;
$email_message = "Name: " . $name . "\n";
$email_message .= "Email: " . $email . "\n\n";
$email_message .= "Message:\n" . $message;
$headers = "From: " . $email;

// Send email
if(mail($to, $subject, $email_message, $headers)) {
    echo json_encode(['status' => 'success', 'message' => 'Email sent successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
}
?>
