<?php
require_once 'config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Get filter parameters from query parameters
$type = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : null;
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : null;
$resource = isset($_GET['resource']) ? mysqli_real_escape_string($conn, $_GET['resource']) : null;

// Build the query
$sql = "SELECT id, name, type, phone, address, resources, latitude, longitude, status FROM agencies WHERE 1=1";
$params = [];
$types = "";

if ($type) {
    $sql .= " AND type = ?";
    $params[] = $type;
    $types .= "s";
}

if ($status) {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($resource) {
    $sql .= " AND JSON_CONTAINS(resources, ?)";
    $params[] = json_encode($resource);
    $types .= "s";
}

// Prepare and execute the query
if ($stmt = mysqli_prepare($conn, $sql)) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $agencies = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            // Parse the JSON resources string
            $row['resources'] = json_decode($row['resources'], true);
            $agencies[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $agencies]);
    } else {
        echo json_encode(['error' => 'Failed to fetch agencies', 'sql_error' => mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Failed to prepare query', 'sql_error' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>
