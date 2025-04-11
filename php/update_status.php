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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (!isset($_POST['status'])) {
        echo json_encode(['error' => 'Status is required']);
        exit;
    }
    
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
    
    // Validate status value
    $valid_statuses = ['available', 'busy', 'offline', 'emergency'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['error' => 'Invalid status value']);
        exit;
    }
    
    // Update agency status with current timestamp
    $sql = "UPDATE agencies SET status = ?, updated_at = NOW() WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $status, $_SESSION['id']);
        
        if (mysqli_stmt_execute($stmt)) {
            // Log the status change
            $log_sql = "INSERT INTO activity_logs (agency_id, activity_type, details) VALUES (?, 'status_update', ?)";
            $details = json_encode([
                'status' => $status, 
                'notes' => $notes,
                'timestamp' => time()
            ]);
            
            if ($log_stmt = mysqli_prepare($conn, $log_sql)) {
                mysqli_stmt_bind_param($log_stmt, "is", $_SESSION['id'], $details);
                mysqli_stmt_execute($log_stmt);
                mysqli_stmt_close($log_stmt);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Status updated successfully',
                'status' => $status
            ]);
        } else {
            echo json_encode([
                'error' => 'Failed to update status',
                'sql_error' => mysqli_error($conn)
            ]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            'error' => 'Failed to prepare query',
            'sql_error' => mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

mysqli_close($conn);
?>
