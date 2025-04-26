<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT id, name, password FROM agencies WHERE email = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $name, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        session_start();
                        
                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["name"] = $name;
                        
                        // Log the login activity
                        $log_sql = "INSERT INTO activity_logs (agency_id, activity_type, details) VALUES (?, 'login', ?)";
                        $details = json_encode([
                            'timestamp' => time(),
                            'ip' => $_SERVER['REMOTE_ADDR'],
                            'user_agent' => $_SERVER['HTTP_USER_AGENT']
                        ]);
                        
                        if ($log_stmt = mysqli_prepare($conn, $log_sql)) {
                            mysqli_stmt_bind_param($log_stmt, "is", $id, $details);
                            mysqli_stmt_execute($log_stmt);
                            mysqli_stmt_close($log_stmt);
                        }
                        
                        // Redirect user to dashboard
                        header("location: ../agency-dashboard.html");
                        exit;
                    } else {
                        $login_err = "Invalid password.";
                    }
                }
            } else {
                $login_err = "Invalid email.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);

if (isset($login_err)) {
    // Show an alert and redirect back to login page
    echo "<script>alert('" . $login_err . "'); window.location.href='../index.html';</script>";
    exit;
}
?>
