<?php
$to = "ankanbasu1234@gmail.com";
$subject = "Your Forgot Password";
$message = "Your password is: test123";
$headers = "From: noreply@yourdomain.com\r\n";

if(mail($to, $subject, $message, $headers)){
    echo "success";
} else {
    echo "fail";
}
?>
