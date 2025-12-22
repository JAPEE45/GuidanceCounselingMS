### 1. Use Case Diagram (Strict UML)
Figure 4 represents the Use Case Diagram using standard UML notation. 
*   **Actors:** Represented as stick figures (Student, Counselor, Admin).
*   **System Boundary:** The rectangle enclosing the use cases.
*   **Relationships:**
    *   **Association (Solid Line):** Direct interaction between Actor and Use Case (e.g., Student -> Book Appointment).
    *   **Include (Dashed Line `<<include>>`):** Represents mandatory behavior. Specifically, **"Book Appointment"** *includes* **"Answer Wellness Form"**, meaning a booking cannot be completed without the wellness data. This explicitly addresses the instructor's requirement for proper dependency modeling.

### 2. Entity-Relationship Diagram (ERD)
Figure 6 utilizes strict Crow's Foot notation to define database cardinality.
*   **Supertype/Subtype:** The `USER` entity is the parent, with a **Mandatory One-to-One (`||--||`)** relationship to `STUDENT`, `COUNSELOR`, or `ADMIN`. This ensures every login is strictly tied to one profile type.
*   **Transactional Integrity:** The `APPOINTMENT` entity has a **Zero-to-One (`||--o|`)** relationship with `COUNSELING_RECORD`. This accurately models the real-world scenario: an appointment starts with *zero* records (Pending), and ends with *one* record (Completed). It prevents "phantom" records from existing without an appointment.

### 3. Activity Diagram (Swimlane)
Figure 5 follows the UML Activity Diagram standard using **Swimlanes**.
*   **Partitions:** The diagram is divided into three vertical lanes: **Student**, **System**, and **Counselor**, clarifying *who* performs *what*.
*   **Control Flow:** Solid arrows show the sequence.
*   **Decision Nodes:** Diamond shapes (`{?}`) represent branching logic (e.g., "Authenticated?" or "Accept Request?").
*   **Start/End:** Represented by filled circles `(( ))`. 
This diagram specifically maps the "Booking Lifecycle," showing how a student's action crosses into the System for processing and lands in the Counselor's lane for approval.

### 4. Context Diagram (DFD Level 0)
Figure 2 acts as the highest-level view.
*   **Single Process:** The "System" is the only circle, strictly following Level 0 rules.
*   **No Data Stores:** Stores are hidden at this level; only external entities and data flows are shown.
*   **Data Packets:** Arrows are labeled with specific data objects (e.g., "Booking Request," "Session Notes") rather than generic actions.
