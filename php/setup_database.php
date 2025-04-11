<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'rescue_system';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($database);

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop existing tables
$conn->query("DROP TABLE IF EXISTS activity_logs");
$conn->query("DROP TABLE IF EXISTS agency_connections");
$conn->query("DROP TABLE IF EXISTS emergency_responses");
$conn->query("DROP TABLE IF EXISTS emergencies");
$conn->query("DROP TABLE IF EXISTS incidents");
$conn->query("DROP TABLE IF EXISTS agencies");

// Enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Create agencies table
$sql = "CREATE TABLE IF NOT EXISTS agencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    resources TEXT NOT NULL,
    status ENUM('available', 'busy', 'offline', 'emergency') DEFAULT 'available',
    latitude DECIMAL(10,8) DEFAULT 0,
    longitude DECIMAL(11,8) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Agencies table created successfully<br>";
} else {
    echo "Error creating agencies table: " . $conn->error . "<br>";
}

// Create incidents table
$sql = "CREATE TABLE IF NOT EXISTS incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    assigned_agency_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_agency_id) REFERENCES agencies(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Incidents table created successfully<br>";
} else {
    echo "Error creating incidents table: " . $conn->error . "<br>";
}

// Create emergencies table
$sql = "CREATE TABLE IF NOT EXISTS emergencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    status ENUM('active', 'resolved') DEFAULT 'active',
    affected_area TEXT,
    severity ENUM('moderate', 'severe', 'critical') DEFAULT 'severe',
    declared_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (declared_by) REFERENCES agencies(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Emergencies table created successfully<br>";
} else {
    echo "Error creating emergencies table: " . $conn->error . "<br>";
}

// Create emergency_responses table
$sql = "CREATE TABLE IF NOT EXISTS emergency_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emergency_id INT,
    agency_id INT,
    status ENUM('responding', 'on_site', 'completed') DEFAULT 'responding',
    response_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_time TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (emergency_id) REFERENCES emergencies(id),
    FOREIGN KEY (agency_id) REFERENCES agencies(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Emergency responses table created successfully<br>";
} else {
    echo "Error creating emergency responses table: " . $conn->error . "<br>";
}

// Create agency_connections table
$sql = "CREATE TABLE IF NOT EXISTS agency_connections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requesting_agency_id INT,
    receiving_agency_id INT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (requesting_agency_id) REFERENCES agencies(id),
    FOREIGN KEY (receiving_agency_id) REFERENCES agencies(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Agency connections table created successfully<br>";
} else {
    echo "Error creating agency connections table: " . $conn->error . "<br>";
}

// Create activity_logs table
$sql = "CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agency_id INT,
    activity_type ENUM('status_update', 'connection_request', 'connection_accepted', 'emergency_response', 'profile_update', 'login', 'logout') NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agency_id) REFERENCES agencies(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Activity logs table created successfully<br>";
} else {
    echo "Error creating activity logs table: " . $conn->error . "<br>";
}

// Create a test agency account
$test_email = 'test@example.com';
$test_password = password_hash('test123', PASSWORD_DEFAULT);

$sql = "INSERT INTO agencies (name, type, email, password, phone, address, resources, latitude, longitude) 
        VALUES ('Test Agency', 'ambulance', '$test_email', '$test_password', '1234567890', 'Test Address', '[]', 0, 0)";

if ($conn->query($sql) === TRUE) {
    echo "Test account created successfully<br>";
    echo "You can login with:<br>";
    echo "Email: test@example.com<br>";
    echo "Password: test123<br>";
} else {
    echo "Error creating test account: " . $conn->error . "<br>";
}

// Close connection
$conn->close();
echo "Database setup completed!";
?>
