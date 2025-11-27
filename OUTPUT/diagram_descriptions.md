### Context Diagram Description

The Context Diagram illustrates the high-level interaction between the **Guidance Counseling Management System (GCMS)** and its primary users: the **Student**, **Counselor**, and **Admin**.

*   **Student:** Accesses the system to register, manage their profile, and book counseling appointments. They provide necessary wellness data during the booking process and receive system-generated confirmations and notifications.
*   **Counselor:** Interacts with the GCMS to manage their schedule, view student records, and document session outcomes. The system provides them with organized appointment lists and secure forms for recording confidential session notes.
*   **Admin:** Acts as the system overseer. They log in to manage user accounts (creating or deactivating counselor profiles) and generate system-wide reports to monitor the efficiency of the guidance services.

The GCMS serves as the central processing unit, mediating all data exchanges to ensure secure and organized communication between these entities.

### Entity-Relationship Diagram (ERD) Description

The Entity-Relationship Diagram (ERD) presents the logical structure of the `gcms_db` database, defining the key entities and their relationships.

*   **Users:** The central entity for authentication, storing login credentials and roles. It has a one-to-many relationship with **Students**, **Counselors**, and **Admins**, ensuring that every system user has a secure login profile.
*   **Appointments:** This transactional entity links **Students** and **Counselors**, recording the date, time, and status of each session. It serves as the bridge between the service seeker and the service provider.
*   **Counseling Records:** Directly linked to Appointments, this entity stores the confidential outcomes of sessions, including notes and recommendations. The one-to-one relationship with Appointments ensures that every record is tied to a specific event.
*   **Notifications:** Linked to the User entity, this allows the system to send alerts to any user role, facilitating communication regarding appointment updates.

The diagram uses Crow's Foot notation to depict cardinalities, highlighting how data is interconnected to support the system's functionality.

### System Architecture Description

The System Architecture diagram depicts the **Three-Tier Architecture** utilized by the GCMS, separating the system into distinct logical layers for better maintainability and scalability.

1.  **Presentation Layer (Frontend):** This is the user-facing layer, accessed via a web browser. It utilizes **HTML5**, **CSS3**, and **Bootstrap 5** to provide a responsive and intuitive interface. **Vanilla JavaScript** handles dynamic interactions, such as form validation and asynchronous requests.
2.  **Application Layer (Backend):** Hosted on an **Apache Web Server**, this layer contains the core business logic written in **Native PHP**. It processes user requests, manages sessions, and integrates with the **PHPMailer** library to send email notifications. It acts as the intermediary between the user interface and the data.
3.  **Data Layer (Storage):** The **MySQL Database** serves as the persistent storage for the system. It securely holds all user records, appointment schedules, and counseling notes.

This architecture ensures a clean separation of concerns, where the frontend handles display, the backend handles logic, and the database handles storage.

### Data Flow Diagram (DFD) - Student Booking Description

The Data Flow Diagram (DFD) for the Student Booking process details the specific steps a student takes to schedule a counseling session.

1.  **Login & Authentication:** The process begins with the student providing credentials, which are validated against the **Users Database**. Upon success, a session token is granted.
2.  **Check Schedule:** The system queries the **Appointments Database** to retrieve available time slots, ensuring no double-booking occurs.
3.  **Submit Booking:** The student selects a date and time and fills out the required Wellness Questionnaire. This data is processed and saved as a new record in the **Appointments Database**.
4.  **Notify Counselor:** Upon successful saving, the system triggers a notification process. It saves an alert to the **Notifications Database** and uses the email service to send a real-time alert to the assigned **Counselor**, completing the booking cycle.

This diagram highlights the flow of data from the user input through the system's logic and into the permanent storage, ensuring a reliable booking experience.
