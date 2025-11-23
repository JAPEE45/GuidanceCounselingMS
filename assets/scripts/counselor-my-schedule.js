// Sample Data - Counseling Sessions
const sessionsData = [
  {
    id: 1,
    date: "2025-11-25",
    time: "09:00 AM",
    studentName: "Maria Santos",
    type: "Physical Wellness",
    status: "pending",
  },
  {
    id: 2,
    date: "2025-11-25",
    time: "10:30 AM",
    studentName: "Juan Dela Cruz",
    type: "Intellectual Wellness",
    status: "pending",
  },
  {
    id: 3,
    date: "2025-11-25",
    time: "02:00 PM",
    studentName: "Ana Reyes",
    type: "Emotional Wellness",
    status: "pending",
  },
  {
    id: 4,
    date: "2025-11-27",
    time: "11:00 AM",
    studentName: "Pedro Garcia",
    type: "Environmental Awareness",
    status: "pending",
  },
  {
    id: 5,
    date: "2025-11-27",
    time: "03:00 PM",
    studentName: "Sofia Martinez",
    type: "Physical Wellness",
    status: "pending",
  },
  {
    id: 6,
    date: "2025-11-29",
    time: "09:30 AM",
    studentName: "Carlos Lopez",
    type: "Intellectual Wellness",
    status: "pending",
  },
  {
    id: 7,
    date: "2025-12-02",
    time: "10:00 AM",
    studentName: "Isabella Cruz",
    type: "Emotional Wellness",
    status: "pending",
  },
  {
    id: 8,
    date: "2025-12-02",
    time: "01:30 PM",
    studentName: "Miguel Torres",
    type: "Environmental Awareness",
    status: "pending",
  },
  {
    id: 9,
    date: "2025-12-05",
    time: "11:30 AM",
    studentName: "Elena Fernandez",
    type: "Physical Wellness",
    status: "pending",
  },
];

let currentDate = new Date();
let sessions = [...sessionsData];

function renderCalendar() {
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();

  // Update month display
  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  document.getElementById(
    "currentMonth"
  ).textContent = `${monthNames[month]} ${year}`;

  // Get first day of month and number of days
  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const daysInPrevMonth = new Date(year, month, 0).getDate();

  const calendarDays = document.getElementById("calendarDays");
  calendarDays.innerHTML = "";

  // Previous month's days
  for (let i = firstDay - 1; i >= 0; i--) {
    const day = daysInPrevMonth - i;
    const dayDiv = createDayElement(day, true, year, month - 1);
    calendarDays.appendChild(dayDiv);
  }

  // Current month's days
  for (let day = 1; day <= daysInMonth; day++) {
    const dayDiv = createDayElement(day, false, year, month);
    calendarDays.appendChild(dayDiv);
  }

  // Next month's days
  const totalCells = calendarDays.children.length;
  const remainingCells = 42 - totalCells;
  for (let day = 1; day <= remainingCells; day++) {
    const dayDiv = createDayElement(day, true, year, month + 1);
    calendarDays.appendChild(dayDiv);
  }
}

function createDayElement(day, isOtherMonth, year, month) {
  const dayDiv = document.createElement("div");
  dayDiv.className = "day";

  if (isOtherMonth) {
    dayDiv.classList.add("other-month");
  }

  // Format date for comparison
  const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(
    day
  ).padStart(2, "0")}`;

  // Check if today
  const today = new Date();
  if (
    year === today.getFullYear() &&
    month === today.getMonth() &&
    day === today.getDate()
  ) {
    dayDiv.classList.add("today");
  }

  // Check for sessions
  const daySessions = sessions.filter(
    (s) => s.date === dateStr && s.status === "pending"
  );
  if (daySessions.length > 0) {
    dayDiv.classList.add("has-sessions");
    const badge = document.createElement("div");
    badge.className = "session-badge";
    badge.textContent = daySessions.length;
    dayDiv.appendChild(badge);
  }

  const dayNumber = document.createElement("div");
  dayNumber.className = "day-number";
  dayNumber.textContent = day;
  dayDiv.appendChild(dayNumber);

  dayDiv.addEventListener("click", () =>
    showSessionsModal(dateStr, daySessions)
  );

  return dayDiv;
}

function showSessionsModal(dateStr, daySessions) {
  const modal = new bootstrap.Modal(document.getElementById("sessionsModal"));
  const date = new Date(dateStr);
  const formattedDate = date.toLocaleDateString("en-US", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  document.getElementById(
    "modalTitle"
  ).textContent = `Sessions for ${formattedDate}`;

  const modalBody = document.getElementById("modalBody");

  if (daySessions.length === 0) {
    modalBody.innerHTML =
      '<div class="no-sessions"><i class="bi bi-calendar-x" style="font-size: 48px; color: #ccc;"></i><p>No sessions for this day</p></div>';
  } else {
    modalBody.innerHTML = daySessions
      .map(
        (session) => `
                    <div class="session-card" id="session-${session.id}">
                        <div class="session-time"><i class="bi bi-clock"></i> ${
                          session.time
                        }</div>
                        <div class="student-name"><i class="bi bi-person"></i> ${
                          session.studentName
                        }</div>
                        <span class="wellness-badge wellness-${
                          session.type.toLowerCase().split(" ")[0]
                        }">${session.type}</span>
                        <div class="session-actions">
                            <button class="btn-confirm" onclick="confirmSession(${
                              session.id
                            })">
                                <i class="bi bi-check-circle"></i> Confirm
                            </button>
                            <button class="btn-cancel" onclick="cancelSession(${
                              session.id
                            })">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </div>
                    </div>
                `
      )
      .join("");
  }

  modal.show();
}

function confirmSession(sessionId) {
  const session = sessions.find((s) => s.id === sessionId);
  if (session) {
    session.status = "confirmed";
    const sessionCard = document.getElementById(`session-${sessionId}`);
    sessionCard.style.opacity = "0.5";
    sessionCard.innerHTML = `
                    <div class="session-time"><i class="bi bi-clock"></i> ${
                      session.time
                    }</div>
                    <div class="student-name"><i class="bi bi-person"></i> ${
                      session.studentName
                    }</div>
                    <span class="wellness-badge wellness-${
                      session.type.toLowerCase().split(" ")[0]
                    }">${session.type}</span>
                    <div style="color: #28a745; font-weight: 600; margin-top: 10px;">
                        <i class="bi bi-check-circle-fill"></i> Confirmed
                    </div>
                `;
    renderCalendar();
  }
}

function cancelSession(sessionId) {
  const session = sessions.find((s) => s.id === sessionId);
  if (session) {
    if (
      confirm(
        `Are you sure you want to cancel the session with ${session.studentName}?`
      )
    ) {
      session.status = "cancelled";
      const sessionCard = document.getElementById(`session-${sessionId}`);
      sessionCard.style.opacity = "0.5";
      sessionCard.innerHTML = `
                        <div class="session-time"><i class="bi bi-clock"></i> ${
                          session.time
                        }</div>
                        <div class="student-name"><i class="bi bi-person"></i> ${
                          session.studentName
                        }</div>
                        <span class="wellness-badge wellness-${
                          session.type.toLowerCase().split(" ")[0]
                        }">${session.type}</span>
                        <div style="color: #dc3545; font-weight: 600; margin-top: 10px;">
                            <i class="bi bi-x-circle-fill"></i> Cancelled
                        </div>
                    `;
      renderCalendar();
    }
  }
}

// Event Listeners
document.getElementById("prevMonth").addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar();
});

document.getElementById("nextMonth").addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar();
});

document.getElementById("today").addEventListener("click", () => {
  currentDate = new Date();
  renderCalendar();
});

// Initial render
renderCalendar();
