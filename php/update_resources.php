<?php
/**
 * Update Agency Resources
 * 
 * This script handles updating an agency's resources and logs the activity.
 */

require_once 'db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['agency_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$agency_id = $_SESSION['agency_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['resources'])) {
    echo json_encode(['error' => 'Invalid data format']);
    exit;
}

$resources = json_encode($data['resources']); // Convert to JSON string for storage

$sql = "UPDATE agencies SET resources = ?, updated_at = NOW() WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $resources, $agency_id);

if ($stmt->execute()) {
    // Log the resource update activity
    $log_sql = "INSERT INTO activity_logs (agency_id, activity_type, details) VALUES (?, 'resource_update', ?)";
    $details = json_encode([
        'timestamp' => time(),
        'resources' => $data['resources']
    ]);
    
    if ($log_stmt = $conn->prepare($log_sql)) {
        $log_stmt->bind_param("is", $agency_id, $details);
        $log_stmt->execute();
        $log_stmt->close();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Resources updated successfully'
    ]);
} else {
    echo json_encode([
        'error' => 'Failed to update resources',
        'sql_error' => $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
