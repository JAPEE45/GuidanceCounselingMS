# Forgot Password System - Setup & Testing Guide

## Quick Setup (3 Steps)

### Step 1: Create the Database Table
Visit this URL in your browser:
```
http://localhost/GuidanceCounselingMS/create_password_resets_table.php
```
This will create the `password_resets` table in your database.

### Step 2: Verify Everything Works
Visit this URL to run automated tests:
```
http://localhost/GuidanceCounselingMS/test_forgot_password.php
```
All tests should show **PASS** status.

### Step 3: Test the Feature
1. Go to: `http://localhost/GuidanceCounselingMS/index.php`
2. Click "Forgot password?"
3. Enter a registered email address
4. Check your email for the 6-digit verification code
5. Enter the code and create a new password
6. Login with your new password

---

## How It Works

### User Flow:
```
1. User clicks "Forgot Password" → Modal opens
2. User enters email → System sends 6-digit code via email
3. User enters code → System validates from database
4. User creates new password → Password is hashed and saved
5. User can now login with new password
```

### Security Features:
- ✅ **Database Storage**: Codes stored in database, not sessions
- ✅ **Code Expiry**: Codes expire after 15 minutes
- ✅ **Single Use**: Codes marked as "used" after password reset
- ✅ **Auto Invalidation**: Old codes invalidated when new code requested
- ✅ **Password Hashing**: Passwords stored using bcrypt hashing
- ✅ **Email Verification**: Must have access to registered email
- ✅ **Unregistered Users**: Clear message if email not found

---

## Database Structure

### Table: `password_resets`
```sql
CREATE TABLE `password_resets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `email` varchar(255) NOT NULL,
    `reset_code` varchar(10) NOT NULL,
    `expires_at` datetime NOT NULL,
    `used` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `email` (`email`),
    KEY `reset_code` (`reset_code`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
)
```

---

## Testing Checklist

- [ ] Database table `password_resets` created
- [ ] Email configuration set in `config/email.php`
- [ ] All tests pass in `test_forgot_password.php`
- [ ] Can send verification code to registered email
- [ ] Code received via email
- [ ] Can verify code successfully
- [ ] Can reset password
- [ ] Can login with new password
- [ ] Old password no longer works
- [ ] Unregistered email shows error message
- [ ] Expired codes are rejected
- [ ] Used codes cannot be reused

---

## Common Issues & Solutions

### Issue 1: "password_resets table not found"
**Solution**: Run `create_password_resets_table.php`

### Issue 2: "Failed to send email"
**Solution**: 
- Check `config/email.php` settings
- Verify Gmail credentials are correct
- Ensure app password is used (not regular password)
- Check internet connection

### Issue 3: "Session expired"
**Solution**: Complete the process within the time limit or start over

### Issue 4: "Code expired"
**Solution**: Request a new code using the "Resend Code" button

### Issue 5: New password not working
**Solution**: 
- Clear browser cache
- Try different browser
- Check if password was actually saved in database

---

## Email Configuration

Make sure `config/email.php` has valid credentials:

```php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'gcounseling27@gmail.com',  // Your Gmail
    'smtp_password' => 'lmgs rxkv tydd veqf',      // Your App Password
    'from_email' => 'gcounseling27@gmail.com',
    'from_name' => 'Guidance Counseling System',
];
```

---

## Password Requirements

- Minimum length: **6 characters**
- Must match confirmation password
- Will be hashed before storage
- Cannot be the same as verification code

---

## Support URLs

- **Login Page**: `http://localhost/GuidanceCounselingMS/index.php`
- **Create Table**: `http://localhost/GuidanceCounselingMS/create_password_resets_table.php`
- **Run Tests**: `http://localhost/GuidanceCounselingMS/test_forgot_password.php`

---

## Files Modified/Created

### Core Files:
- `forgot_password.php` - Main password reset handler
- `config/email_helper.php` - Email sending function
- `assets/scripts/index.js` - Frontend JavaScript
- `index.php` - Login page with forgot password modal

### Setup Files:
- `create_password_resets_table.php` - Creates database table
- `test_forgot_password.php` - Tests all functionality

### Documentation:
- `FORGOT_PASSWORD_GUIDE.md` - This file

---

## Status: ✅ FULLY FUNCTIONAL

The forgot password system is complete and includes:
- Database-backed verification codes
- Email notifications with beautiful HTML templates
- Code expiry and validation
- Security best practices
- Error handling and user feedback
- Comprehensive testing tools
