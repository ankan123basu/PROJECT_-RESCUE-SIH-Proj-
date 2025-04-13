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
$incidentType = isset($postData['type']) ? $postData['type'] : '';
$location = isset($postData['location']) ? $postData['location'] : '';
$severity = isset($postData['severity']) ? $postData['severity'] : '';
$description = isset($postData['description']) ? $postData['description'] : '';
$resourcesNeeded = isset($postData['resources']) ? $postData['resources'] : [];

// Generate incident ID
$incidentId = '#INC-' . date('Y') . '-' . sprintf('%03d', rand(1, 999));

// Set email parameters
$to = "ankanbasu1234@gmail.com";
$from = "ankanbasu10@gmail.com";
$headers = "From: $from\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// Format resources needed for email
$resourcesList = '';
if (!empty($resourcesNeeded)) {
    $resourcesList = '<ul>';
    foreach ($resourcesNeeded as $resource) {
        $resourcesList .= "<li>$resource</li>";
    }
    $resourcesList .= '</ul>';
} else {
    $resourcesList = '<p>No specific resources requested</p>';
}

// Prepare email subject and content
$subject = "ALERT: New Incident Reported - $incidentType";
$message = "
<html>
<head>
    <title>New Incident Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #dc2626; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .footer { background-color: #f3f4f6; padding: 10px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
        .info-item { margin-bottom: 10px; }
        .label { font-weight: bold; }
        .critical { color: #dc2626; font-weight: bold; }
        .moderate { color: #f59e0b; font-weight: bold; }
        .minor { color: #3b82f6; font-weight: bold; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Incident Report</h2>
        </div>
        <div class='content'>
            <p>A new incident has been reported with the following details:</p>
            
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
                <span class='label'>Severity:</span> <span class='" . strtolower($severity) . "'>$severity</span>
            </div>
            <div class='info-item'>
                <span class='label'>Description:</span> $description
            </div>
            <div class='info-item'>
                <span class='label'>Resources Needed:</span>
                $resourcesList
            </div>
            <div class='info-item'>
                <span class='label'>Reported Time:</span> " . date('Y-m-d H:i:s') . "
            </div>
            
            <p>Please respond to this incident as soon as possible.</p>
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
        'message' => 'Incident reported successfully',
        'incidentId' => $incidentId
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send incident report email']);
}
?>
