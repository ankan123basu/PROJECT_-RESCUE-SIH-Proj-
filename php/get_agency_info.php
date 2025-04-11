<?php
require_once 'db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['agency_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$agency_id = $_SESSION['agency_id'];

$sql = "SELECT name, type, contact, location, status, resources FROM agencies WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Parse resources from JSON if stored as JSON string
    $resources = json_decode($row['resources'], true);
    $row['resources'] = $resources;
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['error' => 'Agency not found']);
}

$stmt->close();
$conn->close();
?>
