<?php
session_start();
require_once 'config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$agency_id = isset($data['agency_id']) ? intval($data['agency_id']) : 0;

if (!$agency_id) {
    echo json_encode(['error' => 'Agency ID is required']);
    exit;
}

// Check if connection already exists
$sql = "SELECT id, status FROM agency_connections 
        WHERE (requesting_agency_id = ? AND receiving_agency_id = ?)
        OR (requesting_agency_id = ? AND receiving_agency_id = ?)";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "iiii", $_SESSION['id'], $agency_id, $agency_id, $_SESSION['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($existing = mysqli_fetch_assoc($result)) {
            echo json_encode([
                'error' => 'Connection request already exists',
                'status' => $existing['status']
            ]);
            exit;
        }
    }
    mysqli_stmt_close($stmt);
}

// Create new connection request
$sql = "INSERT INTO agency_connections (requesting_agency_id, receiving_agency_id) VALUES (?, ?)";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['id'], $agency_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $connection_id = mysqli_insert_id($conn);
        echo json_encode([
            'success' => true,
            'message' => 'Connection request sent',
            'connection_id' => $connection_id
        ]);
    } else {
        echo json_encode(['error' => 'Failed to create connection request']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Failed to prepare statement']);
}

mysqli_close($conn);
?>
