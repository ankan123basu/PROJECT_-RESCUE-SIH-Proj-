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
    // Get password data
    $current_password = $_POST['current-password'];
    $new_password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];
    
    // Validate passwords
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo json_encode(['error' => 'All password fields are required']);
        exit;
    }
    
    if ($new_password !== $confirm_password) {
        echo json_encode(['error' => 'New passwords do not match']);
        exit;
    }
    
    // Verify current password
    $sql = "SELECT password FROM agencies WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($result)) {
                $hashed_password = $row['password'];
                
                if (password_verify($current_password, $hashed_password)) {
                    // Current password is correct, update to new password
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    $update_sql = "UPDATE agencies SET password = ? WHERE id = ?";
                    
                    if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                        mysqli_stmt_bind_param($update_stmt, "si", $new_hashed_password, $_SESSION['id']);
                        
                        if (mysqli_stmt_execute($update_stmt)) {
                            echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
                        } else {
                            echo json_encode(['error' => 'Failed to update password', 'sql_error' => mysqli_error($conn)]);
                        }
                        
                        mysqli_stmt_close($update_stmt);
                    } else {
                        echo json_encode(['error' => 'Failed to prepare update query', 'sql_error' => mysqli_error($conn)]);
                    }
                } else {
                    echo json_encode(['error' => 'Current password is incorrect']);
                }
            } else {
                echo json_encode(['error' => 'Agency not found']);
            }
        } else {
            echo json_encode(['error' => 'Failed to execute query', 'sql_error' => mysqli_error($conn)]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['error' => 'Failed to prepare query', 'sql_error' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

mysqli_close($conn);
?>
