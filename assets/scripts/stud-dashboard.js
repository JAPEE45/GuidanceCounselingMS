let sessions = [
  {
    type: "Academic Counseling",
    date: "October 28, 2025",
    counselor: "Dr. Maria Santos",
  },
  {
    type: "Career Guidance",
    date: "October 15, 2025",
    counselor: "Ms. Ana Cruz",
  },
  {
    type: "Personal Development",
    date: "October 5, 2025",
    counselor: "Dr. Maria Santos",
  },
  {
    type: "Personal Development",
    date: "October 5, 2025",
    counselor: "Dr. Maria Santos",
  },
];

function renderSessions() {
  const container = document.getElementById("sessionContainer");

  if (sessions.length === 0) {
    container.innerHTML = `
          <div class="no-sessions">
            <i class="fas fa-calendar-times"></i>
            <p>You have no sessions</p>
          </div>
        `;
    return;
  }

  let html = "";
  sessions.forEach((session) => {
    html += `
          <div class="session-item">
            <div>
              <div class="session-type">${session.type}</div>
              <small class="text-muted">${session.date} - ${session.counselor}</small>
            </div>
          </div>
        `;
  });

  container.innerHTML = html;
}

function clearSessions() {
  sessions = [];
  renderSessions();
}

renderSessions();
