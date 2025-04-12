<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Get request parameters
$intel_type = isset($_POST['intel_type']) ? $_POST['intel_type'] : '';
$title = isset($_POST['title']) ? $_POST['title'] : '';
$content = isset($_POST['content']) ? $_POST['content'] : '';
$classification = isset($_POST['classification']) ? $_POST['classification'] : 'standard';
$share_with = isset($_POST['share_with']) ? $_POST['share_with'] : [];

// Validate required fields
if (empty($intel_type) || empty($title) || empty($content)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields. Please provide intelligence type, title, and content.'
    ]);
    exit;
}

// In a real application, this would be stored in a database
// For demo purposes, we'll just log the intelligence and return success
$timestamp = date('Y-m-d H:i:s');
$log_entry = "[$timestamp] INTELLIGENCE SHARED - Type: $intel_type, Classification: $classification\n";
$log_entry .= "Title: $title\n";
$log_entry .= "Content: $content\n";
$log_entry .= "Shared with: " . implode(', ', $share_with) . "\n\n";

// Log to file
$log_file = '../logs/intelligence_shared.log';

// Create logs directory if it doesn't exist
if (!file_exists('../logs')) {
    mkdir('../logs', 0777, true);
}

// Write to log file
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Send email notification
$to = "ankanbasu1234@gmail.com";

// Adjust subject based on classification
$classification_label = '';
switch ($classification) {
    case 'sensitive':
        $classification_label = '[SENSITIVE]';
        break;
    case 'restricted':
        $classification_label = '[RESTRICTED]';
        break;
    default:
        $classification_label = '';
}

$subject = "Intelligence Report $classification_label: $title";

// Create email message with HTML formatting
$email_message = "<html><body>";

// Add classification banner if applicable
if ($classification == 'sensitive') {
    $email_message .= "<div style='background-color: #f59e0b; color: white; padding: 10px; text-align: center; margin-bottom: 15px;'>
                        <strong>SENSITIVE INFORMATION - LIMITED DISTRIBUTION</strong>
                      </div>";
} else if ($classification == 'restricted') {
    $email_message .= "<div style='background-color: #dc2626; color: white; padding: 10px; text-align: center; margin-bottom: 15px;'>
                        <strong>RESTRICTED INFORMATION - AUTHORIZED PERSONNEL ONLY</strong>
                      </div>";
}

$email_message .= "<h2 style='color: #2563eb;'>INTELLIGENCE REPORT</h2>";
$email_message .= "<p><strong>Type:</strong> $intel_type</p>";
$email_message .= "<p><strong>Title:</strong> $title</p>";
$email_message .= "<p><strong>Classification:</strong> $classification</p>";
$email_message .= "<div style='border-left: 4px solid #2563eb; padding-left: 15px; margin: 15px 0;'>
                    <p>$content</p>
                  </div>";

if (!empty($share_with)) {
    $email_message .= "<p><strong>Shared with:</strong> " . implode(', ', $share_with) . "</p>";
}

$email_message .= "<p><em>This is an automated message from the Project Rescue system. Please handle according to classification protocols.</em></p>";
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
    'message' => 'Intelligence shared successfully!',
    'intel_id' => uniqid('intel_'),
    'timestamp' => $timestamp,
    'agencies_notified' => count($share_with) > 0 ? count($share_with) : rand(1, 5), // Use actual count or simulate
    'email_sent' => $email_sent
];

echo json_encode($response);
