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
    
    // Set timezone to IST (Asia/Kolkata) for correct Indian time
    date_default_timezone_set('Asia/Kolkata');
    $current_time = date('Y-m-d H:i:s');
    
    // --- Styled HTML SOS Email with Animation ---
    $email_message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>' .
        'body{background:#f4f8fb;font-family:Poppins,sans-serif;margin:0;padding:0;}' .
        '.sos-container{background:#fff;border-radius:20px;box-shadow:0 8px 32px rgba(30,64,175,0.16);padding:36px 28px;margin:40px auto;max-width:480px;}' .
        '.sos-title{font-size:28px;font-weight:700;color:#dc2626;text-align:center;margin-bottom:22px;letter-spacing:1px;}' .
        '.sos-table{width:100%;border-collapse:separate;border-spacing:0 12px;}' .
        '.sos-label{color:#1e293b;font-weight:500;text-align:right;padding-right:12px;vertical-align:middle;width:105px;}' .
        '.sos-value{padding-left:0;vertical-align:middle;}' .
        '.sos-pill{background:linear-gradient(90deg,#dc2626,#f59e42);color:#fff;padding:4px 16px;border-radius:8px;display:inline-block;font-weight:600;}' .
        '.sos-footer{text-align:center;font-size:13px;color:#64748b;margin-top:24px;letter-spacing:0.5px;}' .
        '.sos-alert{display:flex;align-items:center;justify-content:center;margin-bottom:20px;}' .
        '.sos-bell{width:48px;height:48px;margin-right:12px;animation:sos-bell-shake 1.2s cubic-bezier(.36,.07,.19,.97) infinite;}' .
        '.sos-bell svg{width:100%;height:100%;color:#f59e42;}' .
        '@keyframes sos-bell-shake{0%{transform:rotate(0);}20%{transform:rotate(-15deg);}40%{transform:rotate(10deg);}60%{transform:rotate(-10deg);}80%{transform:rotate(8deg);}100%{transform:rotate(0);}}' .
        '</style></head><body>' .
        '<div class="sos-container">' .
        '<div class="sos-alert"><span class="sos-bell"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></span><span class="sos-title">EMERGENCY ALERT</span></div>' .
        '<table class="sos-table">' .
        '<tr><td class="sos-label">Agency:</td><td class="sos-value"><span class="sos-pill">' . htmlspecialchars($agency['name']) . '</span></td></tr>' .
        '<tr><td class="sos-label">Type:</td><td class="sos-value"><span class="sos-pill">' . htmlspecialchars($type) . '</span></td></tr>' .
        '<tr><td class="sos-label">Description:</td><td class="sos-value"><span class="sos-pill">' . htmlspecialchars($description) . '</span></td></tr>' .
        '<tr><td class="sos-label">Location:</td><td class="sos-value"><span class="sos-pill">' . htmlspecialchars($location) . '</span></td></tr>' .
        '<tr><td class="sos-label">Severity:</td><td class="sos-value"><span class="sos-pill">' . htmlspecialchars($severity) . '</span></td></tr>' .
        '<tr><td class="sos-label">Time:</td><td class="sos-value"><span class="sos-pill">' . $current_time . '</span></td></tr>' .
        '</table>' .
        '<div class="sos-footer">This is an <b>automated SOS message</b> from the RESCUE Emergency System.</div>' .
        '</div></body></html>';
    $headers = "MIME-Version: 1.0\r\n" .
               "Content-type: text/html; charset=UTF-8\r\n" .
               "From: rescue-system@lpu.co.in";
    
    // Log email attempt
    file_put_contents('../logs/emergency_emails.log', date('Y-m-d H:i:s') . " - Attempting to send SOS email for emergency #{$emergency_id}\n", FILE_APPEND);
    
    // Send email
    $email_sent = mail("ankanbasu1234@gmail.com", "SOS EMERGENCY ALERT: {$agency['name']} - {$type}", $email_message, $headers);
    
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
            'created_at' => $current_time
        ]
    ]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
