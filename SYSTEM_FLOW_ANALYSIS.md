# System Flow Analysis - Guidance Counseling Management System

## Current Implementation Status

### ✅ IMPLEMENTED FEATURES

#### A. STUDENT FLOW
1. **Login / Registration** ✅
   - Student login via index.php
   - Registration via html/student/registration.php
   - User verification against database
   
2. **Profile Management** ✅
   - View profile: html/student/profile.php
   - Update profile: html/student/update_profile.php
   - Data stored in students table

3. **Appointment Booking** ✅ (JUST FIXED)
   - Form: html/student/book-appointment.php
   - Wellness questionnaire included
   - Saves to appointments table
   - Saves wellness data to students table

4. **Notifications** ⚠️ PARTIAL
   - View notifications: html/student/notifications.php
   - Mark as read: html/student/mark_notification_read.php
   - ❌ NOT auto-created when appointment booked

#### B. COUNSELOR FLOW
1. **Login** ✅
   - Counselor login via index.php
   - Validation against counselors table

2. **View Schedule** ✅
   - Dashboard: html/counselor/dashboard.php
   - My Schedule: html/counselor/my-schedule.php
   - Shows appointments from database

3. **Conduct Counseling Session** ✅
   - Session management: html/counselor/session-management.php
   - Student records: html/counselor/student-record.php
   - Views student profile and history

4. **Update Counseling Notes** ✅
   - Save notes: html/counselor/save_session_notes.php
   - Stores in counseling_records table

5. **Referrals** ❌ NOT IMPLEMENTED
   - No referral system exists
   - No referral database table

#### C. ADMIN FLOW
1. **Login** ✅
   - Admin login via index.php

2. **User Account Management** ✅
   - Counselor management: html/admin/counselors.php
   - Create/edit/deactivate counselors
   - Password reset functionality
   - Email credentials to new counselors

3. **System Configuration** ❌ NOT IMPLEMENTED
   - No configuration interface
   - No system_settings usage

4. **Monitoring & Reports** ⚠️ PARTIAL
   - View appointments: html/admin/appointments.php
   - ❌ No statistics/reports
   - ❌ No counselor load analysis

---

## ❌ MISSING CRITICAL FEATURES

### 1. **Notification System** (HIGH PRIORITY)
When student books appointment:
- ❌ Counselor should receive notification
- ❌ Admin should receive notification
- ❌ Student should receive confirmation

### 2. **Counselor Assignment** (HIGH PRIORITY)
- ❌ No automatic counselor assignment
- ❌ Admin cannot manually assign counselors to appointments

### 3. **Referral System** (MEDIUM PRIORITY)
- ❌ No referral database table
- ❌ No referral interface
- ❌ No admin notification for referrals

### 4. **System Configuration** (MEDIUM PRIORITY)
- ❌ No appointment time slot configuration
- ❌ No counselor availability settings
- ❌ No system policies interface

### 5. **Reports & Analytics** (LOW PRIORITY)
- ❌ No appointment statistics
- ❌ No counselor workload reports
- ❌ No student attendance tracking

---

## 🔧 IMMEDIATE FIXES NEEDED

### Priority 1: Notification System
**File to create:** `html/student/book-appointment.php` (update)
**Action:** After appointment creation, insert notifications for:
- Student (confirmation)
- All active counselors (new appointment alert)
- Admin (new appointment alert)

### Priority 2: Counselor Assignment
**File to create:** `html/admin/assign_counselor.php`
**Action:** Allow admin to assign counselors to pending appointments

### Priority 3: Database Schema Update
**Missing table:** referrals
**Action:** Add referral tracking table

---

## 📊 DATABASE USAGE ANALYSIS

### Currently Used Tables:
- ✅ users
- ✅ students
- ✅ counselors
- ✅ admins
- ✅ appointments
- ✅ counseling_records
- ✅ notifications (table exists but not auto-populated)
- ❌ system_settings (exists but unused)

### Missing Tables:
- ❌ referrals (not in schema)

---

## NEXT STEPS

1. Implement notification creation on appointment booking
2. Add counselor assignment functionality
3. Create referral system
4. Build admin reports/analytics
5. Add system configuration interface
