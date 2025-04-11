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
    $name = mysqli_real_escape_string($conn, $_POST['agency-name']);
    $type = mysqli_real_escape_string($conn, $_POST['agency-type']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $resources = isset($_POST['resources']) ? json_encode($_POST['resources']) : '[]';
    
    // Check if address has changed
    $sql = "SELECT address FROM agencies WHERE id = ?";
    $old_address = "";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $old_address = $row['address'];
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // If address has changed, get new coordinates
    $latitude = 0;
    $longitude = 0;
    
    if ($address != $old_address) {
        // Get coordinates from address using OpenStreetMap Nominatim
        $address_encoded = urlencode($address);
        $geocoding_url = "https://nominatim.openstreetmap.org/search?format=json&q={$address_encoded}&limit=1";
        
        // Set User-Agent as required by Nominatim Usage Policy
        $opts = [
            'http' => [
                'header' => "User-Agent: RescueAgencySystem/1.0\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        
        $response = @file_get_contents($geocoding_url, false, $context);
        $data = json_decode($response, true);
        
        if ($response && isset($data[0]['lat']) && isset($data[0]['lon'])) {
            $latitude = $data[0]['lat'];
            $longitude = $data[0]['lon'];
        } else {
            // If geocoding fails, set default coordinates or handle the error
            error_log("Geocoding failed for address: $address");
            echo json_encode(['error' => 'Failed to geocode address']);
            exit;
        }
        
        // Update agency with new coordinates
        $sql = "UPDATE agencies SET name = ?, type = ?, email = ?, phone = ?, address = ?, resources = ?, status = ?, latitude = ?, longitude = ? WHERE id = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssssddi", $name, $type, $email, $phone, $address, $resources, $status, $latitude, $longitude, $_SESSION['id']);
            
            if (mysqli_stmt_execute($stmt)) {
                // Log the profile update activity
                $log_sql = "INSERT INTO activity_logs (agency_id, activity_type, details) VALUES (?, 'profile_update', ?)";
                $details = json_encode([
                    'timestamp' => time(),
                    'address_updated' => true,
                    'fields_updated' => ['name', 'type', 'email', 'phone', 'address', 'resources', 'status']
                ]);
                
                if ($log_stmt = mysqli_prepare($conn, $log_sql)) {
                    mysqli_stmt_bind_param($log_stmt, "is", $_SESSION['id'], $details);
                    mysqli_stmt_execute($log_stmt);
                    mysqli_stmt_close($log_stmt);
                }
                
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            } else {
                echo json_encode(['error' => 'Failed to update profile', 'sql_error' => mysqli_error($conn)]);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['error' => 'Failed to prepare query', 'sql_error' => mysqli_error($conn)]);
        }
    } else {
        // Update agency without changing coordinates
        $sql = "UPDATE agencies SET name = ?, type = ?, email = ?, phone = ?, resources = ?, status = ? WHERE id = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $name, $type, $email, $phone, $resources, $status, $_SESSION['id']);
            
            if (mysqli_stmt_execute($stmt)) {
                // Log the profile update activity
                $log_sql = "INSERT INTO activity_logs (agency_id, activity_type, details) VALUES (?, 'profile_update', ?)";
                $details = json_encode([
                    'timestamp' => time(),
                    'address_updated' => false,
                    'fields_updated' => ['name', 'type', 'email', 'phone', 'resources', 'status']
                ]);
                
                if ($log_stmt = mysqli_prepare($conn, $log_sql)) {
                    mysqli_stmt_bind_param($log_stmt, "is", $_SESSION['id'], $details);
                    mysqli_stmt_execute($log_stmt);
                    mysqli_stmt_close($log_stmt);
                }
                
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            } else {
                echo json_encode(['error' => 'Failed to update profile', 'sql_error' => mysqli_error($conn)]);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['error' => 'Failed to prepare query', 'sql_error' => mysqli_error($conn)]);
        }
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

mysqli_close($conn);
?>
