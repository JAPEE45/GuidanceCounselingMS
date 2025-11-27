# Email Setup Guide for XAMPP

## Quick Setup (3 Steps)

### Step 1: Install Composer (if not already installed)
1. Download Composer from: https://getcomposer.org/download/
2. Run the installer
3. Restart your computer

### Step 2: Install PHPMailer
Open Command Prompt in your project folder and run:
```bash
cd C:\Users\Admin\Documents\xamp\htdocs\GuidanceCounselingMS
composer install
```

### Step 3: Configure Gmail SMTP
1. Open: `config/email.php`
2. Update these lines with YOUR Gmail credentials:
   ```php
   'smtp_username' => 'your-email@gmail.com',  // Your Gmail
   'smtp_password' => 'your-app-password',     // Your App Password
   'from_email' => 'your-email@gmail.com',
   ```

## How to Get Gmail App Password:

1. Go to: https://myaccount.google.com/
2. Click "Security" → Enable "2-Step Verification"
3. After enabling, go back to Security
4. Click "App passwords"
5. Select "Mail" and "Windows Computer"
6. Click "Generate"
7. Copy the 16-character password
8. Paste it in `config/email.php`

## Alternative: Use Without PHPMailer (Not Recommended)

If you don't want to install PHPMailer, the system will fall back to PHP's mail() function, but you need to configure XAMPP:

1. Open: `C:\xampp\php\php.ini`
2. Find and update these lines:
   ```ini
   [mail function]
   SMTP=smtp.gmail.com
   smtp_port=587
   sendmail_from=your-email@gmail.com
   sendmail_path="C:\xampp\sendmail\sendmail.exe -t"
   ```
3. Open: `C:\xampp\sendmail\sendmail.ini`
4. Update:
   ```ini
   smtp_server=smtp.gmail.com
   smtp_port=587
   auth_username=your-email@gmail.com
   auth_password=your-app-password
   force_sender=your-email@gmail.com
   ```
5. Restart Apache in XAMPP

## Testing

After setup, add a new counselor and check if the email is sent!

## Troubleshooting

- **Email not sending?** Check spam folder
- **"Less secure app" error?** Use App Password, not regular password
- **Still not working?** The password will still show in the alert box for manual delivery
