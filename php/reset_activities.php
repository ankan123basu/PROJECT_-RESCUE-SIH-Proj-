<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectrescue";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Drop the existing activities table
$sql = "DROP TABLE IF EXISTS activities";
if ($conn->query($sql) !== TRUE) {
    die("Error dropping table: " . $conn->error);
}

// Create the activities table
$sql = "CREATE TABLE activities (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    activity_type VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    icon_color VARCHAR(50) NOT NULL,
    bg_color VARCHAR(50) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Define the new activities
$activities = [
    [
        'activity_type' => 'success',
        'title' => 'Mission Completed',
        'description' => 'Successfully evacuated 23 civilians from flood zone',
        'icon' => 'fa-check',
        'icon_color' => 'text-green-400',
        'bg_color' => 'bg-green-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-3 hours'))
    ],
    [
        'activity_type' => 'update',
        'title' => 'Status Update',
        'description' => 'Resources reallocated to northern sector',
        'icon' => 'fa-sync',
        'icon_color' => 'text-blue-400',
        'bg_color' => 'bg-blue-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-5 hours'))
    ],
    [
        'activity_type' => 'alert',
        'title' => 'Alert Received',
        'description' => 'Weather warning: Heavy rainfall expected',
        'icon' => 'fa-exclamation',
        'icon_color' => 'text-yellow-400',
        'bg_color' => 'bg-yellow-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-7 hours'))
    ],
    [
        'activity_type' => 'emergency',
        'title' => 'Earthquake Detected',
        'description' => 'Magnitude 5.8 earthquake reported in Western Region',
        'icon' => 'fa-house-damage',
        'icon_color' => 'text-orange-500',
        'bg_color' => 'bg-orange-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-9 hours'))
    ],
    [
        'activity_type' => 'deployment',
        'title' => 'Team Deployed',
        'description' => 'Search and rescue team deployed to Eastern Mountains',
        'icon' => 'fa-helicopter',
        'icon_color' => 'text-purple-400',
        'bg_color' => 'bg-purple-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-11 hours'))
    ],
    [
        'activity_type' => 'medical',
        'title' => 'Medical Emergency',
        'description' => 'Medical supplies dispatched to Southern Region',
        'icon' => 'fa-first-aid',
        'icon_color' => 'text-red-400',
        'bg_color' => 'bg-red-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-13 hours'))
    ],
    [
        'activity_type' => 'rescue',
        'title' => 'Rescue Operation',
        'description' => 'Water rescue team deployed to flooded areas',
        'icon' => 'fa-life-ring',
        'icon_color' => 'text-blue-400',
        'bg_color' => 'bg-blue-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-15 hours'))
    ],
    [
        'activity_type' => 'supply',
        'title' => 'Supplies Delivered',
        'description' => 'Emergency food and water delivered to affected communities',
        'icon' => 'fa-box',
        'icon_color' => 'text-green-400',
        'bg_color' => 'bg-green-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-17 hours'))
    ],
    [
        'activity_type' => 'training',
        'title' => 'Training Completed',
        'description' => 'Emergency response team completed disaster simulation',
        'icon' => 'fa-graduation-cap',
        'icon_color' => 'text-purple-400',
        'bg_color' => 'bg-purple-500/20',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-19 hours'))
    ]
];

// Insert the new activities
$success = true;
foreach ($activities as $activity) {
    $stmt = $conn->prepare("INSERT INTO activities (activity_type, title, description, icon, icon_color, bg_color, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", 
        $activity['activity_type'], 
        $activity['title'], 
        $activity['description'], 
        $activity['icon'], 
        $activity['icon_color'], 
        $activity['bg_color'], 
        $activity['timestamp']
    );
    
    if (!$stmt->execute()) {
        echo "Error inserting activity: " . $stmt->error . "<br>";
        $success = false;
    }
    
    $stmt->close();
}

// Close the connection
$conn->close();

if ($success) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #f0f8ff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #4CAF50;'>Activities Reset Successfully!</h2>";
    echo "<p>The activities database has been reset with the new data.</p>";
    echo "<p>The following activities have been added:</p>";
    echo "<ul>";
    foreach ($activities as $activity) {
        echo "<li><strong>{$activity['title']}</strong>: {$activity['description']}</li>";
    }
    echo "</ul>";
    echo "<p><a href='../agency-dashboard.html' style='display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Return to Dashboard</a></p>";
    echo "</div>";
} else {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #fff0f0; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #F44336;'>Error Resetting Activities</h2>";
    echo "<p>There was an error resetting the activities database. Please check the error messages above.</p>";
    echo "<p><a href='../agency-dashboard.html' style='display: inline-block; background-color: #F44336; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Return to Dashboard</a></p>";
    echo "</div>";
}
?>
