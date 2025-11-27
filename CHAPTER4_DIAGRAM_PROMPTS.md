# Chapter 4 Diagram Prompts for Capstone
**Guidance Counseling Management System (GCMS)**

This document contains accurate, ready-to-use prompts for generating professional diagrams for your Chapter 4 (System Design/Architecture). Use these with diagram tools (Lucidchart, Draw.io, PlantUML, Mermaid, or AI diagram generators).

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
     * JavaScript: stud-registration.js, stud-book-appointment.js, stud-dashboard.js, stud-profile.js, stud-notifications.js
   - Counselor Portal
     * Pages: dashboard.php, my-schedule.php, session-management.php, student-record.php, notifications.php, profile.php
     * JavaScript: counselor-dashboard.js, counselor-my-schedule.js, counselor-student-record.js
   - Admin Portal
     * Pages: dashboard.php, appointments.php, counselors.php, notifications.php, profile.php
     * JavaScript: admin-dashboard.js, admin-appointments.js, admin-counselors.js, admin-notifications.js

2. APPLICATION TIER (PHP Backend)
   - Core Application Files
     * index.php (main entry point)
     * logout.php
     * forgot_password.php
     * create_counselor.php
     * create_admin.php
   - Configuration Layer
     * config/database.php (MySQL connection)
     * config/email.php (email configuration)
     * config/email_helper.php (email utilities)
   - API Endpoints (grouped by role)
     * Student APIs: html/student/*.php (booking, profile updates, notifications)
     * Counselor APIs: html/counselor/*.php (schedule management, session notes, status updates)
     * Admin APIs: html/admin/*.php (counselor management, appointment assignments, system oversight)
   - Notification Engine
     * Internal notification system (NOTIFICATION_SYSTEM_IMPLEMENTED.md)
     * Mark notification read handlers
   - Email Service Integration
     * lib/PHPMailer library (password resets, appointment confirmations)
   - Frontend Assets
     * assets/styles/ (CSS with component-based architecture)
     * assets/scripts/ (client-side JavaScript modules)

3. DATA TIER
   - MySQL Database (gcms_db)
     * Core tables: users, students, counselors, admins, appointments, counseling_records, notifications, system_settings
     * Schema files: database_schema.sql, update_appointments_schema.sql, add_missing_columns.sql

RIGHT SIDE - SEQUENCE FLOW: "Student Books Appointment"

Actors/Components (top to bottom lifelines):
1. Student (Web UI)
2. Frontend JS (stud-book-appointment.js)
3. PHP Backend (book-appointment.php)
4. Database (MySQL)
5. Notification Engine
6. Email Service (PHPMailer)
7. Counselor (Web UI)

Sequence Steps (numbered arrows with message labels):

1. Student → Frontend JS: Fills appointment form (date, time, purpose, consent, medical info)
2. Frontend JS → Frontend JS: Validates form data (date format, required fields)
3. Frontend JS → PHP Backend: POST /html/student/book-appointment.php {student_id, date, time, purpose, concerns, medical_conditions, consent}
4. PHP Backend → PHP Backend: Authenticate session, validate student_id
5. PHP Backend → Database: INSERT INTO appointments (student_id, appointment_date, appointment_time, purpose, counseling_concerns, medical_conditions, consent_acknowledged, status='Pending')
6. Database → PHP Backend: Return appointment_id
7. PHP Backend → Database: INSERT INTO notifications (user_id=counselor_admin, message='New appointment request from [Student Name]', is_read=FALSE)
8. PHP Backend → Notification Engine: Trigger in-app notification
9. PHP Backend → Email Service: Send email notification (if configured) to assigned counselor/admin
10. Email Service → Counselor: Email notification sent
11. PHP Backend → Frontend JS: JSON response {success: true, appointment_id: X, message: 'Appointment booked successfully'}
12. Frontend JS → Student: Display success message + redirect to dashboard
13. Counselor → Counselor Web UI: Sees new notification badge on html/counselor/notifications.php
14. Counselor → PHP Backend: GET appointment details via html/admin/get_appointment_details.php
15. Counselor → PHP Backend: POST update status to 'Confirmed' via html/counselor/update_appointment_status.php
16. PHP Backend → Database: UPDATE appointments SET status='Confirmed' WHERE appointment_id=X
17. PHP Backend → Database: INSERT INTO notifications (user_id=student_id, message='Appointment confirmed by counselor')
18. PHP Backend → Student: Student receives confirmation notification

ANNOTATIONS:
- Add authentication boundary (dotted box around protected endpoints)
- Label key technologies: PHP 7.4+, MySQL 5.7+, PHPMailer 6.x, Vanilla JavaScript
- Show HTTP methods (GET/POST) on request arrows
- Include file paths as small labels under component boxes
- Add note: "All passwords hashed with password_hash(), sessions managed server-side"

DELIVERABLE:
Single diagram titled "GCMS System Architecture & Appointment Booking Flow"
```

---

## Diagram 2: Entity-Relationship Diagram (ERD)

### Purpose
Detailed database schema showing all entities, attributes, relationships, and cardinalities.

### Diagram Type
Entity-Relationship Diagram (Crow's Foot Notation)

### Prompt for Diagram Tool / Designer

```
Create a comprehensive Entity-Relationship Diagram (ERD) for the Guidance Counseling Management System (GCMS) database using Crow's Foot notation for Chapter 4 of a capstone thesis.

DATABASE NAME: gcms_db

ENTITIES & ATTRIBUTES (PK = Primary Key, FK = Foreign Key, U = Unique):

1. USERS
   - user_id: INT, PK, AUTO_INCREMENT
   - email: VARCHAR(255), NOT NULL, U
   - password: VARCHAR(255), NOT NULL [hashed]
   - role: ENUM('student', 'counselor', 'admin'), NOT NULL
   - created_at: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP
   - updated_at: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE

2. STUDENTS
   - student_id: INT, PK, AUTO_INCREMENT
   - user_id: INT, FK → users(user_id), NOT NULL
   - student_number: VARCHAR(50), NOT NULL, U
   - first_name: VARCHAR(100), NOT NULL
   - middle_name: VARCHAR(100), NULL
   - last_name: VARCHAR(100), NOT NULL
   - age: INT
   - gender: ENUM('Male', 'Female', 'Other')
   - birthday: DATE
   - course: VARCHAR(100)
   - year_level: VARCHAR(50)
   - department: VARCHAR(100)
   - contact_number: VARCHAR(20)
   - address: TEXT
   - physical_wellness: TEXT
   - intellectual_wellness: TEXT
   - environmental_wellness: TEXT

3. COUNSELORS
   - counselor_id: INT, PK, AUTO_INCREMENT
   - user_id: INT, FK → users(user_id), NOT NULL
   - first_name: VARCHAR(100), NOT NULL
   - last_name: VARCHAR(100), NOT NULL
   - department: VARCHAR(100)
   - specialization: VARCHAR(100)
   - contact_number: VARCHAR(20)
   - status: ENUM('Active', 'Inactive'), DEFAULT 'Active'

4. ADMINS
   - admin_id: INT, PK, AUTO_INCREMENT
   - user_id: INT, FK → users(user_id), NOT NULL
   - first_name: VARCHAR(100), NOT NULL
   - last_name: VARCHAR(100), NOT NULL

5. APPOINTMENTS
   - appointment_id: INT, PK, AUTO_INCREMENT
   - student_id: INT, FK → students(student_id), NOT NULL
   - counselor_id: INT, FK → counselors(counselor_id), NULL [assigned later]
   - appointment_date: DATE, NOT NULL
   - appointment_time: TIME, NOT NULL
   - purpose: VARCHAR(255)
   - is_minor: BOOLEAN, DEFAULT FALSE
   - parent_guardian_name: VARCHAR(200)
   - parent_guardian_contact: VARCHAR(20)
   - counseling_concerns: TEXT
   - current_medications: TEXT
   - medical_conditions: TEXT
   - student_address: TEXT
   - consent_acknowledged: BOOLEAN, DEFAULT FALSE
   - status: ENUM('Pending', 'Confirmed', 'Declined', 'Rescheduled', 'Completed', 'Cancelled'), DEFAULT 'Pending'
   - created_at: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP
   - updated_at: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE

6. COUNSELING_RECORDS
   - record_id: INT, PK, AUTO_INCREMENT
   - appointment_id: INT, FK → appointments(appointment_id), NOT NULL
   - session_notes: TEXT
   - recommendations: TEXT
   - observations: TEXT
   - attendance_status: ENUM('Present', 'Absent', 'Excused'), DEFAULT 'Present'
   - created_at: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP
   - updated_at: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE

7. NOTIFICATIONS
   - notification_id: INT, PK, AUTO_INCREMENT
   - user_id: INT, FK → users(user_id), NOT NULL
   - message: TEXT, NOT NULL
   - is_read: BOOLEAN, DEFAULT FALSE
   - created_at: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP

8. SYSTEM_SETTINGS
   - setting_id: INT, PK, AUTO_INCREMENT
   - setting_key: VARCHAR(100), NOT NULL, U
   - setting_value: TEXT
   - updated_at: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE

RELATIONSHIPS & CARDINALITIES:

1. users (1) ──< students (Many) [One-to-Many]
   - One user account can have one student profile
   - FK: students.user_id → users.user_id
   - ON DELETE CASCADE

2. users (1) ──< counselors (Many) [One-to-Many]
   - One user account can have one counselor profile
   - FK: counselors.user_id → users.user_id
   - ON DELETE CASCADE

3. users (1) ──< admins (Many) [One-to-Many]
   - One user account can have one admin profile
   - FK: admins.user_id → users.user_id
   - ON DELETE CASCADE

4. students (1) ──< appointments (Many) [One-to-Many]
   - One student can book many appointments
   - FK: appointments.student_id → students.student_id
   - ON DELETE CASCADE

5. counselors (1) ──< appointments (Many) [One-to-Many, Optional]
   - One counselor can be assigned to many appointments
   - Appointment can exist without counselor initially (NULL allowed)
   - FK: appointments.counselor_id → counselors.counselor_id
   - ON DELETE SET NULL

6. appointments (1) ──< counseling_records (Many) [One-to-Many]
   - One appointment can have one or more counseling session records
   - FK: counseling_records.appointment_id → appointments.appointment_id
   - ON DELETE CASCADE

7. users (1) ──< notifications (Many) [One-to-Many]
   - One user can receive many notifications
   - FK: notifications.user_id → users.user_id
   - ON DELETE CASCADE

DIAGRAM SPECIFICATIONS:
- Use Crow's Foot notation (○ = zero, | = one, < = many)
- Show relationship lines with cardinality symbols on both ends
- Color-code entity boxes: Core entities (Blue), Profile entities (Green), Transactional entities (Orange), System entities (Gray)
- Include legend explaining: PK (key icon), FK (arrow icon), NOT NULL (asterisk), UNIQUE (U)
- Group related entities visually: User Management (users, students, counselors, admins), Appointment Management (appointments, counseling_records), System Support (notifications, system_settings)
- Add brief notes: "ON DELETE CASCADE" near relevant FK lines, "Initially NULL" near appointments.counselor_id
- Export as SVG or high-resolution PNG (300 DPI)

DELIVERABLE:
ERD titled "GCMS Database Schema - Entity-Relationship Diagram"

SOURCE DOCUMENTATION:
Based on database_schema.sql, update_appointments_schema.sql, add_missing_columns.sql from GCMS repository (November 2025)
```

---

## Usage Instructions

### For AI Diagram Generators (ChatGPT, Claude with diagram plugins, etc.)
1. Copy the entire prompt (including all specifications)
2. Paste into the AI tool with a diagram generation capability
3. Request: "Generate this diagram using [PlantUML/Mermaid/DrawIO XML]"
4. Export to PNG/SVG for your thesis document

### For Manual Diagram Creation (Lucidchart, Draw.io, Visio)
1. Use the prompts as detailed specifications
2. Follow the entity/component lists exactly
3. Implement the color scheme and notation specified
4. Verify all relationships and cardinalities match the schema

### For PlantUML (Recommended for ERD)
- The ERD prompt can be converted to PlantUML syntax
- Use `@startuml` and `@enduml` tags
- Entities become `entity "NAME"`
- Relationships use `--`, `||--o{`, etc. for cardinality

### For Mermaid (Recommended for Architecture)
- Architecture diagram can use `graph LR` or `flowchart TB`
- Sequence diagram uses `sequenceDiagram`
- Both can be embedded in Markdown or exported via Mermaid Live Editor

### Export Settings for Thesis
- **Format**: SVG (vector, scalable) + PNG backup (300 DPI)
- **Size**: A4 landscape or larger (1920x1080 minimum for PNG)
- **Fonts**: Arial or Helvetica, minimum 10pt for readability
- **Colors**: High contrast, printer-friendly (avoid pure red/green for accessibility)

### Quality Checklist
✅ All entities from database schema included  
✅ All relationships show correct cardinality  
✅ Foreign keys clearly marked  
✅ Component file paths referenced  
✅ Sequence flow matches actual system behavior  
✅ Legends and annotations present  
✅ Professional formatting (aligned, clear labels)  
✅ High resolution for printing  

---

## Diagram 3: Use Case Diagram

### Purpose
Illustrate system functionality from user perspective, showing all actors and their interactions with the system.

### Diagram Type
UML Use Case Diagram

### Prompt for Diagram Tool / Designer

```
Create a comprehensive UML Use Case Diagram for the Guidance Counseling Management System (GCMS) for Chapter 4 of a capstone thesis.

SYSTEM BOUNDARY:
- Rectangle labeled "Guidance Counseling Management System (GCMS)"
- All use cases contained within the boundary
- Actors positioned outside the boundary

ACTORS (stick figures outside system boundary):

1. Student
   - Primary user who books appointments and manages their profile
   - Position: Left side of diagram

2. Counselor
   - Manages schedules, conducts sessions, maintains records
   - Position: Top-right of diagram

3. Admin
   - System administrator with oversight and management capabilities
   - Position: Bottom-right of diagram

4. Email System (optional, shown as external system box)
   - Automated email notifications
   - Position: Right side, outside main boundary

USE CASES (ovals inside system boundary):

AUTHENTICATION & PROFILE MANAGEMENT:
1. Register Account
   - Actor: Student
   - Description: Create new student account with credentials

2. Login to System
   - Actors: Student, Counselor, Admin
   - Description: Authenticate and access role-specific dashboard

3. Logout
   - Actors: Student, Counselor, Admin
   - Description: End session securely

4. Reset Password
   - Actors: Student, Counselor, Admin
   - Description: Request password reset via email
   - <<extends>> Receive Email Notification

5. Update Profile
   - Actors: Student, Counselor, Admin
   - Description: Modify personal information, contact details

APPOINTMENT MANAGEMENT (Student):
6. Book Appointment
   - Actor: Student
   - Description: Schedule counseling session with date/time/purpose
   - <<includes>> Select Available Time Slot
   - <<includes>> Provide Medical Information
   - <<includes>> Acknowledge Consent Form
   - Triggers: Notify Counselor/Admin

7. View Appointment Status
   - Actor: Student
   - Description: Check pending/confirmed/completed appointments

8. Cancel Appointment
   - Actor: Student
   - Description: Cancel scheduled appointment
   - Triggers: Notify Counselor/Admin

9. View Appointment History
   - Actor: Student
   - Description: Review past counseling sessions

APPOINTMENT MANAGEMENT (Counselor):
10. View Appointment Requests
    - Actor: Counselor
    - Description: See pending appointment bookings

11. Confirm Appointment
    - Actor: Counselor
    - Description: Accept and confirm student appointment
    - Triggers: Notify Student

12. Decline Appointment
    - Actor: Counselor
    - Description: Reject appointment request with reason
    - Triggers: Notify Student

13. Reschedule Appointment
    - Actor: Counselor
    - Description: Propose new date/time for appointment
    - Triggers: Notify Student

14. View My Schedule
    - Actor: Counselor
    - Description: Calendar view of all appointments

15. Manage Session
    - Actor: Counselor
    - Description: Conduct and document counseling session
    - <<includes>> Record Session Notes
    - <<includes>> Add Recommendations
    - <<includes>> Mark Attendance Status

16. View Student Records
    - Actor: Counselor
    - Description: Access student counseling history and wellness data

17. Complete Appointment
    - Actor: Counselor
    - Description: Mark appointment as completed after session

APPOINTMENT MANAGEMENT (Admin):
18. Assign Counselor
    - Actor: Admin
    - Description: Manually assign counselor to pending appointment
    - Triggers: Notify Counselor and Student

19. View All Appointments
    - Actor: Admin
    - Description: System-wide appointment dashboard

20. Override Appointment Status
    - Actor: Admin
    - Description: Change appointment status (administrative override)

COUNSELOR MANAGEMENT (Admin):
21. Add Counselor
    - Actor: Admin
    - Description: Create new counselor account with specialization

22. Edit Counselor Details
    - Actor: Admin
    - Description: Update counselor information, department, status

23. Deactivate Counselor
    - Actor: Admin
    - Description: Set counselor status to inactive

24. View Counselor List
    - Actor: Admin
    - Description: See all counselors with their details and workload

NOTIFICATION SYSTEM:
25. Receive Notifications
    - Actors: Student, Counselor, Admin
    - Description: Get in-app notifications for system events

26. Mark Notification as Read
    - Actors: Student, Counselor, Admin
    - Description: Clear notification badge

27. View Notification History
    - Actors: Student, Counselor, Admin
    - Description: Access past notifications

REPORTING & ANALYTICS (Admin):
28. Generate Reports
    - Actor: Admin
    - Description: Create system usage and appointment statistics

29. View Dashboard Analytics
    - Actor: Admin
    - Description: Overview of system metrics and trends

RELATIONSHIPS (use arrows and labels):

ASSOCIATIONS (solid lines):
- Student ── Book Appointment
- Student ── View Appointment Status
- Student ── Cancel Appointment
- Student ── View Appointment History
- Student ── Register Account
- Student ── Login to System
- Student ── Logout
- Student ── Reset Password
- Student ── Update Profile
- Student ── Receive Notifications
- Student ── Mark Notification as Read
- Student ── View Notification History

- Counselor ── View Appointment Requests
- Counselor ── Confirm Appointment
- Counselor ── Decline Appointment
- Counselor ── Reschedule Appointment
- Counselor ── View My Schedule
- Counselor ── Manage Session
- Counselor ── View Student Records
- Counselor ── Complete Appointment
- Counselor ── Login to System
- Counselor ── Logout
- Counselor ── Reset Password
- Counselor ── Update Profile
- Counselor ── Receive Notifications
- Counselor ── Mark Notification as Read
- Counselor ── View Notification History

- Admin ── Assign Counselor
- Admin ── View All Appointments
- Admin ── Override Appointment Status
- Admin ── Add Counselor
- Admin ── Edit Counselor Details
- Admin ── Deactivate Counselor
- Admin ── View Counselor List
- Admin ── Generate Reports
- Admin ── View Dashboard Analytics
- Admin ── Login to System
- Admin ── Logout
- Admin ── Reset Password
- Admin ── Update Profile
- Admin ── Receive Notifications
- Admin ── Mark Notification as Read
- Admin ── View Notification History

INCLUDE RELATIONSHIPS (dashed arrow with <<include>>):
- Book Appointment <<includes>> Select Available Time Slot
- Book Appointment <<includes>> Provide Medical Information
- Book Appointment <<includes>> Acknowledge Consent Form
- Manage Session <<includes>> Record Session Notes
- Manage Session <<includes>> Add Recommendations
- Manage Session <<includes>> Mark Attendance Status

EXTEND RELATIONSHIPS (dashed arrow with <<extend>>):
- Receive Email Notification <<extends>> Reset Password
- Receive Email Notification <<extends>> Book Appointment
- Receive Email Notification <<extends>> Confirm Appointment
- Receive Email Notification <<extends>> Assign Counselor

DIAGRAM SPECIFICATIONS:
- Use standard UML use case notation (ovals for use cases, stick figures for actors)
- Group related use cases visually with dashed rectangles labeled:
  * "Authentication & Profile Management"
  * "Student Appointment Functions"
  * "Counselor Appointment Functions"
  * "Admin Management Functions"
  * "Notification System"
  * "Reporting & Analytics"
- Color-code actors: Student (Blue), Counselor (Green), Admin (Orange)
- Use consistent font sizes: Actor names (12pt bold), Use case names (10pt)
- Show cardinality where relevant (1..* for multiple appointments per student)
- Add legend explaining: ──── (association), - - - -> (include), - - - -> (extend)
- Export as SVG or high-resolution PNG (300 DPI)

DELIVERABLE:
Use Case Diagram titled "GCMS Use Case Diagram - System Functionality Overview"

NOTES:
- Based on actual system features from html/student/, html/counselor/, html/admin/ directories
- Verified against implemented notification system (NOTIFICATION_SYSTEM_IMPLEMENTED.md)
- Aligned with database schema (users, appointments, notifications tables)
```

---

## Additional Diagrams (Optional for Chapter 4)

### Deployment Diagram
Show the physical deployment:
- Web Server (Apache/Nginx with PHP)
- Database Server (MySQL)
- Client Browsers
- Email Server (SMTP via PHPMailer)
- Network connections between components

---

## References
- Database Schema: `database_schema.sql`
- Schema Updates: `update_appointments_schema.sql`, `add_missing_columns.sql`
- System Documentation: `NOTIFICATION_SYSTEM_IMPLEMENTED.md`, `SYSTEM_FLOW_ANALYSIS.md`
- Email Configuration: `EMAIL_SETUP_GUIDE.md`

---

**Document Version**: 1.0  
**Date**: November 27, 2025  
**Repository**: GuidanceCounselingMS (JAPEE45)
