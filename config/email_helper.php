<?php
/**
 * Send email using PHPMailer
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $htmlMessage Email body (HTML)
 * @param string $fromName Optional sender name (defaults to system name)
 */

// Include PHPMailer classes at the top level
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $htmlMessage, $fromName = null) {
    $emailConfig = require __DIR__ . '/email.php';
    
    // Use provided sender name or default to config
    $senderName = $fromName ?? $emailConfig['from_name'];
    
    // Check if PHPMailer is available
    $phpmailerPath = __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
    
    if (file_exists($phpmailerPath)) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $emailConfig['smtp_host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $emailConfig['smtp_username'];
            $mail->Password   = $emailConfig['smtp_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $emailConfig['smtp_port'];
            
            // Recipients - Use dynamic sender name
            $mail->setFrom($emailConfig['from_email'], $senderName);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlMessage;
            $mail->AltBody = strip_tags($htmlMessage);
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: {$mail->ErrorInfo}");
            return false;
        }
    } else {
        // Fallback to PHP mail() function (won't work on localhost without configuration)
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$senderName} <" . $emailConfig['from_email'] . ">" . "\r\n";
        
        return mail($to, $subject, $htmlMessage, $headers);
    }
}
?>
