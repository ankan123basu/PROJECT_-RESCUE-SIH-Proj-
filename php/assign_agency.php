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

// Get agency ID and emergency ID from POST data
$agencyId = isset($_POST['agencyId']) ? intval($_POST['agencyId']) : 0;
$emergencyId = isset($_POST['emergencyId']) ? intval($_POST['emergencyId']) : 0;

// Validate input
if ($agencyId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Agency assigned']);
    exit;
}

if ($emergencyId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'No active emergency to assign']);
    exit;
}

// Simulate a successful response
// In a real application, this would interact with a database
try {
    // Log the assignment for demonstration
    $logFile = fopen("assignment_log.txt", "a");
    fwrite($logFile, date("Y-m-d H:i:s") . " - Assigned Agency #$agencyId to Emergency #$emergencyId\n");
    fclose($logFile);
    
    // Simulate agency data
    $agency = [
        'id' => $agencyId,
        'name' => 'Agency ' . $agencyId,
        'type' => ['ambulance', 'fire', 'medical', 'rescue'][rand(0, 3)],
        'status' => 'assigned'
    ];
    
    // Simulate emergency data
    $emergency = [
        'id' => $emergencyId,
        'type' => 'Earthquake',
        'location' => 'Downtown'
    ];
    
    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => "{$agency['name']} has been assigned to the emergency",
        'assignmentId' => rand(1000, 9999),
        'agency' => $agency,
        'emergency' => $emergency
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred']);
    exit;
}
?>
