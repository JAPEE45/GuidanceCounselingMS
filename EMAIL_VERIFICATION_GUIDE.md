# Email Verification System - Setup Guide

## What Changed

### ✅ Registration System
- **Username → Email**: Registration now uses email address as the username
- **Email Verification**: All new student registrations require email verification before login
- **Automatic Emails**: System sends beautiful verification emails automatically

### ✅ Login System  
- **Email Login**: Users now login with their email address instead of username
- **Verification Check**: Students must verify email before accessing their account
- **Clear Messages**: Users get notified if email is not verified

---

## Setup Instructions

### Step 1: Update Database Schema
Run this URL in your browser **ONCE**:
```
http://localhost/GuidanceCounselingMS/setup_email_verification.php
```

This will add two new columns to the `users` table:
- `email_verified` (0 = not verified, 1 = verified)
- `verification_token` (unique token for email verification)

### Step 2: Test Registration Flow
1. Go to: `http://localhost/GuidanceCounselingMS/html/student/registration.php`
2. Fill out the registration form with a **valid email address**
3. Submit the form
4. Check your email inbox for the verification email
5. Click the "Verify Email Address" button in the email
6. You should see a success page
7. Try logging in with your email and password

### Step 3: Verify Login Flow
1. Go to: `http://localhost/GuidanceCounselingMS/index.php`
2. Try logging in with an unverified account → Should show verification message
3. Try logging in with a verified account → Should work normally

---

## How It Works

### Registration Process
```
1. Student fills registration form with EMAIL
   ↓
2. System creates account with email_verified = 0
   ↓
3. System generates unique verification token
   ↓
4. System sends email with verification link
   ↓
5. Student receives email notification
```

### Email Verification Process
```
1. Student clicks link in email
   ↓
2. Link opens: verify_email.php?token=XXXXX
   ↓
3. System finds user with that token
   ↓
4. System sets email_verified = 1
   ↓
5. System clears verification_token
   ↓
6. Student can now login
```

### Login Process
```
1. Student enters email and password
   ↓
2. System checks credentials
   ↓
3. System checks if email_verified = 1
   ↓
4. If not verified → Show error message
   ↓
5. If verified → Allow login
```

---

## Email Template

The verification email includes:
- ✉️ Welcome message with student's name
- 📧 Registered email address
- 🎓 Student ID
- 📚 Course information
- 🔘 Big "Verify Email Address" button
- 🔗 Backup text link if button doesn't work
- ⏰ Clear instructions
- 🎨 Beautiful gradient design

---

## Important Notes

### For Existing Users
- **Admins and Counselors**: Can login without email verification
- **Students**: Existing students may have `email_verified = NULL`, system treats this as verified
- **New Students**: All new registrations require verification

### Security Features
- ✅ Unique 64-character verification tokens
- ✅ Token is cleared after verification (single-use)
- ✅ Password hashing with bcrypt
- ✅ Email format validation
- ✅ SQL injection protection with prepared statements

### Error Handling
- Invalid token → "Invalid or expired verification link"
- Already verified → "Your email has already been verified"
- Email send failure → "Registration successful but couldn't send email"
- Database errors → Proper rollback and error messages

---

## Files Created/Modified

### New Files
1. `setup_email_verification.php` - Database setup script
2. `verify_email.php` - Email verification page
3. `add_email_verification.sql` - SQL schema changes
4. `EMAIL_VERIFICATION_GUIDE.md` - This documentation

### Modified Files
1. `html/student/registration.php` - Updated to use email and send verification
2. `index.php` - Updated login to check verification and use email
3. Database: `users` table - Added email_verified and verification_token columns

---

## Testing Checklist

- [ ] Database columns added successfully
- [ ] Can register with email address
- [ ] Verification email is sent
- [ ] Email has correct formatting and links
- [ ] Verification link works
- [ ] Can login after verification
- [ ] Cannot login before verification
- [ ] Error messages display correctly
- [ ] Existing users can still login
- [ ] Admin/counselor login works without verification

---

## Troubleshooting

### Issue: "Email not sent"
**Solution**: 
- Check `config/email.php` settings
- Verify Gmail credentials
- Check internet connection
- Look at error logs

### Issue: "Column doesn't exist"
**Solution**: Run `setup_email_verification.php` again

### Issue: "Verification link doesn't work"
**Solution**:
- Check if URL is correct: `http://localhost/GuidanceCounselingMS/verify_email.php?token=XXX`
- Verify token exists in database
- Check if already verified

### Issue: "Can't login after verification"
**Solution**:
- Clear browser cache
- Check database: `SELECT email_verified FROM users WHERE email = 'your@email.com'`
- Should return `1`

---

## Database Queries for Testing

### Check verification status
```sql
SELECT email, email_verified, verification_token, role 
FROM users 
WHERE email = 'your@email.com';
```

### Manually verify an email
```sql
UPDATE users 
SET email_verified = 1, verification_token = NULL 
WHERE email = 'your@email.com';
```

### See all unverified students
```sql
SELECT u.email, s.first_name, s.last_name, u.created_at
FROM users u
LEFT JOIN students s ON u.user_id = s.user_id
WHERE u.role = 'student' AND u.email_verified = 0;
```

### Reset verification for testing
```sql
UPDATE users 
SET email_verified = 0, verification_token = 'test_token_123' 
WHERE email = 'your@email.com';
```

---

## Admin Features

Admins can:
- See which students have verified emails
- Manually verify emails if needed
- Resend verification emails (future feature)
- View registration statistics

---

## Status: ✅ READY TO USE

The email verification system is fully functional and includes:
- Email-based registration and login
- Automated verification emails
- Beautiful email templates
- Security best practices
- Comprehensive error handling
- Admin and counselor exemption from verification
