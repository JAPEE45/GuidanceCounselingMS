<?php
// Email Configuration
// Update these settings with your email credentials

return [
    'smtp_host' => 'smtp.gmail.com',  // Gmail SMTP server
    'smtp_port' => 587,                // TLS port
    'smtp_username' => 'your-email@gmail.com',  // YOUR Gmail address
    'smtp_password' => 'your-app-password',     // YOUR Gmail App Password
    'from_email' => 'your-email@gmail.com',     // From email address
    'from_name' => 'Guidance Counseling System', // From name
];

/*
 * HOW TO GET GMAIL APP PASSWORD:
 * 1. Go to your Google Account: https://myaccount.google.com/
 * 2. Click on "Security" in the left menu
 * 3. Enable "2-Step Verification" if not already enabled
 * 4. After enabling 2-Step, go back to Security
 * 5. Click on "App passwords" (you'll see this after 2-Step is enabled)
 * 6. Select "Mail" and "Windows Computer"
 * 7. Click "Generate"
 * 8. Copy the 16-character password and paste it above
 * 
 * NOTE: Use the App Password, NOT your regular Gmail password!
 */
?>
