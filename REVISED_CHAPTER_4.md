# CHAPTER 4: DESIGN AND METHODOLOGY (Revised)

This chapter discusses the design, development, and methodology applied in creating the **Guidance Counseling Management System (GCMS)** for Catanduanes State University. It details the requirements analysis, documentation, design of software and systems, development, testing, respondents, and statistical tools used for system evaluation. The software development methodology applied in this project is the **Agile Methodology**, which emphasizes iterative development, collaboration with stakeholders, and continuous improvement.

## Requirements Analysis

The requirements were gathered through multiple techniques, including interviews with guidance counselors, surveys with students, and direct observations of existing counseling processes at CatSU. The current process is largely paper-based and relies on walk-in scheduling. This creates inefficiencies such as scheduling conflicts, difficulty in tracking student records, and manual notification delays.

The proposed system addresses these issues by introducing a web-based platform for booking appointments, managing digital counseling records, and automating notifications via email and in-system alerts.

## Requirements Documentation

The requirements for the Guidance Counseling Management System are presented in tabular form below, categorized into functional and non-functional requirements.

### Table 1. Functional Requirements

| MODULE | REQUIREMENT | DESCRIPTION |
| :--- | :--- | :--- |
| **Authentication** | Secure Login | Users (Student, Counselor, Admin) must log in using email and password. Passwords must be encrypted. |
|  | Role-Based Access | The system must redirect users to their specific dashboard (Student, Counselor, or Admin) upon login. |
| **Student Module** | Appointment Booking | Students must be able to book counseling sessions by selecting available dates/times and stating their purpose. |
|  | Wellness Questionnaire | Students must complete a wellness assessment (Physical, Intellectual, Environmental) during booking. |
|  | Profile Management | Students must be able to view and update their personal contact information. |
|  | Notifications | Students must receive confirmation alerts when their appointment is accepted. |
| **Counselor Module** | Schedule Management | Counselors must be able to view their upcoming appointments and confirm or reschedule them. |
|  | Session Management | Counselors must be able to record session notes, recommendations, and attendance status for each appointment. |
|  | Student Records | Counselors must have access to student profiles and appointment history for reference. |
| **Admin Module** | User Management | Admins must be able to add, edit, or deactivate counselor accounts. |
|  | Appointment Oversight | Admins must be able to view all appointments system-wide and assign counselors if necessary. |

### Table 2. Non-Functional Requirements

| REQUIREMENT | DESCRIPTION |
| :--- | :--- |
| **Usability** | The system should have a user-friendly interface using standard web components (Bootstrap) for ease of navigation. |
| **Security** | Passwords must be hashed (using `password_hash`). Database interactions must use prepared statements to prevent SQL injection. |
| **Performance** | The system should load pages quickly and handle multiple concurrent users without significant delay. |
| **Reliability** | The system must accurately store and retrieve data without loss, ensuring record integrity. |
| **Compatibility** | The web application must be accessible via standard web browsers (Chrome, Firefox, Edge). |

---

## Design of Software, Systems, Product and/or Processes

### Context Diagram

The Context Diagram of the GCMS illustrates the system as a central process interacting with three main external entities: the **Student**, the **Counselor**, and the **Admin**.

*   **Student:** Registers/Logs in, updates profile, requests appointments (providing wellness data), and receives notifications/confirmations.
*   **Counselor:** Logs in, views assigned schedules, manages session notes (inputs counseling records), and updates appointment status.
*   **Admin:** Logs in, manages user accounts (counselors), and oversees the master appointment list.

The system acts as the central hub, processing these inputs (bookings, notes, commands) and generating outputs (confirmations, schedule views, records) to streamline the counseling workflow.

### Data Flow Diagrams (DFD)

*   **Student Flow:** Focuses on the `Registration/Login` process (validating credentials against the `users` table) and the `Appointment Booking` process (saving request data to the `appointments` table and wellness answers to the `students` table).
*   **Counselor Flow:** Focuses on `Schedule Retrieval` (querying the `appointments` table) and `Session Documentation` (writing notes to the `counseling_records` table).
*   **Admin Flow:** Focuses on `User Account Management` (CRUD operations on `counselors` table) and `System Oversight` (monitoring `appointments` table).

### Entity-Relationship Diagram (ERD)

The database `gcms_db` is designed with the following key entities:

1.  **`users`**: Stores login credentials (email, hashed password) and role.
2.  **`students`**: Linked to `users`. Stores personal details (name, course, contact) and JSON-encoded wellness data.
3.  **`counselors`**: Linked to `users`. Stores professional details (department, specialization).
4.  **`admins`**: Linked to `users`. Stores admin profile details.
5.  **`appointments`**: The central transaction table. Links `students` and `counselors`. Stores date, time, purpose, and status (Pending, Confirmed, etc.).
6.  **`counseling_records`**: Linked to `appointments`. Stores confidential session notes, observations, and recommendations.
7.  **`notifications`**: Stores system alerts for users.

### System Architecture

The GCMS follows a standard **Three-Tier Web Architecture**:

1.  **Presentation Layer (Frontend):** HTML5, CSS3, and Vanilla JavaScript (with Bootstrap 5 framework). This runs in the user's client browser.
2.  **Application Layer (Backend):** Native PHP running on an Apache Web Server (XAMPP environment). It handles logic, session management, and PHPMailer integration.
3.  **Data Layer (Database):** MySQL Database. Stores all persistent data.

---

## System Prototype (Description of Actual Modules)

### 1. Login Page (`index.php`)
The secure entry point. It requires an email and password. It validates the user against the `users` database table and redirects them to the appropriate dashboard (`student/`, `counselor/`, or `admin/`) based on their role.

### 2. Student Dashboard & Booking (`html/student/dashboard.php`, `book-appointment.php`)
*   **Dashboard:** Displays a summary of the student's upcoming appointments and recent notifications.
*   **Booking Form:** A multi-step form where students select a date/time and answer mandatory **Wellness Questions** (Physical, Intellectual, Environmental). Submitting this creates a "Pending" appointment and triggers notifications.

### 3. Counselor Dashboard & Schedule (`html/counselor/dashboard.php`, `my-schedule.php`)
*   **Dashboard:** Shows counters for total/pending appointments.
*   **My Schedule:** A list view of appointments assigned to the counselor. They can click to view details or update the status (Confirm/Reschedule).

### 4. Session Management (`html/counselor/session-management.php`)
An interface for conducting sessions. The counselor can view the student's wellness responses and enter confidential **Session Notes** and **Recommendations**. These are saved securely to the `counseling_records` table.

### 5. Admin Dashboard & Management (`html/admin/dashboard.php`, `counselors.php`)
*   **Dashboard:** Provides a high-level view of system activity (Total Users, Total Appointments).
*   **Counselor Management:** Allows the admin to register new counselors and manage their account status (Active/Inactive).
*   **Appointment Oversight:** Allows the admin to view all appointments across the system to ensure students are being attended to.

---

## Development and Testing

### Software Development Method
The project utilized the **Agile Methodology**. Development was broken down into iterative cycles (sprints):
1.  **Sprint 1:** Database design and Authentication (Login/Logout).
2.  **Sprint 2:** Student Booking module and Profile management.
3.  **Sprint 3:** Counselor Schedule and Session Notes module.
4.  **Sprint 4:** Admin oversight and Notification system integration.

### Testing
*   **Unit Testing:** Individual PHP scripts (e.g., database connection, login logic) were tested for errors.
*   **Integration Testing:** The flow from "Student Booking" -> "Database" -> "Counselor View" was tested to ensure data consistency.
*   **User Acceptance Testing:** The system was evaluated on local servers (XAMPP) to ensure it meets the defined requirements.

---

## Data Gathering Procedure
Data for the system design was gathered through the analysis of the existing manual forms (Intake Forms, Appointment Slips) used by the Guidance Office. This ensured the digital versions (e.g., the Wellness Questionnaire) accurately reflected the information counselors need.

## Respondents
(As per original study design) The system is designed for the three key stakeholders:
1.  **Students:** The primary service seekers.
2.  **Guidance Counselors:** The service providers.
3.  **Administrator:** The system overseer (Head of Guidance).

## Statistical Tool
Descriptive statistics (weighted mean) were used to analyze the results of the software quality evaluation survey based on ISO/IEC 25010 standards (Functionality, Reliability, Usability).
