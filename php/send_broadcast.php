<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get alert data from POST request
$alertType = isset($_POST['alertType']) ? $_POST['alertType'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';

// Validate input
if (empty($alertType) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Alert type and message are required']);
    exit;
}

// Format alert type for email subject
$alertTypeFormatted = ucfirst(str_replace('_', ' ', $alertType));

// Set timezone to IST (Asia/Kolkata)
date_default_timezone_set('Asia/Kolkata');
$current_time = date('Y-m-d H:i:s');

// Email details
$to = "ankanbasu1234@gmail.com";
$subject = "RESCUE Emergency Alert: " . $alertTypeFormatted;
$email_message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>' .
    'body{background:#f0f4ff;font-family:Segoe UI,Roboto,Poppins,sans-serif;margin:0;padding:0;}' .
    '.broadcast-container{background:#fff;border-radius:22px;box-shadow:0 10px 32px rgba(56,189,248,0.18);padding:38px 30px;margin:40px auto;max-width:500px;}' .
    '.broadcast-title{font-size:26px;font-weight:900;color:#0ea5e9;text-align:center;margin-bottom:22px;letter-spacing:1.5px;text-shadow:0 2px 12px #38bdf8;}' .
    '.broadcast-table{width:100%;border-collapse:separate;border-spacing:0 16px;}' .
    '.broadcast-label{color:#0f172a;font-weight:700;text-align:right;padding-right:15px;vertical-align:middle;width:120px;font-size:16px;}' .
    '.broadcast-value{padding-left:0;vertical-align:middle;}' .
    '.broadcast-pill{background:linear-gradient(90deg,#0ea5e9,#22d3ee,#a7f3d0);color:#0f172a;padding:7px 22px;border-radius:14px;display:inline-block;font-weight:800;box-shadow:0 4px 18px #38bdf844;font-size:15px;letter-spacing:0.5px;}' .
    '.broadcast-footer{text-align:center;font-size:13px;color:#64748b;margin-top:28px;letter-spacing:0.5px;}' .
    '.broadcast-anim{display:flex;align-items:center;justify-content:center;margin-bottom:24px;}' .
    '.broadcast-spin{width:48px;height:48px;margin-right:14px;display:flex;align-items:center;justify-content:center;}' .
    '.broadcast-spin svg{width:100%;height:100%;animation:spinAnim 1.1s linear infinite;color:#22d3ee;}' .
    '@keyframes spinAnim{0%{transform:rotate(0);}100%{transform:rotate(360deg);}}' .
    '</style></head><body>' .
    '<div class="broadcast-container">' .
    '<div class="broadcast-anim"><span class="broadcast-spin"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" opacity="0.3"/><path stroke-linecap="round" stroke-width="3" d="M12 2a10 10 0 0 1 10 10"/></svg></span><span class="broadcast-title">BROADCAST ALERT</span></div>' .
    '<table class="broadcast-table">' .
    '<tr><td class="broadcast-label">Alert Type:</td><td class="broadcast-value"><span class="broadcast-pill">' . htmlspecialchars($alertTypeFormatted) . '</span></td></tr>' .
    '<tr><td class="broadcast-label">Message:</td><td class="broadcast-value"><span class="broadcast-pill">' . nl2br(htmlspecialchars($message)) . '</span></td></tr>' .
    '<tr><td class="broadcast-label">Time:</td><td class="broadcast-value"><span class="broadcast-pill">' . $current_time . '</span></td></tr>' .
    '</table>' .
    '<div class="broadcast-footer">This is an <b>automated broadcast</b> from the RESCUE Emergency Coordination System.</div>' .
    '</div></body></html>';
$headers = "MIME-Version: 1.0\r\n" .
           "Content-type: text/html; charset=UTF-8\r\n" .
           "From: rescue-system@lpu.co.in";

// Log attempt
file_put_contents('broadcast_log.txt', date('Y-m-d H:i:s') . " - Attempting to send broadcast alert: " . $alertTypeFormatted . "\n", FILE_APPEND);

// Send email
if(mail($to, $subject, $email_message, $headers)) {
    file_put_contents('broadcast_log.txt', date('Y-m-d H:i:s') . " - Email sent successfully\n", FILE_APPEND);
    echo json_encode(['status' => 'success', 'message' => 'Alert broadcast successfully sent to all agencies']);
} else {
    file_put_contents('broadcast_log.txt', date('Y-m-d H:i:s') . " - Failed to send email\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Failed to send alert broadcast']);
}
?>
