<?php
session_start();

// Set headers for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (isset($_SESSION["id"])) {
    echo json_encode([
        'loggedIn' => true,
        'agencyId' => $_SESSION["id"],
        'name' => isset($_SESSION["name"]) ? $_SESSION["name"] : ''
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
