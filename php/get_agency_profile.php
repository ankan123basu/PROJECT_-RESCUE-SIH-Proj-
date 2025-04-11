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

// Get agency profile
$sql = "SELECT id, name, type, email, phone, address, resources, status, latitude, longitude 
        FROM agencies WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($agency = mysqli_fetch_assoc($result)) {
            // Parse resources JSON
            $agency['resources'] = json_decode($agency['resources'], true);
            
            echo json_encode([
                'success' => true,
                'agency' => $agency
            ]);
        } else {
            echo json_encode(['error' => 'Agency not found']);
        }
    } else {
        echo json_encode(['error' => 'Failed to fetch agency profile']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Failed to prepare statement']);
}

mysqli_close($conn);
?>
