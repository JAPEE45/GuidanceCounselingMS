# Global Intructions:
    - DONT EDIT THE SOURCE CODE OF THIS CODEBASE
    - YOUR MY CAPSTONE RESEARCH BUDDY, WE'RE JUST USING THIS CODE BASE TO CREATE A CHAPTER 4 PAPERS
    - IF WE HAVE AN OUTPUT FILE JUST PUT IT IN ONE DIRECTORY: /OUTPUT
    Z
# Guidance Counseling Management System (GCMS)

## Project Overview
GCMS is a web-based application designed to manage guidance counseling operations. It features role-based access for Students, Counselors, and Administrators to handle appointments, session notes, profiles, and more.

**Tech Stack:**
*   **Backend:** Native PHP (no major framework).
*   **Database:** MySQL (accessed via PDO).
*   **Frontend:** HTML5, Bootstrap 5, CSS3, Vanilla JavaScript.
*   **Dependencies:** PHPMailer (for email functionality).
*   **Environment:** Designed for XAMPP (Apache/MySQL).

## Architecture
The project structure is organized by user role and functionality:

*   `html/`: Contains the main application logic, sub-divided by role:
    *   `student/`: Student-facing pages (Booking, Profile, Dashboard).
    *   `counselor/`: Counselor-facing pages (Schedule, Session Notes, Dashboard).
    *   `admin/`: Admin-facing pages (User Management, Reports).
*   `config/`: Configuration files (Database connection, Email settings).
*   `assets/`: Static resources (CSS, JS).
*   `lib/`: External libraries (PHPMailer).
*   `Root Directory`:
    *   `index.php`: The main entry point (Login page).
    *   `*.sql`: Database schema and update scripts.
    *   `*.md`: Project documentation and status reports.

## Database (`gcms_db`)
*   **Connection:** Defined in `config/database.php`.
    *   Default: `localhost`, User: `root`, Pass: `` (Empty).

### Schema Overview
The database `gcms_db` consists of the following key entities:

*   **`users`**: Central authentication table.
    *   Columns: `user_id` (PK), `email`, `password` (hashed), `role` (student, counselor, admin), `created_at`, `updated_at`.
*   **`admins`**: Administrator profiles linked to `users`.
    *   Columns: `admin_id` (PK), `user_id` (FK), `first_name`, `last_name`.
*   **`students`**: Student profiles with demographic and wellness info.
    *   Columns: `student_id` (PK), `user_id` (FK), `student_number`, `first_name`, `middle_name`, `last_name`, `age`, `gender`, `birthday`, `course`, `year_level`, `department`, `contact_number`, `address`, `physical_wellness` (JSON), `intellectual_wellness` (JSON), `environmental_wellness` (JSON).
*   **`counselors`**: Counselor profiles.
    *   Columns: `counselor_id` (PK), `user_id` (FK), `first_name`, `last_name`, `department`, `specialization`, `contact_number`, `status` (Active/Inactive).
*   **`appointments`**: Appointment requests and schedules.
    *   Columns: `appointment_id` (PK), `student_id` (FK), `counselor_id` (FK, Nullable), `appointment_date`, `appointment_time`, `purpose`, `is_minor`, `parent_guardian_name`, `parent_guardian_contact`, `counseling_concerns` (JSON/Text), `current_medications`, `medical_conditions`, `student_address`, `consent_acknowledged`, `status` (Pending, Confirmed, Declined, Rescheduled, Completed, Cancelled), `created_at`, `updated_at`.
*   **`counseling_records`**: Session notes and outcomes for appointments.
    *   Columns: `record_id` (PK), `appointment_id` (FK), `session_notes`, `recommendations`, `observations`, `attendance_status` (Present, Absent, Excused), `created_at`, `updated_at`.
*   **`notifications`**: System notifications for users.
    *   Columns: `notification_id` (PK), `user_id` (FK), `message`, `is_read`, `created_at`.
*   **`system_settings`**: Global application settings.
    *   Columns: `setting_id` (PK), `setting_key` (Unique), `setting_value`, `updated_at`.

### Schema Management
*   **Current Snapshot:** Reflected in `gcms_db.sql`.
*   **Updates:** Check `DATABASE_UPDATE_INSTRUCTIONS.md` for recent schema changes or migration steps.

## Development & Usage

### Setup
1.  Start **Apache** and **MySQL** in XAMPP.
2.  Create a database named `gcms_db`.
3.  Import the SQL files to set up the schema.
4.  Ensure `composer install` has been run if using Composer for PHPMailer, or ensure `lib/PHPMailer` is correctly populated.

### Development Conventions
*   **Database Access:** Always use the PDO connection from `config/database.php`.
*   **Security:** Use `password_verify` for logins. Sanitize inputs (though usage of prepared statements varies, stick to PDO prepared statements).
*   **Styling:** Use Bootstrap 5 utility classes. Custom styles are in `assets/styles`.
*   **Session:** `session_start()` is used for state management. Check `$_SESSION['role']` for access control.

## Current Status (as of Nov 2025)
*   **Implemented:** Authentication, Student Booking (with wellness questionnaire), Counselor Session Management, Admin User Management.
*   **In Progress/Missing:**
    *   **Notifications:** Partial implementation. Needs robust auto-triggering on events.
    *   **Counselor Assignment:** Admin cannot currently manually assign counselors.
    *   **Referrals:** System not yet implemented.
    *   **Analytics:** No reporting features yet.

## Key Files for Context
*   `SYSTEM_FLOW_ANALYSIS.md`: Detailed breakdown of implemented vs. missing features.
*   `DATABASE_UPDATE_INSTRUCTIONS.md`: Recent database changes.
*   `index.php`: Login logic and routing.

# Chapter 4: Design and Methodology (Context for Revision)

**Methodology:** Agile Methodology (Iterative development, sprints).
**Requirements Gathering:** Interviews, surveys, direct observations.

**Functional Requirements:**
1.  **Authentication & Security:** Secure login (Bcrypt), role-validated sessions.
2.  **Student Appointment & Wellness:** Booking + Wellness Questionnaire (Physical, Intellectual, Environmental).
3.  **Counselor Session Management:** View appointments, read wellness responses, record confidential Session Notes & recommendations.
4.  **Administrative Management:** User accounts, master schedule, assign counselors.
5.  **Notification System:** Automated in-system and email alerts (PHPMailer) for status updates.

**Non-Functional Requirements:**
1.  **Usability:** Responsive, Bootstrap 5.
2.  **Security:** PDO Prepared Statements, SQL injection prevention, session hijacking protection.
3.  **Reliability:** Data integrity, email delivery via mail server.
4.  **Performance:** Loading under 3 seconds, handle peak usage.

**Design Diagrams (To Revise/Discuss):**
*   **Context Diagram (Figure 2):** Central GCMS interacting with Student, Counselor, Admin.
*   **DFD Level 1 (Figure 3):** 5 Processes: 1.0 Authentication, 2.0 Appointment Management, 3.0 Session Documentation, 4.0 System Administration, 5.0 Notification System. Flows between Users -> System -> Databases (Users, Appointments, Records, Notifications).
*   **Use Case Diagram (Figure 4):**
    *   **Student:** Register/Login, Profile, Schedule Appt, View Appt Status.
    *   **Counselor:** Manage Schedule, Session Management (Record Notes, Update Records).
    *   **Admin:** User Accounts, Monitor System, Configure Settings.
*   **Activity Diagram (Figure 5):** Swimlane showing Student/Counselor/Admin flows.
*   **ERD (Figure 6):** Crow's Foot notation. `USERS` (Parent) -> `STUDENTS`/`COUNSELORS` (Child). `APPOINTMENTS` connects Students/Counselors. `RECORDS` linked 1:1 to Appointments.
*   **System Architecture (Figure 7):** 3-Tier (GUI -> Apache Web Server -> MySQL Database).

**System Prototype (Figures 8-23):**
*   **Login:** Role-based redirection (Fig 8).
*   **Student:** Registration (Fig 9), Dashboard (Fig 10), Booking with Wellness Form (Fig 11a-c), Notifications (Fig 12).
*   **Counselor:** Dashboard (Fig 13), Calendar (Fig 14), Session Manager (Fig 15 - Active/History tabs), Student Record (Fig 16), Profile (Fig 17).
*   **Admin:** Dashboard (Metrics) (Fig 18), Counselors Mgmt (Fig 19), Appointments Mgmt (Fig 20), Notifications/Assignment (Fig 22), Profile (Fig 23).

**Development & Testing:**
*   **Method:** Agile (Sprints).
*   **Evaluation:** ISO/IEC 25010 (Functional Suitability, Performance Efficiency, Compatibility, Usability, Reliability, Security, Maintainability, Portability).
*   **Data Gathering:** Interviews, observations (Dev phase); Survey questionnaire (Evaluation phase).
*   **Respondents:** 27 Total Population -> Sample of 9 (5 Students, 3 Counselors, 1 Admin).
*   **Statistical Tools:** Weighted Mean, Frequency Counts, Likert Scale (5-point).