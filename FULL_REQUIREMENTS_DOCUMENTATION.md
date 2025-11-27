# Requirements Documentation
**Project:** Guidance Counseling Management System (GCMS)
**Target Audience:** Students, Guidance Counselors, Administrators

## 1. Functional Requirements
These requirements define the specific behaviors and functions the system must support. They are categorized by user role and system module.

### Table 1. Functional Requirements

| MODULE | REQUIREMENT | DESCRIPTION |
| :--- | :--- | :--- |
| **1. Authentication & Access Control** | Secure Login | The system shall allow users to log in using a registered email and password. All passwords must be encrypted using hashing algorithms. |
| | Role-Based Redirection | Upon successful login, the system must automatically redirect the user to their specific dashboard (Student, Counselor, or Admin). |
| | Session Management | The system must maintain secure sessions and automatically log users out after a period of inactivity or upon clicking "Logout". |
| **2. Student Module** | Account Registration | Students must be able to create an account by providing their Student ID, Name, Course, and Contact details. |
| | Appointment Booking | Students must be able to request counseling sessions by selecting a date, time, and appointment purpose. |
| | **Wellness Assessment** | **(Critical Feature)** During the booking process, students must complete a mandatory wellness questionnaire (Physical, Intellectual, Environmental) to provide context for the counselor. |
| | Profile Management | Students must be able to view and update their contact information and profile details. |
| | Status Tracking | Students must be able to view the current status of their requested appointments (Pending, Confirmed, Rescheduled). |
| **3. Counselor Module** | Schedule Management | Counselors must have a dashboard to view all appointments assigned to them, categorized by status. |
| | Appointment Actions | Counselors must be able to Confirm, Reschedule, or Cancel appointments based on their availability. |
| | **Session Documentation** | Counselors must be able to record confidential session notes, observations, and recommendations for each completed appointment. |
| | Student Record Access | Counselors must be able to view the wellness history and previous session records of students assigned to them. |
| **4. Admin Module** | Counselor Account Management | Administrators must be able to add new counselor accounts, update existing details, and deactivate accounts if necessary. |
| | System Oversight | Administrators must have a master view of all appointments across the university to ensure no request is ignored. |
| | Dashboard Analytics | The system must display key metrics such as Total Appointments, Pending Requests, and Active Counselors on the admin dashboard. |
| **5. Notification System** | Email Alerts | The system must send automated email notifications via PHPMailer when an appointment is requested or status is changed. |
| | In-App Notifications | The system must display visual alerts (badges) on the user dashboard for new events (e.g., "New Appointment Request"). |

---

## 2. Non-Functional Requirements
These requirements define the quality attributes, performance, and constraints of the system, aligned with ISO/IEC 25010 standards.

### Table 2. Non-Functional Requirements

| QUALITY ATTRIBUTE | REQUIREMENT | DESCRIPTION |
| :--- | :--- | :--- |
| **Usability** | User Interface | The system shall utilize a responsive design (Bootstrap framework) that adapts to different screen sizes (desktop/laptop). |
| | Ease of Use | Navigation menus must be consistent across all pages, and forms must provide clear validation feedback (e.g., "Date is required"). |
| **Security** | Data Protection | Passwords shall never be stored in plain text. They must be hashed using `password_hash()` (Bcrypt). |
| | Input Validation | All user inputs must be sanitized and processed using PDO Prepared Statements to prevent SQL Injection attacks. |
| | Access Control | Pages must be protected by session checks (`$_SESSION['role']`) to prevent unauthorized access via direct URL entry. |
| **Performance** | Response Time | The system shall load pages and process form submissions (such as booking an appointment) in under 3 seconds under normal load. |
| | Availability | The system shall be available during school hours (8:00 AM - 5:00 PM) via the local network or web host. |
| **Reliability** | Data Integrity | The database must enforce Referential Integrity (Foreign Keys) to ensure that an appointment cannot exist without a valid student and counselor. |
| **Maintainability** | Code Structure | The source code shall follow a modular structure (separating Logic, UI, and Configuration) to facilitate future updates (e.g., adding SMS features later). |

---

## 3. System Constraints & Assumptions

### Constraints
1.  **Network:** The system requires an active internet connection (or local intranet connection) to access the server and send emails.
2.  **Hardware:** The system is designed for web browsers; it is not a native mobile app (Android/iOS), though it is mobile-responsive.
3.  **SMS:** SMS notifications are not currently implemented due to the cost of API gateways; Email is the primary external notification channel.

### Assumptions
1.  **Data Accuracy:** It is assumed that students provide truthful information in the Wellness Questionnaire.
2.  **User Email:** It is assumed that all users (Students and Staff) have access to a valid email address for account recovery and notifications.
