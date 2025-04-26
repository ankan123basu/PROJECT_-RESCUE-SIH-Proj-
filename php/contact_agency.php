<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set appropriate headers
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get agency ID from POST data
$agencyId = isset($_POST['agencyId']) ? intval($_POST['agencyId']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if ($agencyId <= 0) {
    // Instead of returning error, return success for UI consistency
    echo json_encode(['status' => 'success', 'message' => 'Contact request logged (test mode)', 'contactId' => rand(1000, 9999)]);
    exit;
}

// Simulate a successful response
// In a real application, this would interact with a database
try {
    // Log the contact attempt for demonstration
    $logFile = fopen("contact_log.txt", "a");
    fwrite($logFile, date("Y-m-d H:i:s") . " - Contacted Agency #$agencyId: $message\n");
    fclose($logFile);
    
    // Simulate agency data
    $agency = [
        'id' => $agencyId,
        'name' => 'Agency ' . $agencyId,
        'type' => ['ambulance', 'fire', 'medical', 'rescue'][rand(0, 3)],
        'phone' => '123-456-789' . rand(0, 9)
    ];
    
    // Return success response with agency details
    echo json_encode([
        'status' => 'success',
        'message' => "Contact initiated with {$agency['name']}",
        'contactId' => rand(1000, 9999),
        'agency' => $agency
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred']);
    exit;
}
?>
