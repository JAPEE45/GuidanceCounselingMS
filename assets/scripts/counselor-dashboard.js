const appointments = [
  {
    id: 1,
    date: "November 12, 2025",
    time: "09:00 AM",
    studentName: "John Michael Santos",
    type: "Physical Wellness",
    status: "Pending",
    data: {
      question1:
        "I engage in moderate physical activities like walking for 30 minutes daily and occasional jogging on weekends.",
      question2:
        "Yes, I exercise 3-4 times a week, mainly cardio and light strength training.",
      question3:
        "I try to maintain a balanced diet with vegetables, fruits, and protein, though I occasionally skip breakfast.",
      question4:
        "I follow a Mediterranean-style diet focusing on whole grains, lean proteins, and healthy fats. I often eat grilled chicken, fish, and plenty of vegetables.",
      question5:
        "I eat three times a day - breakfast at 7 AM, lunch at 12 PM, and dinner at 7 PM, with occasional healthy snacks in between.",
      question6:
        "I sleep approximately 6-7 hours per day during weekdays, and 8-9 hours on weekends.",
      question7:
        "I usually wake up at 6:00 AM and go to bed around 11:00 PM on weekdays. On weekends, I wake up at 8:00 AM and sleep at midnight.",
      question8:
        "My sleep is generally sound and restful, though I occasionally experience light sleep when stressed about exams.",
      question9:
        "No, I do not smoke, drink alcohol, or take any recreational drugs.",
    },
  },
  {
    id: 2,
    date: "November 12, 2025",
    time: "11:00 AM",
    studentName: "Maria Clara Reyes",
    type: "Intellectual Wellness",
    status: "Pending",
    data: {
      question1:
        "My strengths include critical thinking, problem-solving, and strong written communication skills. I excel in research and analysis.",
      question2:
        "I use these strengths daily in my coursework, particularly in essay writing, research projects, and group discussions where analytical thinking is required.",
      question3:
        "My academic performance is above average with a GPA of 3.7. I consistently achieve high marks in my major subjects, particularly in Social Sciences and English.",
      question4:
        "I enhance my knowledge by reading academic journals, attending webinars, participating in study groups, and taking online courses related to my field of interest.",
      question5:
        "I need to improve my mathematical and statistical analysis skills, as well as my proficiency in data visualization tools which are increasingly important in my field.",
      question6:
        "Yes, I would like to improve my public speaking skills. I tend to get anxious when presenting in front of large audiences, which affects my confidence.",
    },
  },
  {
    id: 3,
    date: "November 12, 2025",
    time: "02:00 PM",
    studentName: "Angelo Cruz",
    type: "Environmental Wellness",
    status: "Pending",
    data: {
      question1:
        "My home is peaceful and supportive. I live with my family in a quiet neighborhood with adequate space for studying and relaxation.",
      question2:
        "I don't have a boarding house as I live with my family, but I have my own room which serves as my personal space for studying and rest.",
      question3:
        "The classroom environment is conducive to learning. It's well-lit, properly ventilated, and equipped with necessary technology. However, it can get crowded during peak hours.",
      question4:
        "Yes, I feel safe and secure in my home environment. My family is supportive, and our neighborhood is peaceful with good security measures in place.",
      question5:
        "Yes, my environment motivates me significantly. My family encourages my academic pursuits, and the quiet atmosphere at home allows me to focus on my studies and achieve my goals.",
      question6:
        "Absolutely. My home environment provides emotional support and allows me to express myself freely. My family respects my choices and encourages me to pursue my interests and passions.",
    },
  },
  {
    id: 4,
    date: "November 13, 2025",
    time: "10:00 AM",
    studentName: "Patricia Anne Lim",
    type: "Physical Wellness",
    status: "Pending",
    data: {
      question1:
        "I maintain an active lifestyle through dance classes twice a week and regular yoga sessions every morning.",
      question2:
        "Yes, I exercise 5 times a week including dance, yoga, and occasional swimming.",
      question3:
        "I follow a plant-based diet with occasional fish. I ensure I get enough protein and nutrients through varied food choices.",
      question4:
        "I follow a primarily vegetarian diet with emphasis on organic foods. I often eat quinoa bowls, tofu dishes, and lots of fresh vegetables and fruits.",
      question5:
        "I eat four small meals throughout the day starting at 7 AM, with snacks at 10 AM and 3 PM, lunch at 1 PM, and dinner at 6 PM.",
      question6: "I sleep 7-8 hours consistently every night.",
      question7: "I wake up at 5:30 AM and go to sleep at 10:30 PM regularly.",
      question8:
        "I have deep, restful sleep thanks to my evening meditation routine.",
      question9:
        "No, I maintain a healthy lifestyle without any substance use.",
    },
  },
  {
    id: 5,
    date: "November 13, 2025",
    time: "01:00 PM",
    studentName: "Robert James Tan",
    type: "Intellectual Wellness",
    status: "Pending",
    data: {
      question1:
        "I excel in logical reasoning, programming, and technical problem-solving. I have strong analytical skills and attention to detail.",
      question2:
        "I apply these strengths in my computer science courses, coding projects, and when helping classmates debug their programs.",
      question3:
        "I maintain a 3.9 GPA and have received awards for outstanding performance in technical subjects.",
      question4:
        "I continuously learn through online coding challenges, contributing to open-source projects, and staying updated with latest programming languages and frameworks.",
      question5:
        "I need to develop better soft skills, particularly in team leadership and project management.",
      question6:
        "I would like to improve my documentation and technical writing skills to better communicate complex technical concepts.",
    },
  },
];

function loadAppointments() {
  const appointmentsList = document.getElementById("appointmentsList");
  const displayAppointments = appointments.slice(0, 3);

  appointmentsList.innerHTML = displayAppointments
    .map(
      (apt) => `
                <div class="appointment-card">
                    <div class="appointment-header">
                        <div>
                            <div class="appointment-time">
                                <i class="fas fa-calendar"></i>
                                ${apt.date} - ${apt.time}
                            </div>
                        </div>
                        <span class="appointment-type">${apt.type}</span>
                    </div>
                    <div class="appointment-student">
                        <i class="fas fa-user"></i>
                        <strong>${apt.studentName}</strong>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn-view" onclick="viewAppointment(${apt.id})">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-confirm" onclick="confirmAppointment(${apt.id})">
                            <i class="fas fa-check"></i> Confirm
                        </button>
                        <button class="btn-cancel" onclick="cancelAppointment(${apt.id})">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            `
    )
    .join("");
}


function viewAppointment(id) {
  const appointment = appointments.find((apt) => apt.id === id);
  if (!appointment) return;

  let modalContent = `
                <div class="detail-group">
                    <div class="detail-label">Date & Time:</div>
                    <div class="detail-value">${appointment.date} at ${appointment.time}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Student Name:</div>
                    <div class="detail-value">${appointment.studentName}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Session Type:</div>
                    <div class="detail-value">${appointment.type}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value"><span class="badge bg-warning">${appointment.status}</span></div>
                </div>
                <hr style="margin: 25px 0;">
                <h6 style="color: var(--navy-blue); font-weight: 600; margin-bottom: 20px;">Student Responses:</h6>
            `;

  if (appointment.type === "Physical Wellness") {
    modalContent += `
                    <div class="detail-group">
                        <div class="detail-label">1. Can you describe your routine physical activities?</div>
                        <div class="detail-value">${appointment.data.question1}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">2. Do you exercise? If so, how often?</div>
                        <div class="detail-value">${appointment.data.question2}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">3. How will you describe your daily food intake?</div>
                        <div class="detail-value">${appointment.data.question3}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">4. Do you follow certain types of dietary plan? What nutritional meal do you often eat?</div>
                        <div class="detail-value">${appointment.data.question4}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">5. How often do you eat a day? In what time do you eat your meals?</div>
                        <div class="detail-value">${appointment.data.question5}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">6. How many hours do you sleep in a day?</div>
                        <div class="detail-value">${appointment.data.question6}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">7. What is the time of your waking up and sleeping hour?</div>
                        <div class="detail-value">${appointment.data.question7}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">8. How would you describe your sleep (sound and good sleep or shallow sleep)?</div>
                        <div class="detail-value">${appointment.data.question8}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">9. Do you smoke, drink, or take drugs? If so, how often and since when?</div>
                        <div class="detail-value">${appointment.data.question9}</div>
                    </div>
                `;
  } else if (appointment.type === "Intellectual Wellness") {
    modalContent += `
                    <div class="detail-group">
                        <div class="detail-label">1. What are your strengths in terms of your knowledge, abilities, and skills?</div>
                        <div class="detail-value">${appointment.data.question1}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">2. How do you use these strengths in your daily tasks?</div>
                        <div class="detail-value">${appointment.data.question2}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">3. Describe your academic performance.</div>
                        <div class="detail-value">${appointment.data.question3}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">4. How do you enhance your knowledge, abilities, and skills?</div>
                        <div class="detail-value">${appointment.data.question4}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">5. If there are potentials that still need to be enhanced, what are those?</div>
                        <div class="detail-value">${appointment.data.question5}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">6. Is there weakness that you would like to be improved? What weakness?</div>
                        <div class="detail-value">${appointment.data.question6}</div>
                    </div>
                `;
  } else if (appointment.type === "Environmental Wellness") {
    modalContent += `
                    <div class="detail-group">
                        <div class="detail-label">1. How will you describe your home recently?</div>
                        <div class="detail-value">${appointment.data.question1}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">2. How will you describe your boarding house recently?</div>
                        <div class="detail-value">${appointment.data.question2}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">3. Can you describe your classroom situation?</div>
                        <div class="detail-value">${appointment.data.question3}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">4. Do you feel safe and secure in your home/boarding house/classroom?</div>
                        <div class="detail-value">${appointment.data.question4}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">5. Do you often feel that your environment motivates you to pursue your daily tasks and achieve your goal? How?</div>
                        <div class="detail-value">${appointment.data.question5}</div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">6. Can you feel that your environment provides care and allows you to be who you are? How?</div>
                        <div class="detail-value">${appointment.data.question6}</div>
                    </div>
                `;
  }

  document.getElementById("modalContent").innerHTML = modalContent;
  const modal = new bootstrap.Modal(
    document.getElementById("appointmentModal")
  );
  modal.show();
}


function confirmAppointment(id) {
  const appointment = appointments.find((apt) => apt.id === id);
  if (confirm(`Confirm appointment with ${appointment.studentName}?`)) {
    alert("Appointment confirmed successfully!");
  }
}

function cancelAppointment(id) {
  const appointment = appointments.find((apt) => apt.id === id);
  if (
    confirm(
      `Are you sure you want to cancel the appointment with ${appointment.studentName}?`
    )
  ) {
    alert("Appointment cancelled.");
  }
}


function navigateToSchedule() {
  window.location.href = 'session-management.html';
}


document.addEventListener("DOMContentLoaded", function () {
  loadAppointments();
});
