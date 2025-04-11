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

// Get current agency's information
$sql = "SELECT id, name, type, latitude, longitude FROM agencies WHERE id = ?";
$myAgency = null;

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $myAgency = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// Get connected agencies
$sql = "SELECT a.id, a.name, a.type, a.status, a.phone, a.latitude, a.longitude, a.resources, a.updated_at
        FROM agencies a
        INNER JOIN agency_connections ac ON (a.id = ac.agency_id_1 OR a.id = ac.agency_id_2)
        WHERE (ac.agency_id_1 = ? OR ac.agency_id_2 = ?)
        AND ac.status = 'connected'
        AND a.id != ?";

$connections = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "iii", $_SESSION['id'], $_SESSION['id'], $_SESSION['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            // Parse resources JSON if it exists
            if ($row['resources']) {
                try {
                    $row['resources'] = json_decode($row['resources'], true);
                } catch (Exception $e) {
                    $row['resources'] = [];
                }
            } else {
                $row['resources'] = [];
            }
            $connections[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Get pending connection requests
$sql = "SELECT a.id, a.name, a.type, a.status, ac.created_at
        FROM agencies a
        INNER JOIN agency_connections ac ON a.id = ac.agency_id_1
        WHERE ac.agency_id_2 = ? 
        AND ac.status = 'pending'";

$requests = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Get sent connection requests
$sql = "SELECT a.id, a.name, a.type, a.status, ac.created_at
        FROM agencies a
        INNER JOIN agency_connections ac ON a.id = ac.agency_id_2
        WHERE ac.agency_id_1 = ? 
        AND ac.status = 'pending'";

$sent = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $sent[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Return the response
echo json_encode([
    'success' => true,
    'agency' => $myAgency,
    'connections' => $connections,
    'requests' => $requests,
    'sent' => $sent,
    'timestamp' => time() * 1000 // Current timestamp in milliseconds
]);

mysqli_close($conn);
?>
