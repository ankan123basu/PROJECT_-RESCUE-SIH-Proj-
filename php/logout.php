<?php
// Initialize the session
session_start();
require_once 'config.php';

// Log the logout activity if user is logged in
if (isset($_SESSION['id'])) {
    $log_sql = "INSERT INTO activity_logs (agency_id, activity_type, details) VALUES (?, 'logout', ?)";
    $details = json_encode([
        'timestamp' => time(),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    ]);
    
    if ($log_stmt = mysqli_prepare($conn, $log_sql)) {
        mysqli_stmt_bind_param($log_stmt, "is", $_SESSION['id'], $details);
        mysqli_stmt_execute($log_stmt);
        mysqli_stmt_close($log_stmt);
    }
    
    mysqli_close($conn);
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("location: ../index.html");
exit;
?>
