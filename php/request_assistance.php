<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Get request parameters
$emergency_type = isset($_POST['emergency_type']) ? $_POST['emergency_type'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$resources_needed = isset($_POST['resources_needed']) ? $_POST['resources_needed'] : [];
$priority = isset($_POST['priority']) ? $_POST['priority'] : 'medium';

// Validate required fields
if (empty($emergency_type) || empty($location) || empty($description)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields. Please provide emergency type, location, and description.'
    ]);
    exit;
}

// In a real application, this would be stored in a database
// For demo purposes, we'll just log the request and return success
$timestamp = date('Y-m-d H:i:s');
$log_entry = "[$timestamp] ASSISTANCE REQUEST - Type: $emergency_type, Location: $location, Priority: $priority\n";
$log_entry .= "Description: $description\n";
$log_entry .= "Resources Needed: " . implode(', ', $resources_needed) . "\n\n";

// Log to file
$log_file = '../logs/assistance_requests.log';

// Create logs directory if it doesn't exist
if (!file_exists('../logs')) {
    mkdir('../logs', 0777, true);
}

// Write to log file
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Send email notification
$to = "ankanbasu1234@gmail.com";
$subject = "URGENT: Assistance Request - $emergency_type Emergency";

// Create email message with HTML formatting
$email_message = "<html><body>";
$email_message .= "<h2 style='color: #e53e3e;'>ASSISTANCE REQUEST</h2>";
$email_message .= "<p><strong>Emergency Type:</strong> $emergency_type</p>";
$email_message .= "<p><strong>Location:</strong> $location</p>";
$email_message .= "<p><strong>Priority:</strong> $priority</p>";
$email_message .= "<p><strong>Description:</strong> $description</p>";

if (!empty($resources_needed)) {
    $email_message .= "<p><strong>Resources Needed:</strong></p>";
    $email_message .= "<ul>";
    foreach ($resources_needed as $resource) {
        $email_message .= "<li>$resource</li>";
    }
    $email_message .= "</ul>";
}

$email_message .= "<p><em>This is an automated message from the Project Rescue system. Please respond according to your agency's protocols.</em></p>";
$email_message .= "</body></html>";

// Set email headers for HTML content
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: projectrescue@emergency-system.org\r\n";

// Send email
$email_sent = mail($to, $subject, $email_message, $headers);

// Prepare response
$response = [
    'success' => true,
    'message' => 'Assistance request sent successfully!',
    'request_id' => uniqid('req_'),
    'timestamp' => $timestamp,
    'agencies_notified' => rand(3, 8), // Simulated number of agencies notified
    'email_sent' => $email_sent
];

echo json_encode($response);
