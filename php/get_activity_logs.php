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

// Get activity logs for the agency
$sql = "SELECT id, activity_type, details, created_at 
        FROM activity_logs 
        WHERE agency_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        $logs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Parse JSON details
            $row['details'] = json_decode($row['details'], true);
            $logs[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'logs' => $logs
        ]);
    } else {
        echo json_encode(['error' => 'Failed to execute query', 'sql_error' => mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Failed to prepare query', 'sql_error' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>
