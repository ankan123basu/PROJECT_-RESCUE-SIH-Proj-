<?php
session_start();
// For demo purposes, we're not requiring config.php
// require_once 'config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// For demo purposes, we're bypassing the session check
// if (!isset($_SESSION['id'])) {
//     echo json_encode(['error' => 'Not authorized']);
//     exit;
// }

// Set a demo agency ID for testing
$_SESSION['id'] = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    // For demo purposes, we'll accept both POST and GET requests
    
    // Get status from POST or GET data
    $status = isset($_POST['status']) ? $_POST['status'] : (isset($_GET['status']) ? $_GET['status'] : null);
    $notes = isset($_POST['notes']) ? $_POST['notes'] : (isset($_GET['notes']) ? $_GET['notes'] : '');
    
    if (!$status) {
        echo json_encode(['error' => 'Status is required']);
        exit;
    }
    
    // Validate status
    $valid_statuses = ['active', 'standby', 'emergency', 'offline', 'maintenance'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['error' => 'Invalid status value']);
        exit;
    }
    
    // Create logs directory if it doesn't exist
    if (!file_exists('../logs')) {
        mkdir('../logs', 0777, true);
    }
    
    // Log the status update for demo purposes
    $log_entry = date('Y-m-d H:i:s') . " - Status updated to: {$status}\n";
    if (!empty($notes)) {
        $log_entry .= "Notes: {$notes}\n";
    }
    file_put_contents('../logs/status_updates.log', $log_entry, FILE_APPEND);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'status' => $status,
        'notes' => $notes,
        'updated_at' => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
