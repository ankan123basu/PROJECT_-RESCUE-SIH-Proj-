<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectrescue";

// First, connect without selecting a database to create it if needed
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die(json_encode(['status' => 'error', 'message' => 'Error creating database: ' . $conn->error]));
}

// Select the database
$conn->select_db($dbname);

// Log successful connection
file_put_contents('activity_log.txt', date('Y-m-d H:i:s') . " - Connected to database successfully\n", FILE_APPEND);

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS activities (
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
    file_put_contents('activity_error.txt', date('Y-m-d H:i:s') . " - Error creating table: " . $conn->error . "\n", FILE_APPEND);
    die(json_encode(['status' => 'error', 'message' => 'Error creating table: ' . $conn->error]));
}

// Log table creation/check
file_put_contents('activity_log.txt', date('Y-m-d H:i:s') . " - Table check/creation successful\n", FILE_APPEND);

// Check if we need to insert sample data (if table is empty)
$result = $conn->query("SELECT COUNT(*) as count FROM activities");

// Check if query was successful
if (!$result) {
    file_put_contents('activity_error.txt', date('Y-m-d H:i:s') . " - Error checking table count: " . $conn->error . "\n", FILE_APPEND);
    die(json_encode(['status' => 'error', 'message' => 'Error checking table: ' . $conn->error]));
}

$row = $result->fetch_assoc();
file_put_contents('activity_log.txt', date('Y-m-d H:i:s') . " - Found " . $row['count'] . " existing activities\n", FILE_APPEND);

if ($row['count'] == 0) {
    // Insert sample activities
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

    // Log sample data insertion
    file_put_contents('activity_log.txt', date('Y-m-d H:i:s') . " - Inserting sample data\n", FILE_APPEND);
    
    foreach ($activities as $activity) {
        $stmt = $conn->prepare("INSERT INTO activities (activity_type, title, description, icon, icon_color, bg_color, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            file_put_contents('activity_error.txt', date('Y-m-d H:i:s') . " - Prepare failed: " . $conn->error . "\n", FILE_APPEND);
            continue;
        }
        
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
            file_put_contents('activity_error.txt', date('Y-m-d H:i:s') . " - Execute failed: " . $stmt->error . "\n", FILE_APPEND);
        } else {
            file_put_contents('activity_log.txt', date('Y-m-d H:i:s') . " - Inserted activity: " . $activity['title'] . "\n", FILE_APPEND);
        }
        
        $stmt->close();
    }
}

// Get activities from database
$sql = "SELECT * FROM activities ORDER BY timestamp DESC LIMIT 6";
$result = $conn->query($sql);

if (!$result) {
    file_put_contents('activity_error.txt', date('Y-m-d H:i:s') . " - Error fetching activities: " . $conn->error . "\n", FILE_APPEND);
    die(json_encode(['status' => 'error', 'message' => 'Error fetching activities: ' . $conn->error]));
}

file_put_contents('activity_log.txt', date('Y-m-d H:i:s') . " - Successfully queried activities\n", FILE_APPEND);

$activities = [];
if ($result->num_rows > 0) {
    file_put_contents('activity_log.txt', date('Y-m-d H:i:s') . " - Found " . $result->num_rows . " activities\n", FILE_APPEND);
    while($row = $result->fetch_assoc()) {
        // Calculate time ago
        $timestamp = strtotime($row['timestamp']);
        $current_time = time();
        $time_difference = $current_time - $timestamp;
        
        if ($time_difference < 60) {
            $time_ago = "Just now";
        } elseif ($time_difference < 3600) {
            $minutes = floor($time_difference / 60);
            $time_ago = $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
        } elseif ($time_difference < 86400) {
            $hours = floor($time_difference / 3600);
            $time_ago = $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
        } else {
            $days = floor($time_difference / 86400);
            $time_ago = $days . " day" . ($days > 1 ? "s" : "") . " ago";
        }
        
        $row['time_ago'] = $time_ago;
        $activities[] = $row;
    }
}

// Return activities as JSON
header('Content-Type: application/json');
$response = ['status' => 'success', 'activities' => $activities];

file_put_contents('activity_log.txt', date('Y-m-d H:i:s') . " - Returning " . count($activities) . " activities\n", FILE_APPEND);

echo json_encode($response);

$conn->close();
?>
