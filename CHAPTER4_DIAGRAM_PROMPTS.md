# Chapter 4 Diagram Prompts for Capstone (Revised)
**Guidance Counseling Management System (GCMS)**

This document contains accurate, ready-to-use prompts for generating professional diagrams for your Chapter 4 (System Design/Architecture). These prompts have been updated to strictly reflect the actual implemented features (Email/PHPMailer, In-App Notifications, No Referrals, No SMS).

Use these with diagram tools (Lucidchart, Draw.io, PlantUML, Mermaid, or AI diagram generators).

---

## Diagram 1: System Architecture & User Flow Diagram

### Purpose
Show the complete system architecture with components, technologies, and a typical user interaction flow (Student booking an appointment).

### Diagram Type
Combined Component Architecture + Sequence Diagram

### Prompt for Diagram Tool / Designer

```
Create a professional system architecture diagram for a Guidance Counseling Management System (GCMS) for Chapter 4 of a capstone thesis.

DIAGRAM LAYOUT:
- Split view: Left side = Component Architecture, Right side = Sequence Flow
- Use UML notation with clear component boxes and sequence lifelines
- Color scheme: Frontend (Light Blue), Backend/PHP (Green), Database (Orange), External Services (Purple)
- Export as SVG or high-resolution PNG (300 DPI minimum)

LEFT SIDE - SYSTEM COMPONENTS:

1. CLIENT TIER (Web Browsers)
   - Student Portal
     * Pages: registration.php, book-appointment.php, dashboard.php, profile.php, notifications.php
     * JavaScript: stud-book-appointment.js, stud-dashboard.js, stud-notifications.js
   - Counselor Portal
     * Pages: dashboard.php, my-schedule.php, session-management.php, student-record.php
     * JavaScript: counselor-dashboard.js, counselor-my-schedule.js
   - Admin Portal
     * Pages: dashboard.php, appointments.php, counselors.php, notifications.php
     * JavaScript: admin-dashboard.js, admin-counselors.js

2. APPLICATION TIER (PHP Backend)
   - Core Application Files
     * index.php (Login/Routing)
     * logout.php
   - Configuration Layer
     * config/database.php (PDO MySQL connection)
     * config/email.php (SMTP settings)
   - Business Logic (grouped by role)
     * Student Logic: html/student/*.php (Booking, Profile)
     * Counselor Logic: html/counselor/*.php (Session Notes, Schedule)
     * Admin Logic: html/admin/*.php (User Management, Assignment)
   - Notification Engine
     * In-App Database Triggers
     * PHPMailer Integration (lib/PHPMailer) for Email Alerts

3. DATA TIER
   - MySQL Database (gcms_db)
     * Core tables: users, students, counselors, admins, appointments, counseling_records, notifications

RIGHT SIDE - SEQUENCE FLOW: "Student Books Appointment"

Actors/Components (top to bottom lifelines):
1. Student (Web UI)
2. Frontend JS (stud-book-appointment.js)
3. PHP Backend (book-appointment.php)
4. Database (MySQL)
5. PHPMailer Service
6. Counselor (Web UI)

Sequence Steps (numbered arrows with message labels):

1. Student → Frontend JS: Selects Date/Time & Fills Wellness Form
2. Frontend JS → PHP Backend: POST /book-appointment.php (JSON Data)
3. PHP Backend → Database: INSERT INTO appointments (status='Pending')
4. Database → PHP Backend: Returns new appointment_id
5. PHP Backend → Database: INSERT INTO notifications (for Admin & Counselor)
6. PHP Backend → PHPMailer Service: Send Email Alert to Counselor
7. PHP Backend → Frontend JS: Return Success Response
8. Frontend JS → Student: Display "Appointment Pending" Confirmation
9. Counselor → Counselor Web UI: Receives In-App Notification
10. Counselor → PHP Backend: View Appointment Details
11. Counselor → Database: UPDATE appointments SET status='Confirmed'

ANNOTATIONS:
- Label key technologies: PHP 8.x, MySQL, Bootstrap 5, PHPMailer
- Note: "No SMS Gateway - Email & In-App Notifications Only"

DELIVERABLE:
Single diagram titled "GCMS System Architecture & Appointment Booking Flow"
```

---

## Diagram 2: Entity-Relationship Diagram (ERD)

### Purpose
Detailed database schema showing all entities, attributes, relationships, and cardinalities. Matches `gcms_db.sql`.

### Diagram Type
Entity-Relationship Diagram (Crow's Foot Notation)

### Prompt for Diagram Tool / Designer

```
Create a comprehensive Entity-Relationship Diagram (ERD) for the Guidance Counseling Management System (GCMS) database using Crow's Foot notation.

DATABASE NAME: gcms_db

ENTITIES & ATTRIBUTES:

1. USERS
   - user_id (PK)
   - email (Unique)
   - password (Hashed)
   - role (Enum: student, counselor, admin)
   - created_at

2. STUDENTS
   - student_id (PK)
   - user_id (FK -> users.user_id)
   - student_number (Unique)
   - first_name, last_name, middle_name
   - course, year_level, department
   - contact_number
   - physical_wellness (JSON/Text)
   - intellectual_wellness (JSON/Text)
   - environmental_wellness (JSON/Text)

3. COUNSELORS
   - counselor_id (PK)
   - user_id (FK -> users.user_id)
   - first_name, last_name
   - department, specialization
   - status (Active, Inactive)

4. ADMINS
   - admin_id (PK)
   - user_id (FK -> users.user_id)
   - first_name, last_name

5. APPOINTMENTS
   - appointment_id (PK)
   - student_id (FK -> students.student_id)
   - counselor_id (FK -> counselors.counselor_id, Nullable)
   - appointment_date, appointment_time
   - purpose
   - status (Pending, Confirmed, Completed, Cancelled)
   - counseling_concerns (Text)

6. COUNSELING_RECORDS
   - record_id (PK)
   - appointment_id (FK -> appointments.appointment_id)
   - session_notes (Text)
   - recommendations (Text)
   - attendance_status (Present, Absent)

7. NOTIFICATIONS
   - notification_id (PK)
   - user_id (FK -> users.user_id)
   - message
   - is_read (Boolean)

RELATIONSHIPS:
- users (1) ---- (0..1) students
- users (1) ---- (0..1) counselors
- users (1) ---- (0..1) admins
- students (1) ---- (0..N) appointments
- counselors (1) ---- (0..N) appointments
- appointments (1) ---- (0..1) counseling_records
- users (1) ---- (0..N) notifications

DELIVERABLE:
ERD titled "GCMS Database Schema"
```

---

## Diagram 3: Use Case Diagram

### Purpose
Illustrate system functionality from user perspective. **Revised to remove unimplemented features (SMS, Referrals, Social Login).**

### Diagram Type
UML Use Case Diagram

### Prompt for Diagram Tool / Designer

```
Create a comprehensive UML Use Case Diagram for the Guidance Counseling Management System (GCMS).

SYSTEM BOUNDARY: "Guidance Counseling Management System"

ACTORS:
1. Student (Left)
2. Counselor (Right)
3. Admin (Right)

USE CASES (Ovals):

GROUP 1: AUTHENTICATION
- Login (Email/Password)
- Logout
- Manage Profile

GROUP 2: STUDENT FUNCTIONS
- Book Appointment
  * <<include>> Fill Wellness Questionnaire
- View Appointment Status
- View Notification History

GROUP 3: COUNSELOR FUNCTIONS
- View My Schedule
- Manage Appointment Requests (Confirm/Reschedule)
- Conduct Session
  * <<include>> Record Session Notes
- View Student Records

GROUP 4: ADMIN FUNCTIONS
- Manage Counselors (Add/Edit/Deactivate)
- View System Stats (Dashboard)
- Monitor All Appointments
- Assign Counselor to Appointment

RELATIONSHIPS:
- Student --> Login, Book Appointment, View Status, Manage Profile
- Counselor --> Login, View Schedule, Conduct Session, Manage Requests
- Admin --> Login, Manage Counselors, View Stats, Monitor Appointments

NOTES:
- No "Referrals" use case.
- No "SMS" use case.
- No "System Configuration" use case (handled via database/code).

DELIVERABLE:
Use Case Diagram titled "GCMS System Functionality"
```

---

**Document Version**: 2.0 (Revised)
**Date**: November 27, 2025
**Status**: Aligned with actual PHP/MySQL implementation.