<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input (backend validation)
    $name = trim($_POST['agency-name']);
    $type = trim($_POST['agency-type']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $address = trim($_POST['address']);
    $resources = isset($_POST['resources']) ? json_encode($_POST['resources']) : '[]';
    $status = 'available'; // Default status for new agencies

    // 1. Required fields
    if (empty($name) || empty($type) || empty($email) || empty($phone) || empty($password) || empty($address)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    // 2. Email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid email format.']);
        exit;
    }

    // 3. Password strength (min 8 chars, at least 1 number, 1 special char)
    if (!preg_match('/^(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/', $password)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Password must be at least 8 characters long and include a number and a special character.']);
        exit;
    }

    // 4. Phone format (digits only, exactly 10 digits)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid phone number. Only 10 digits allowed.']);
        exit;
    }

    
    // 6. Address validation
    if (strlen($address) < 5) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Please provide a valid address.']);
        exit;
    }

    // 7. Password hash
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Used to do geomapping of the agency on map using OpenStreetMap
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
        mysqli_stmt_bind_param($stmt, "sssssssdds", $name, $type, $email, $password_hash, $phone, $address, $resources, $latitude, $longitude, $status);

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
