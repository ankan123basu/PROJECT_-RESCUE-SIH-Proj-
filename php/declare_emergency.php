<?php
session_start();
// For demo purposes, we're not requiring config.php since we don't need database connection
// require_once 'config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// For demo purposes, we're bypassing the session check
// Normally we would check if the user is logged in
// if (!isset($_SESSION['id'])) {
//     echo json_encode(['error' => 'Not authorized']);
//     exit;
// }

// Set a demo agency ID for testing
$_SESSION['id'] = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    // For demo purposes, we'll accept both POST and GET requests
    
    // Get emergency details - using POST or GET as available
    $type = isset($_POST['type']) ? $_POST['type'] : (isset($_GET['type']) ? $_GET['type'] : 'emergency');
    $description = isset($_POST['description']) ? $_POST['description'] : (isset($_GET['description']) ? $_GET['description'] : 'Emergency declared from agency dashboard');
    $location = isset($_POST['location']) ? $_POST['location'] : (isset($_GET['location']) ? $_GET['location'] : 'New Delhi, India');
    $severity = isset($_POST['severity']) ? $_POST['severity'] : (isset($_GET['severity']) ? $_GET['severity'] : 'critical');
    
    // For demo purposes, create a mock agency
    $agency = [
        'name' => 'National Disaster Response Force',
        'type' => 'Emergency Response',
        'latitude' => '28.6139',
        'longitude' => '77.2090'
    ];
    
    // Create a mock emergency ID
    $emergency_id = rand(1000, 9999);
    $agency_id = $_SESSION['id'];
    
    // Create logs directory if it doesn't exist
    if (!file_exists('../logs')) {
        mkdir('../logs', 0777, true);
    }
    
    // Send SOS email notification
    $to = "ankanbasu1234@gmail.com";
    $subject = "SOS EMERGENCY ALERT: {$agency['name']} - {$type}";
    $email_message = "EMERGENCY ALERT\n\n";
    $email_message .= "Agency: {$agency['name']}\n";
    $email_message .= "Type: {$type}\n";
    $email_message .= "Description: {$description}\n";
    $email_message .= "Location: {$location}\n";
    $email_message .= "Severity: {$severity}\n";
    $email_message .= "Time: " . date('Y-m-d H:i:s') . "\n\n";
    $email_message .= "This is an automated SOS message from the RESCUE Emergency System.";
    
    $headers = "From: rescue-system@lpu.co.in";
    
    // Log email attempt
    file_put_contents('../logs/emergency_emails.log', date('Y-m-d H:i:s') . " - Attempting to send SOS email for emergency #{$emergency_id}\n", FILE_APPEND);
    
    // Send email
    $email_sent = mail($to, $subject, $email_message, $headers);
    
    // Log email result
    file_put_contents('../logs/emergency_emails.log', date('Y-m-d H:i:s') . " - Email sent: " . ($email_sent ? "Success" : "Failed") . "\n", FILE_APPEND);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'email_sent' => $email_sent,
        'emergency' => [
            'id' => $emergency_id,
            'type' => $type,
            'description' => $description,
            'location' => $location,
            'severity' => $severity,
            'agency_id' => $agency_id,
            'agency_name' => $agency['name'],
            'agency_type' => $agency['type'],
            'latitude' => $agency['latitude'],
            'longitude' => $agency['longitude'],
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
