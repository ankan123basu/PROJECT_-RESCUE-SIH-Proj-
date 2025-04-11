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

// Get agency profile data
$sql = "SELECT id, name, type, email, phone, address, resources, latitude, longitude, status 
        FROM agencies 
        WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Parse resources JSON
            $row['resources'] = json_decode($row['resources'], true);
            
            echo json_encode([
                'success' => true,
                'agency' => $row
            ]);
        } else {
            echo json_encode(['error' => 'Agency not found']);
        }
    } else {
        echo json_encode(['error' => 'Failed to execute query', 'sql_error' => mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Failed to prepare query', 'sql_error' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>
