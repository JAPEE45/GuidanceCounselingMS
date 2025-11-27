// Sample appointment data
// Sample appointment data
var appointmentsData = window.appointmentsData || [];
var historyData = window.historyData || [];


let selectedAppointment = null;

// Initialize the page
function init() {
  renderAppointments("pending");
  renderAppointments("history");
}

// Render appointments
function renderAppointments(section) {
  const data = section === "pending" ? appointmentsData : historyData;
  const container = document.getElementById(`${section}-appointments`);

  // Group appointments by date
  const grouped = {};
  data.forEach((apt) => {
    if (!grouped[apt.date]) {
      grouped[apt.date] = [];
    }
    grouped[apt.date].push(apt);
  });

  let html = "";
  for (const date in grouped) {
    html += `
                    <div class="date-group">
                        <div class="date-header">${date}</div>
                        ${grouped[date]
        .map(
          (apt) => `
                            <div class="appointment-item" onclick="selectAppointment(${apt.id
            }, '${section}')" id="apt-${apt.id}">
                                <div class="appointment-time">
                                    ${apt.time} - Student: ${apt.student}
                                    <span class="appointment-type type-${apt.type.toLowerCase().split(" ")[0]
            }">${apt.type}</span>
                                </div>
                            </div>
                        `
        )
        .join("")}
                    </div>
                `;
  }

  container.innerHTML =
    html ||
    '<div class="date-group"><p class="text-muted">No appointments found.</p></div>';
}

// Select appointment
function selectAppointment(id, section) {
  const data = section === "pending" ? appointmentsData : historyData;
  const appointment = data.find((apt) => apt.id === id);

  if (!appointment) return;

  // Remove previous selection
  document.querySelectorAll(".appointment-item").forEach((item) => {
    item.classList.remove("selected");
  });

  // Add selection to clicked item
  document.getElementById(`apt-${id}`).classList.add("selected");

  selectedAppointment = appointment;

  // Enable buttons only for pending appointments
  if (section === "pending") {
    document.getElementById("approveBtn").disabled = false;
    document.getElementById("declineBtn").disabled = false;
    document.getElementById("rescheduleBtn").disabled = false;
  }

  // Show modal with details
  showAppointmentDetails(appointment);
}

// Show appointment details modal
function showAppointmentDetails(appointment) {
  const modalBody = document.getElementById("modalBody");
  let content = `
                <div class="data-item">
                    <div class="data-question">Student Name:</div>
                    <div class="data-answer">${appointment.student}</div>
                </div>
                <div class="data-item">
                    <div class="data-question">Appointment Type:</div>
                    <div class="data-answer">${appointment.type}</div>
                </div>
                <div class="data-item">
                    <div class="data-question">Date & Time:</div>
                    <div class="data-answer">${appointment.date} at ${appointment.time}</div>
                </div>
            `;

  if (appointment.type === "Physical Wellness") {
    content += `
                    <hr class="my-4">
                    <h6 class="mb-3" style="color: var(--navy-blue);">Physical Wellness Questionnaire:</h6>
                    <div class="data-item">
                        <div class="data-question">1. Can you describe your routine physical activities?</div>
                        <div class="data-answer">${appointment.data.q1}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">2. Do you exercise? If so, how often?</div>
                        <div class="data-answer">${appointment.data.q2}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">3. How will you describe your daily food intake?</div>
                        <div class="data-answer">${appointment.data.q3}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">4. Do you follow certain types of dietary plan? What nutritional meal do you often eat?</div>
                        <div class="data-answer">${appointment.data.q4}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">5. How often do you eat a day? In what time do you eat your meals?</div>
                        <div class="data-answer">${appointment.data.q5}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">6. How many hours do you sleep in a day?</div>
                        <div class="data-answer">${appointment.data.q6}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">7. What is the time of your waking up and sleeping hour?</div>
                        <div class="data-answer">${appointment.data.q7}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">8. How would you describe your sleep (sound and good sleep or shallow sleep)?</div>
                        <div class="data-answer">${appointment.data.q8}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">9. Do you smoke, drink, or take drugs? If so, how often and since when?</div>
                        <div class="data-answer">${appointment.data.q9}</div>
                    </div>
                `;
  } else if (appointment.type === "Intellectual Wellness") {
    content += `
                    <hr class="my-4">
                    <h6 class="mb-3" style="color: var(--navy-blue);">Intellectual Wellness Questionnaire:</h6>
                    <div class="data-item">
                        <div class="data-question">1. What are your strengths in terms of your knowledge, abilities, and skills?</div>
                        <div class="data-answer">${appointment.data.q1}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">2. How do you use these strengths in your daily tasks?</div>
                        <div class="data-answer">${appointment.data.q2}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">3. Describe your academic performance.</div>
                        <div class="data-answer">${appointment.data.q3}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">4. How do you enhance your knowledge, abilities, and skills?</div>
                        <div class="data-answer">${appointment.data.q4}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">5. If there are potentials that still need to be enhanced, what are those?</div>
                        <div class="data-answer">${appointment.data.q5}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">6. Is there weakness that you would like to be improved? What weakness?</div>
                        <div class="data-answer">${appointment.data.q6}</div>
                    </div>
                `;
  } else if (appointment.type === "Environmental Wellness") {
    content += `
                    <hr class="my-4">
                    <h6 class="mb-3" style="color: var(--navy-blue);">Environmental Wellness Questionnaire:</h6>
                    <div class="data-item">
                        <div class="data-question">1. How will you describe your home recently?</div>
                        <div class="data-answer">${appointment.data.q1}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">2. How will you describe your boarding house recently?</div>
                        <div class="data-answer">${appointment.data.q2}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">3. Can you describe your classroom situation?</div>
                        <div class="data-answer">${appointment.data.q3}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">4. Do you feel safe and secure in your home/boarding house/classroom?</div>
                        <div class="data-answer">${appointment.data.q4}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">5. Do you often feel that your environment motivates you to pursue your daily tasks and achieve your goal? How?</div>
                        <div class="data-answer">${appointment.data.q5}</div>
                    </div>
                    <div class="data-item">
                        <div class="data-question">6. Can you feel that your environment provides care and allows you to be who you are? How?</div>
                        <div class="data-answer">${appointment.data.q6}</div>
                    </div>
                `;
  }

  modalBody.innerHTML = content;

  const modal = new bootstrap.Modal(
    document.getElementById("appointmentModal")
  );
  modal.show();
}

// Close modal and deselect
// document.getElementById('appointmentModal').addEventListener('hidden.bs.modal', function () {
//     deselectAppointment();
// });
// document.addEventListener('click', function (event) {
//     deselectAppointment()
// })

// Deselect appointment
function deselectAppointment() {
  document.querySelectorAll(".appointment-item").forEach((item) => {
    item.classList.remove("selected");
  });

  selectedAppointment = null;

  // Disable action buttons
  document.getElementById("approveBtn").disabled = true;
  document.getElementById("declineBtn").disabled = true;
  document.getElementById("rescheduleBtn").disabled = true;
}

// Switch tabs
function switchTab(tab) {
  // Update tab buttons
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.classList.remove("active");
  });
  event.target.classList.add("active");

  // Update sections
  document.querySelectorAll(".appointments-section").forEach((section) => {
    section.classList.remove("active");
  });
  document.getElementById(`${tab}-section`).classList.add("active");

  // Deselect appointment when switching tabs
  deselectAppointment();
}

// Show confirmation modal
function showConfirmModal(action) {
  const modal = new bootstrap.Modal(document.getElementById(`${action}Modal`));
  modal.show();
}

// Process action
function processAction(action) {
  if (!selectedAppointment) return;

  if (action === "approve") {
    alert(`Appointment with ${selectedAppointment.student} has been approved!`);
    moveToHistory(selectedAppointment.id, "approved");
  } else if (action === "decline") {
    alert(`Appointment with ${selectedAppointment.student} has been declined.`);
    moveToHistory(selectedAppointment.id, "declined");
  } else if (action === "reschedule") {
    const newDateTime = document.getElementById("newDateTime").value;
    if (!newDateTime) {
      alert("Please select a new date and time.");
      return;
    }
    alert(
      `Appointment with ${selectedAppointment.student} has been rescheduled to ${newDateTime}.`
    );
    moveToHistory(selectedAppointment.id, "rescheduled");
  }

  // Close all modals
  document.querySelectorAll(".modal").forEach((modalEl) => {
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
  });

  // Deselect appointment
  deselectAppointment();
}

// Move appointment to history
function moveToHistory(id, status) {
  const index = appointmentsData.findIndex((apt) => apt.id === id);
  if (index !== -1) {
    const appointment = appointmentsData.splice(index, 1)[0];
    appointment.status = status;
    historyData.unshift(appointment);

    // Re-render appointments
    renderAppointments("pending");
    renderAppointments("history");
  }
}

// Initialize on page load
init();
