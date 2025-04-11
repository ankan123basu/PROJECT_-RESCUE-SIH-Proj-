<?php
/**
 * Real-time Updates API
 * 
 * This script fetches updates for the Agency Dashboard, Agency Network,
 * and Emergency Coordination Center based on the module and timestamp.
 */

// Initialize the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// Include database connection
require_once 'config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Get parameters
$module = isset($_GET['module']) ? $_GET['module'] : '';
$timestamp = isset($_GET['timestamp']) ? (int)$_GET['timestamp'] : 0;

// Convert timestamp to MySQL datetime format
$datetime = date('Y-m-d H:i:s', $timestamp / 1000);

// Get agency ID from session
$agency_id = $_SESSION['id'];

// Prepare response array
$response = [
    'success' => true,
    'module' => $module,
    'timestamp' => time() * 1000, // Current timestamp in milliseconds
    'updates' => []
];

// Fetch updates based on module
switch ($module) {
    case 'dashboard':
        // Fetch activity logs
        $sql = "SELECT * FROM activity_logs WHERE created_at > ? ORDER BY created_at DESC LIMIT 20";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $datetime);
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $logs = [];
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $logs[] = $row;
                }
                
                $response['updates']['activity_logs'] = $logs;
            }
            
            mysqli_stmt_close($stmt);
        }
        
        // Fetch agency status updates
        $sql = "SELECT a.id, a.name, a.status, a.updated_at FROM agencies a WHERE a.updated_at > ? ORDER BY a.updated_at DESC LIMIT 20";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $datetime);
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $statuses = [];
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $statuses[] = $row;
                }
                
                $response['updates']['agency_statuses'] = $statuses;
            }
            
            mysqli_stmt_close($stmt);
        }
        break;
        
    case 'network':
        // Fetch network connections (connected agencies)
        $sql = "SELECT a.id, a.name, a.type, a.status, a.latitude, a.longitude, a.updated_at 
                FROM agencies a 
                JOIN agency_connections ac ON (a.id = ac.agency_id_1 OR a.id = ac.agency_id_2) 
                WHERE (ac.agency_id_1 = ? OR ac.agency_id_2 = ?) 
                AND a.id != ? 
                AND ac.status = 'connected' 
                AND a.updated_at > ?";
                
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiis", $agency_id, $agency_id, $agency_id, $datetime);
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $connections = [];
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $connections[] = $row;
                }
                
                $response['updates']['connections'] = $connections;
            }
            
            mysqli_stmt_close($stmt);
        }
        
        // Fetch connection requests
        $sql = "SELECT a.id, a.name, a.type, ac.created_at, ac.status
                FROM agencies a 
                JOIN agency_connections ac ON a.id = ac.agency_id_1
                WHERE ac.agency_id_2 = ? 
                AND ac.status = 'pending' 
                AND ac.created_at > ?";
                
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "is", $agency_id, $datetime);
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $requests = [];
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $requests[] = $row;
                }
                
                $response['updates']['requests'] = $requests;
            }
            
            mysqli_stmt_close($stmt);
        }
        break;
        
    case 'emergency':
        // Fetch emergency declarations
        $sql = "SELECT e.*, a.name as agency_name, a.type as agency_type 
                FROM emergencies e 
                JOIN agencies a ON e.agency_id = a.id 
                WHERE e.created_at > ? 
                ORDER BY e.created_at DESC";
                
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $datetime);
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $emergencies = [];
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $emergencies[] = $row;
                }
                
                $response['updates']['emergencies'] = $emergencies;
            }
            
            mysqli_stmt_close($stmt);
        }
        
        // Fetch resource availability
        $sql = "SELECT a.id, a.name, a.type, a.resources, a.status, a.updated_at 
                FROM agencies a 
                WHERE a.updated_at > ?";
                
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $datetime);
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $resources = [];
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $resources[] = $row;
                }
                
                $response['updates']['resources'] = $resources;
            }
            
            mysqli_stmt_close($stmt);
        }
        break;
        
    default:
        $response['success'] = false;
        $response['error'] = 'Invalid module specified';
        break;
}

// Return JSON response
echo json_encode($response);

// Close connection
mysqli_close($conn);
?>
