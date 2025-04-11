<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $name = mysqli_real_escape_string($conn, $_POST['agency-name']);
    $type = mysqli_real_escape_string($conn, $_POST['agency-type']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $resources = isset($_POST['resources']) ? json_encode($_POST['resources']) : '[]';
    $status = 'available'; // Default status for new agencies
    
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
        $latitude = 0;
        $longitude = 0;
        error_log("Geocoding failed for address: $address");
    }
    
    // Check if email already exists
    $sql = "SELECT id FROM agencies WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'This email is already registered.']);
                exit;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Insert new agency
    $sql = "INSERT INTO agencies (name, type, email, password, phone, address, resources, latitude, longitude, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssssssdds", $name, $type, $email, $password, $phone, $address, $resources, $latitude, $longitude, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            // Get the newly inserted agency ID
            $agency_id = mysqli_insert_id($conn);
            
            // Log successful registration
            error_log("New agency registered: $name (ID: $agency_id) with coordinates: $latitude, $longitude");
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Agency successfully registered!'
            ]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Registration failed. Please try again.',
                'sql_error' => mysqli_error($conn)
            ]);
            exit;
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

mysqli_close($conn);
?>
