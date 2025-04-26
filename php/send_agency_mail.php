<?php
// Send mail to ankanbasu1234@gmail.com with agency info and assignment/contact message
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$agencyName = isset($_POST['agency_name']) ? trim($_POST['agency_name']) : '';
$agencyPhone = isset($_POST['agency_phone']) ? trim($_POST['agency_phone']) : '';
$actionType = isset($_POST['action_type']) ? trim($_POST['action_type']) : '';

if (empty($agencyName) || empty($agencyPhone) || empty($actionType)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$to = 'ankanbasu1234@gmail.com';
$subject = '🚨 RESCUE SYSTEM: Agency ' . ucfirst($actionType);
$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>' .
    'body{font-family:Poppins,sans-serif;background:#f4f8fb;margin:0;padding:0;}' .
    '.mail-container{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(30,64,175,0.13);padding:32px 24px;margin:40px auto;max-width:420px;}' .
    '.mail-title{color:#2563eb;font-size:24px;font-weight:700;margin-bottom:8px;text-align:center;letter-spacing:1px;}' .
    '.mail-info{font-size:16px;color:#1e293b;margin:8px 0 16px 0;text-align:center;}' .
    '.mail-highlight{background:linear-gradient(90deg,#3b82f6,#06b6d4);color:#fff;padding:4px 12px;border-radius:8px;display:inline-block;font-weight:600;}' .
    '.mail-footer{text-align:center;font-size:13px;color:#64748b;margin-top:20px;letter-spacing:0.5px;}' .
    '</style></head><body>' .
    '<div class="mail-container">' .
    '<div class="mail-title">🚨 Emergency ' . ucfirst($actionType) . ' Notification</div>' .
    '<div class="mail-info">' .
    'Agency: <span class="mail-highlight">' . htmlspecialchars($agencyName) . '</span><br>' .
    'Phone: <span class="mail-highlight">' . htmlspecialchars($agencyPhone) . '</span><br>' .
    'Status: <span class="mail-highlight">' . ($actionType === 'assign' ? 'Assigned to Emergency' : 'Contacted for Emergency') . '</span>' .
    '</div>' .
    '<div class="mail-footer">RESCUE System Automated Notification</div>' .
    '</div></body></html>';
$headers = "MIME-Version: 1.0\r\n" .
           "Content-type: text/html; charset=UTF-8\r\n" .
           "From: RESCUE System <noreply@projectrescue.com>\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo json_encode(['status' => 'success', 'message' => 'Mail sent successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send mail']);
}
?>
