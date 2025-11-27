// Sample Data - Counseling Sessions
// Check if sessionsData is already defined (e.g. from PHP), otherwise use empty array
var sessionsData = window.sessionsData || [];


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
    (s) => s.date === dateStr && s.status !== "cancelled"
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
        (session) => {
          const isConfirmed = session.status === 'confirmed';
          const statusBadge = isConfirmed
            ? '<div class="text-success fw-bold mt-2"><i class="bi bi-check-circle-fill"></i> Confirmed</div>'
            : '';

          const actions = isConfirmed
            ? `<button class="btn-cancel" onclick="cancelSession(${session.id})"><i class="bi bi-x-circle"></i> Cancel</button>`
            : `
                    <button class="btn-confirm" onclick="confirmSession(${session.id})">
                        <i class="bi bi-check-circle"></i> Confirm
                    </button>
                    <button class="btn-cancel" onclick="cancelSession(${session.id})">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                `;

          return `
                <div class="session-card" id="session-${session.id}">
                    <div class="session-time"><i class="bi bi-clock"></i> ${session.time}</div>
                    <div class="student-name"><i class="bi bi-person"></i> ${session.studentName}</div>
                    <span class="wellness-badge wellness-${session.type.toLowerCase().split(" ")[0]}">${session.type}</span>
                    ${statusBadge}
                    <div class="session-actions">
                        ${actions}
                    </div>
                </div>
            `;
        }
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
                    <div class="session-time"><i class="bi bi-clock"></i> ${session.time
      }</div>
                    <div class="student-name"><i class="bi bi-person"></i> ${session.studentName
      }</div>
                    <span class="wellness-badge wellness-${session.type.toLowerCase().split(" ")[0]
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
                        <div class="session-time"><i class="bi bi-clock"></i> ${session.time
        }</div>
                        <div class="student-name"><i class="bi bi-person"></i> ${session.studentName
        }</div>
                        <span class="wellness-badge wellness-${session.type.toLowerCase().split(" ")[0]
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
