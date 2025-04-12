<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get alert data from POST request
$alertType = isset($_POST['alertType']) ? $_POST['alertType'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';

// Validate input
if (empty($alertType) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Alert type and message are required']);
    exit;
}

// Format alert type for email subject
$alertTypeFormatted = ucfirst(str_replace('_', ' ', $alertType));

// Email details
$to = "ankanbasu1234@gmail.com";
$subject = "RESCUE Emergency Alert: " . $alertTypeFormatted;
$email_message = "Alert Type: " . $alertTypeFormatted . "\n\n";
$email_message .= "Message:\n" . $message . "\n\n";
$email_message .= "This alert was sent from the RESCUE Emergency Coordination System at " . date('Y-m-d H:i:s') . ".";
$headers = "From: rescue-system@lpu.co.in";

// Log attempt
file_put_contents('broadcast_log.txt', date('Y-m-d H:i:s') . " - Attempting to send broadcast alert: " . $alertTypeFormatted . "\n", FILE_APPEND);

// Send email
if(mail($to, $subject, $email_message, $headers)) {
    file_put_contents('broadcast_log.txt', date('Y-m-d H:i:s') . " - Email sent successfully\n", FILE_APPEND);
    echo json_encode(['status' => 'success', 'message' => 'Alert broadcast successfully sent to all agencies']);
} else {
    file_put_contents('broadcast_log.txt', date('Y-m-d H:i:s') . " - Failed to send email\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Failed to send alert broadcast']);
}
?>
