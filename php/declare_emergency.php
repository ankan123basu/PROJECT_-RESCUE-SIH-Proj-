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
    // Get emergency details
    $type = isset($_POST['type']) ? mysqli_real_escape_string($conn, $_POST['type']) : null;
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
    $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';
    $severity = isset($_POST['severity']) ? mysqli_real_escape_string($conn, $_POST['severity']) : 'medium';
    
    if (!$type) {
        echo json_encode(['error' => 'Emergency type is required']);
        exit;
    }
    
    // Validate severity
    $valid_severities = ['low', 'medium', 'high', 'critical'];
    if (!in_array($severity, $valid_severities)) {
        $severity = 'medium'; // Default to medium if invalid
    }
    
    // Get agency information
    $agency_id = $_SESSION['id'];
    $agency_sql = "SELECT name, type, latitude, longitude FROM agencies WHERE id = ?";
    $agency_stmt = mysqli_prepare($conn, $agency_sql);
    mysqli_stmt_bind_param($agency_stmt, "i", $agency_id);
    mysqli_stmt_execute($agency_stmt);
    $agency_result = mysqli_stmt_get_result($agency_stmt);
    $agency = mysqli_fetch_assoc($agency_result);
    mysqli_stmt_close($agency_stmt);
    
    // Create new emergency
    $sql = "INSERT INTO emergencies (agency_id, type, description, location, severity, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'active', NOW())";
            
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "issss", $agency_id, $type, $description, $location, $severity);
        
        if (mysqli_stmt_execute($stmt)) {
            $emergency_id = mysqli_insert_id($conn);
            
            // Update declaring agency's status to emergency
            $update_sql = "UPDATE agencies SET status = 'emergency', updated_at = NOW() WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "i", $agency_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
            
            // Log the emergency declaration
            $log_sql = "INSERT INTO activity_logs (agency_id, activity_type, details) VALUES (?, 'emergency_declared', ?)";
            $details = json_encode([
                'emergency_id' => $emergency_id,
                'type' => $type,
                'severity' => $severity,
                'timestamp' => time()
            ]);
            
            $log_stmt = mysqli_prepare($conn, $log_sql);
            mysqli_stmt_bind_param($log_stmt, "is", $agency_id, $details);
            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
            
            echo json_encode([
                'success' => true,
                'emergency' => [
                    'id' => $emergency_id,
                    'type' => $type,
                    'description' => $description,
                    'location' => $location,
                    'severity' => $severity,
                    'agency_id' => $agency_id,
                    'agency_name' => $agency['name'],
                    'agency_type' => $agency['type'],
                    'latitude' => $agency['latitude'],
                    'longitude' => $agency['longitude'],
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            echo json_encode([
                'error' => 'Failed to declare emergency',
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
