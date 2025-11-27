# Guidance Counseling Management System (GCMS) - Full Documentation

## 1. Introduction
The **Guidance Counseling Management System (GCMS)** is a web-based application designed to streamline the operations of a school guidance office. It facilitates digital appointment booking, record-keeping, and communication between students, counselors, and administrators.

## 2. Technical Architecture

### 2.1 Technology Stack
*   **Backend:** Native PHP 8.x (No framework)
*   **Database:** MySQL (via PDO for security)
*   **Frontend:** HTML5, CSS3, Bootstrap 5, Vanilla JavaScript
*   **Email Service:** PHPMailer
*   **Server Environment:** Designed for XAMPP (Apache + MySQL)

### 2.2 Directory Structure
```
GuidanceCounselingMS/
├── config/                 # Database and Email configuration
├── assets/                 # Static resources
│   ├── styles/             # CSS files (Bootstrap overrides)
│   └── scripts/            # JavaScript files
├── html/                   # Application Logic (Views & Controllers)
│   ├── admin/              # Admin-specific pages
│   ├── counselor/          # Counselor-specific pages
│   └── student/            # Student-specific pages
├── lib/                    # External libraries (PHPMailer)
├── index.php               # Main entry point (Login)
└── *.sql                   # Database schema files
```

## 3. Database Schema (`gcms_db`)

The system relies on a relational MySQL database with the following key entities:

*   **`users`**: Central authentication table.
    *   `user_id` (PK), `email`, `password` (hashed), `role` (student/counselor/admin).
*   **`students`**: Extended profile for students.
    *   Links to `users`. Stores demographic data and wellness survey responses (`physical_wellness`, `intellectual_wellness`, etc., stored as JSON).
*   **`counselors`**: Extended profile for counselors.
    *   Links to `users`. Stores `department`, `specialization`, and `status`.
*   **`admins`**: Extended profile for administrators.
*   **`appointments`**: Core booking table.
    *   `appointment_id`, `student_id`, `counselor_id`, `appointment_date`, `appointment_time`, `purpose`, `status`.
*   **`counseling_records`**: Clinical notes for sessions.
    *   `record_id`, `appointment_id`, `session_notes`, `recommendations`.
*   **`notifications`**: In-app notification system.
    *   `user_id`, `message`, `is_read`, `created_at`.

## 4. User Roles & Workflows

### 4.1 Student
*   **Registration:** Students can self-register via `html/student/registration.php`.
*   **Booking:** 
    *   Students book appointments via `html/student/book-appointment.php`.
    *   **Wellness Check:** Includes a mandatory wellness questionnaire saved to their profile.
    *   **Trigger:** Booking triggers notifications to the Student (confirmation), all active Counselors (alert), and Admins.
*   **Dashboard:** View upcoming appointments and notifications.

### 4.2 Counselor
*   **Dashboard:** View appointment summaries and recent notifications.
*   **Schedule Management:** View assigned appointments in `html/counselor/my-schedule.php`.
*   **Session Management:** 
    *   Conduct sessions using `html/counselor/session-management.php`.
    *   View student history and wellness data.
    *   Save secure session notes (`counseling_records`).

### 4.3 Administrator
*   **User Management:** 
    *   Create/Edit/Deactivate counselors.
    *   Reset passwords.
*   **Appointment Oversight:** 
    *   View all appointments system-wide.
    *   Manually assign counselors to pending appointments (`html/admin/assign_counselor.php`).

## 5. Key Features & Logic

### 5.1 Authentication
*   Uses PHP Sessions (`session_start()`).
*   Role-based access control checks (`$_SESSION['role']`) at the top of every secure page.
*   Passwords hashed using `password_hash()` and verified with `password_verify()`.

### 5.2 Notification System
*   **Automatic Triggers:**
    *   **New Appointment:** Notifies Student (Confirm), Counselors (New Request), Admin (New Request).
    *   **Assignment:** Notifies Student (Counselor Assigned), Counselor (New Assignment).
*   **Status:** Notifications have an `is_read` flag manageable via the UI.

### 5.3 Security
*   **PDO Prepared Statements:** Used for all database queries to prevent SQL Injection.
*   **Input Sanitization:** HTML special characters escaped on output.
*   **Session Hijacking Protection:** `session_regenerate_id()` used on login.

## 6. Installation & Setup

1.  **Environment:** Install XAMPP.
2.  **Database:**
    *   Open phpMyAdmin (`http://localhost/phpmyadmin`).
    *   Create a database named `gcms_db`.
    *   Import `gcms_db.sql`.
3.  **Configuration:**
    *   Edit `config/database.php` if your MySQL credentials differ from default (`root`/``).
    *   Edit `config/email.php` to set up SMTP credentials for email features.
4.  **Dependencies:**
    *   Ensure `lib/PHPMailer` is populated (or run `composer install` if using Composer).
5.  **Run:**
    *   Place project folder in `htdocs`.
    *   Access via `http://localhost/GuidanceCounselingMS`.

## 7. Current Development Status (Nov 2025)

*   **Fully Functional:**
    *   User Authentication & Role Management.
    *   Student Appointment Booking with Wellness Data.
    *   Counselor Session Documentation.
    *   In-App Notification System.
*   **Pending Features:**
    *   Referral System (Database & UI).
    *   Advanced Analytics/Reporting.
    *   System-wide Configuration UI.
