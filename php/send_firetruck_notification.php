<?php
// Set headers to handle AJAX requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get the POST data
$postData = json_decode(file_get_contents('php://input'), true);

if (!$postData) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

// Extract data from the POST request
$notificationType = isset($postData['type']) ? $postData['type'] : '';
$fireTruckId = isset($postData['fireTruckId']) ? $postData['fireTruckId'] : '';
$fireTruckName = isset($postData['fireTruckName']) ? $postData['fireTruckName'] : '';
$location = isset($postData['location']) ? $postData['location'] : '';
$incidentType = isset($postData['incidentType']) ? $postData['incidentType'] : '';
$severity = isset($postData['severity']) ? $postData['severity'] : '';
$description = isset($postData['description']) ? $postData['description'] : '';

// Set email parameters
$to = "ankanbasu1234@gmail.com";
$from = "ankanbasu10@gmail.com";
$headers = "From: $from\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// Prepare email content based on notification type
if ($notificationType === 'dispatch') {
    $subject = "ALERT: Fire Truck Dispatched - ID: $fireTruckId";
    $message = "
    <html>
    <head>
        <title>Fire Truck Dispatch Notification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .header { background-color: #dc2626; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { padding: 20px; }
            .footer { background-color: #fee2e2; padding: 10px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
            .info-item { margin-bottom: 10px; }
            .label { font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Fire Truck Dispatch Notification</h2>
            </div>
            <div class='content'>
                <p>A fire truck has been dispatched with the following details:</p>
                
                <div class='info-item'>
                    <span class='label'>Fire Truck ID:</span> $fireTruckId
                </div>
                <div class='info-item'>
                    <span class='label'>Fire Truck Name:</span> $fireTruckName
                </div>
                <div class='info-item'>
                    <span class='label'>Incident Location:</span> $location
                </div>
                <div class='info-item'>
                    <span class='label'>Incident Type:</span> $incidentType
                </div>
                <div class='info-item'>
                    <span class='label'>Dispatch Time:</span> " . date('Y-m-d H:i:s') . "
                </div>
            </div>
            <div class='footer'>
                <p>This is an automated message from the Project Rescue Fire Truck Tracking System.</p>
            </div>
        </div>
    </body>
    </html>
    ";
} else if ($notificationType === 'emergency') {
    $subject = "URGENT: Fire Emergency Reported - $incidentType";
    $message = "
    <html>
    <head>
        <title>Fire Emergency Notification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .header { background-color: #b91c1c; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { padding: 20px; }
            .footer { background-color: #fee2e2; padding: 10px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
            .info-item { margin-bottom: 10px; }
            .label { font-weight: bold; }
            .severity { color: red; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Fire Emergency Notification</h2>
            </div>
            <div class='content'>
                <p>A fire emergency has been reported with the following details:</p>
                
                <div class='info-item'>
                    <span class='label'>Emergency Type:</span> $incidentType
                </div>
                <div class='info-item'>
                    <span class='label'>Location:</span> $location
                </div>
                <div class='info-item'>
                    <span class='label'>Severity:</span> <span class='severity'>$severity</span>
                </div>
                <div class='info-item'>
                    <span class='label'>Description:</span> $description
                </div>
                <div class='info-item'>
                    <span class='label'>Reported Time:</span> " . date('Y-m-d H:i:s') . "
                </div>
                
                <p>A fire truck has been dispatched to the location if available.</p>
            </div>
            <div class='footer'>
                <p>This is an automated message from the Project Rescue Fire Emergency Response System.</p>
            </div>
        </div>
    </body>
    </html>
    ";
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid notification type']);
    exit;
}

// Send the email
$mailSent = mail($to, $subject, $message, $headers);

// Return the result
if ($mailSent) {
    echo json_encode(['success' => true, 'message' => 'Notification email sent successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send notification email']);
}
?>
