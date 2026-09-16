# 🎓 Guidance Counseling Management System (GCMS)

> **Empowering Student Well-Being Through Streamlined, Confidential, and Digital Guidance Operations.**  
> *A modernized web-based appointment booking, wellness tracking, and session documentation platform.*

---

## 🌟 1. Project Overview & Tagline

The **Guidance Counseling Management System (GCMS)** is an end-to-end web platform engineered to modernize guidance counseling and mental wellness services in higher education institutions. 

**Tagline:**  
*Bridging students and counselors through secure appointment scheduling, comprehensive wellness insights, and automated communication.*

---

## 💡 2. Why Use This App? (User Benefits)

Traditional guidance offices often struggle with manual paper logs, walk-in congestion, schedule overlap, and delayed follow-ups. GCMS eliminates administrative bottlenecks and fosters a confidential, supportive environment.

* **🎯 Target Users:**
  * **College Students:** University students seeking academic, personal, emotional, or career counseling in an accessible and stigma-free manner.
  * **Guidance Counselors:** Licensed guidance personnel managing complex schedules, student assessments, and case histories.
  * **System Administrators / Office Heads:** Guidance department leadership requiring master visibility over appointments, staff load, and system health.

* **🚀 Main Problems Solved:**
  * **Eliminates Manual Walk-in Inefficiencies:** Students book appointments online 24/7 without long physical queues or paper intake slips.
  * **Guarantees Privacy & Stigma-Free Access:** Allows students to discreetly seek counseling assistance with role-based confidentiality safeguards.
  * **Holistic Pre-Session Assessment:** Captures multi-dimensional student wellness data (Physical, Intellectual, and Environmental wellness) prior to the session, equipping counselors with actionable insights beforehand.
  * **Zero Scheduling Conflicts:** Automated calendar filtering prevents double-booking and informs counselors in real time.
  * **Centralized & Secure Case Records:** Replaces physical filing cabinets with tamper-proof digital session notes, attendance records, and counselor recommendations.

---

## ✨ 3. Key Features

### 👨‍🎓 Student Portal
* **Intuitive Online Booking:** Select counseling dates, available time slots, and specify concerns (academic, personal, social, or career).
* **Multi-Dimensional Wellness Intake:** Integrated intake questionnaire covering physical wellness, intellectual health, and environmental adjustments.
* **Appointment Tracking & History:** Real-time visibility into appointment status (`Pending`, `Confirmed`, `Rescheduled`, `Completed`, `Cancelled`).
* **Profile Management & Secure Account Recovery:** Update student profile details, course, and department, with built-in password reset and email verification.

### 👩‍🏫 Counselor Workspace
* **Interactive Schedule Dashboard:** Filter upcoming, confirmed, and pending sessions; accept, decline, or reschedule appointments with a single click.
* **Confidential Session Manager:** Securely document session observations, clinical notes, attendance, and personalized student action plans.
* **Holistic Student Profiles:** Review historical sessions and completed wellness questionnaires before conducting a session.

### 🛡️ Administrative Governance
* **User & Staff Management:** Create, review, update, and manage guidance counselor profiles and departmental designations.
* **Master Appointment Oversight:** Centralized university-wide view of all appointments to guarantee that no student inquiry is left unaddressed.
* **Audit Trail & System Monitoring:** System health and account status control.

### 📬 Automated Communication & Alerts
* **In-System Notifications:** Instant real-time dashboard notifications for status changes, confirmations, and reschedules.
* **Automated Email Dispatch (PHPMailer):** Email alerts with schedule confirmations and OTP verification codes directly to university accounts.

---

## 💻 4. Tech Stack

| Component | Technology | Description |
| :--- | :--- | :--- |
| **Frontend** | **HTML5, CSS3, JavaScript (ES6+)** | Responsive user interface and dynamic client-side interactions |
| **UI Framework** | **Bootstrap 5.3 & Bootstrap Icons** | Modern, clean, mobile-first design framework |
| **Backend** | **Native PHP (PDO)** | Server-side logic, session authentication, and secure prepared queries |
| **Database** | **MySQL (`gcms_db`)** | Relational database schema with foreign key integrity |
| **Email Service** | **PHPMailer** | Robust SMTP mailing integration for transactional emails and verification |
| **Server Environment**| **Apache (XAMPP)** | Local web server stack |

---

## 🔄 5. How to Use the System (Workflow)

```
[Student Registration / Login] 
             ⬇
[Book Appointment + Complete Wellness Intake] 
             ⬇
[Pending Request Generated] ➡ [Counselor / Admin Receives Notification]
             ⬇
[Counselor Confirms / Reschedules Appointment] ➡ [Student Receives Confirmation]
             ⬇
[Counseling Session Conducted]
             ⬇
[Counselor Records Session Notes & Recommendations in Case History]
```

1. **Access & Authenticate:** Open the application at `index.php`. New students register their student profile; existing users (Student, Counselor, Admin) securely log in.
2. **Book a Counseling Session (Student):**
   * Navigate to **Book Appointment**.
   * Pick an available date and time slot.
   * Fill out the structured **Wellness Questionnaire** (Physical, Intellectual, Environmental questions) and submit.
3. **Review & Schedule (Counselor / Admin):**
   * Counselor logs into the **Counselor Dashboard** and receives the booking notification.
   * Reviews the student's submission and wellness concerns.
   * Confirms, reschedules, or assigns the session.
4. **Session Execution & Documentation (Counselor):**
   * During or following the meeting, the counselor opens **Session Management**.
   * Logs attendance status, clinical observations, confidential notes, and actionable recommendations.
5. **Continuous Follow-up:**
   * The student tracks appointment outcomes in their dashboard.
   * Records remain securely archived in the student’s case history for future counseling continuity.

---

## ⚙️ 6. Installation & Setup Instructions

Follow these steps to set up and run the system locally on your development environment:

### 📋 Prerequisites
* [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 7.4 or PHP 8.x)
* Git installed on your system
* Web browser (Chrome, Firefox, Edge, etc.)

---

### 📥 Step-by-Step Installation

#### 1. Clone the Repository
Clone the project into your local web server root (e.g., `C:/xampp/htdocs/`):
```bash
cd C:/xampp/htdocs
git clone https://github.com/JAPEE45/GuidanceCounselingMS.git
```

#### 2. Start Apache and MySQL
* Launch the **XAMPP Control Panel**.
* Start both the **Apache** and **MySQL** services.

#### 3. Import the Database
* Open your browser and navigate to **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
* Create a new database named:
  ```sql
  gcms_db
  ```
* Select `gcms_db`, click on the **Import** tab, and import the SQL file:
  * File: `main_database.sql`
* *(Optional)* If updating schema with email verification, run: `add_email_verification.sql`.

#### 4. Configure Application Settings
* **Database Connection:** Check `config/database.php` and update credentials if needed (defaults to root without password):
  ```php
  $host = 'localhost';
  $db_name = 'gcms_db';
  $username = 'root';
  $password = '';
  ```
* **Email SMTP (Optional for local testing):** Configure your SMTP credentials in `config/email.php` for automated email notifications.

#### 5. Launch the Application
Open your browser and visit:
```
http://localhost/GuidanceCounselingMS
```

---

## 🏛️ Academic Capstone Project Information

* **🎓 Project Type:** Undergraduate Capstone Project (BS in Computer Science)
* **🏫 Institution:** **Catanduanes State University (CatSU)** — *Guidance Counseling and Testing Office*
* **📅 Completed Date:** **November 2025** (Academic Year 2025–2026)
* **📋 Methodology:** Agile Software Development Methodology evaluated via **ISO/IEC 25010 Software Quality Standards** (Functionality, Reliability, Usability).

---

## 📄 License & Confidentiality
This project is developed for educational and academic research purposes under the College of Computer Studies at Catanduanes State University. Data privacy and student counseling confidentiality principles apply.
