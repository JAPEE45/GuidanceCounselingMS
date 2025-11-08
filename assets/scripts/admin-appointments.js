// Sample appointment data
const appointmentsData = [
  {
    id: 1,
    date: "Monday, November 11",
    time: "09:00 AM",
    student: "Juan Dela Cruz",
    type: "Physical Wellness",
    status: "pending",
    data: {
      q1: "I maintain a regular exercise routine, usually working out 3-4 times a week including cardio and strength training.",
      q2: "Yes, I exercise regularly. I go to the gym 4 times a week and do jogging on weekends.",
      q3: "I try to eat balanced meals with vegetables, protein, and carbohydrates. However, I sometimes skip breakfast due to my schedule.",
      q4: "I don't follow a specific diet plan, but I try to eat nutritious meals. I often eat chicken, fish, vegetables, and rice.",
      q5: "I eat 3 meals a day. Breakfast at 7 AM (when I have time), lunch at 12 PM, and dinner at 7 PM.",
      q6: "I sleep around 6-7 hours on weekdays, but I try to catch up on weekends with 8-9 hours.",
      q7: "I usually sleep at 11 PM and wake up at 6 AM on weekdays. On weekends, I sleep at midnight and wake up at 9 AM.",
      q8: "My sleep is generally good, though sometimes I have trouble falling asleep when stressed about schoolwork.",
      q9: "No, I don't smoke, drink alcohol, or take drugs.",
    },
  },
  {
    id: 2,
    date: "Monday, November 11",
    time: "10:30 AM",
    student: "Maria Santos",
    type: "Intellectual Wellness",
    status: "pending",
    data: {
      q1: "My strengths include critical thinking, research skills, and the ability to learn new concepts quickly. I'm also good at writing and analysis.",
      q2: "I use these strengths in my coursework, especially in research projects and essay writing. I also help classmates understand difficult topics.",
      q3: "My academic performance is generally good. I maintain a GPA of 3.5 and consistently perform well in my major subjects.",
      q4: "I enhance my knowledge by reading academic journals, attending webinars, and participating in study groups. I also take online courses related to my field.",
      q5: "I would like to improve my public speaking skills and become more confident in presentations.",
      q6: "Yes, I struggle with time management sometimes. I tend to procrastinate on large projects and then rush to complete them.",
    },
  },
  {
    id: 3,
    date: "Tuesday, November 12",
    time: "02:00 PM",
    student: "Pedro Reyes",
    type: "Environmental Wellness",
    status: "pending",
    data: {
      q1: "My home is comfortable and peaceful. It's a supportive environment where I can focus on my studies and relax.",
      q2: "N/A - I live with my family.",
      q3: "The classroom is well-maintained and conducive to learning. It has good lighting and ventilation.",
      q4: "Yes, I feel safe and secure both at home and in the classroom. My family is supportive and the school has good security measures.",
      q5: "Yes, my environment motivates me. My family encourages me to pursue my goals, and the school provides resources and support for students.",
      q6: "Yes, I feel my environment allows me to be authentic. My family accepts me for who I am, and I have friends who support me.",
    },
  },
  {
    id: 4,
    date: "Wednesday, November 13",
    time: "09:00 AM",
    student: "Ana Garcia",
    type: "Intellectual Wellness",
    status: "pending",
    data: {
      q1: "I'm good at mathematics, problem-solving, and logical reasoning. I also have strong organizational skills.",
      q2: "I apply these skills in my engineering courses and when helping organize student activities.",
      q3: "I'm doing well academically with a 3.7 GPA. I particularly excel in technical subjects.",
      q4: "I participate in workshops, join academic competitions, and work on personal projects to enhance my skills.",
      q5: "I want to improve my programming skills and learn more about emerging technologies.",
      q6: "I sometimes struggle with creativity and thinking outside the box. I tend to stick to conventional solutions.",
    },
  },
  {
    id: 5,
    date: "Wednesday, November 13",
    time: "11:00 AM",
    student: "Carlos Mendoza",
    type: "Physical Wellness",
    status: "pending",
    data: {
      q1: "I walk to school daily which takes about 30 minutes. I also play basketball with friends twice a week.",
      q2: "Yes, I exercise. I play basketball regularly and do some bodyweight exercises at home.",
      q3: "I eat regular meals, mostly home-cooked food. My diet consists of rice, meat, vegetables, and fruits.",
      q4: "No specific diet plan. I eat what my family prepares, which is typically Filipino cuisine with balanced nutrition.",
      q5: "I eat 3 main meals and 1-2 snacks daily. Breakfast at 6:30 AM, lunch at 12:30 PM, dinner at 7 PM.",
      q6: "I sleep about 7-8 hours on most nights.",
      q7: "I sleep around 10:30 PM and wake up at 6 AM.",
      q8: "I have sound sleep most nights. I rarely have trouble sleeping.",
      q9: "No, I don't smoke, drink, or take drugs.",
    },
  },
];

const historyData = [
  {
    id: 101,
    date: "Friday, November 8",
    time: "01:00 PM",
    student: "Lisa Fernandez",
    type: "Environmental Wellness",
    status: "approved",
    data: {},
  },
  {
    id: 102,
    date: "Thursday, November 7",
    time: "03:00 PM",
    student: "Mark Gonzales",
    type: "Physical Wellness",
    status: "completed",
    data: {},
  },
];

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
                            <div class="appointment-item" onclick="selectAppointment(${
                              apt.id
                            }, '${section}')" id="apt-${apt.id}">
                                <div class="appointment-time">
                                    ${apt.time} - Student: ${apt.student}
                                    <span class="appointment-type type-${
                                      apt.type.toLowerCase().split(" ")[0]
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
