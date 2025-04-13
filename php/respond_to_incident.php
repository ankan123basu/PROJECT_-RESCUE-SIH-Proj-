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
$incidentId = isset($postData['incidentId']) ? $postData['incidentId'] : '';
$incidentType = isset($postData['type']) ? $postData['type'] : '';
$location = isset($postData['location']) ? $postData['location'] : '';
$status = isset($postData['status']) ? $postData['status'] : '';
$responseDetails = isset($postData['responseDetails']) ? $postData['responseDetails'] : '';
$respondingResources = isset($postData['resources']) ? $postData['resources'] : [];

// Set email parameters
$to = "ankanbasu1234@gmail.com";
$from = "ankanbasu10@gmail.com";
$headers = "From: $from\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// Format responding resources for email
$resourcesList = '';
if (!empty($respondingResources)) {
    $resourcesList = '<ul>';
    foreach ($respondingResources as $resource) {
        $resourcesList .= "<li>$resource</li>";
    }
    $resourcesList .= '</ul>';
} else {
    $resourcesList = '<p>No resources assigned yet</p>';
}

// Prepare email subject and content
$subject = "RESPONSE: Action Taken for Incident $incidentId";
$message = "
<html>
<head>
    <title>Incident Response Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #2563eb; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .footer { background-color: #f3f4f6; padding: 10px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
        .info-item { margin-bottom: 10px; }
        .label { font-weight: bold; }
        .critical { color: #dc2626; font-weight: bold; }
        .moderate { color: #f59e0b; font-weight: bold; }
        .minor { color: #3b82f6; font-weight: bold; }
        .responding { color: #10b981; font-weight: bold; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Incident Response Notification</h2>
        </div>
        <div class='content'>
            <p>A response has been initiated for the following incident:</p>
            
            <div class='info-item'>
                <span class='label'>Incident ID:</span> $incidentId
            </div>
            <div class='info-item'>
                <span class='label'>Type:</span> $incidentType
            </div>
            <div class='info-item'>
                <span class='label'>Location:</span> $location
            </div>
            <div class='info-item'>
                <span class='label'>Current Status:</span> <span class='responding'>Responding</span>
            </div>
            <div class='info-item'>
                <span class='label'>Response Details:</span> $responseDetails
            </div>
            <div class='info-item'>
                <span class='label'>Responding Resources:</span>
                $resourcesList
            </div>
            <div class='info-item'>
                <span class='label'>Response Time:</span> " . date('Y-m-d H:i:s') . "
            </div>
            
            <p>The emergency response team has been dispatched to handle this incident.</p>
        </div>
        <div class='footer'>
            <p>This is an automated message from the Project Rescue Incident Management System.</p>
        </div>
    </div>
</body>
</html>
";

// Send the email
$mailSent = mail($to, $subject, $message, $headers);

// Return the result
if ($mailSent) {
    echo json_encode([
        'success' => true, 
        'message' => 'Response initiated successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send response notification email']);
}
?>
