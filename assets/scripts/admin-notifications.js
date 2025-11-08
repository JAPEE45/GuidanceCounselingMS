// Sample Data
const newAppointmentsData = [
  {
    id: 1,
    studentName: "Maria Santos",
    studentId: "2024-0001",
    grade: "Grade 11",
    concern: "Academic Counseling",
    date: "2025-11-10",
    time: "10:00 AM",
    description: "Needs help with course selection for college preparation",
    contactEmail: "maria.santos@school.edu",
    contactPhone: "0917-123-4567",
  },
  {
    id: 2,
    studentName: "Juan Dela Cruz",
    studentId: "2024-0002",
    grade: "Grade 10",
    concern: "Personal Counseling",
    date: "2025-11-11",
    time: "2:00 PM",
    description: "Experiencing difficulties with peer relationships",
    contactEmail: "juan.delacruz@school.edu",
    contactPhone: "0918-234-5678",
  },
  {
    id: 3,
    studentName: "Anna Reyes",
    studentId: "2024-0003",
    grade: "Grade 12",
    concern: "Career Guidance",
    date: "2025-11-12",
    time: "11:00 AM",
    description: "Wants to discuss career options and college applications",
    contactEmail: "anna.reyes@school.edu",
    contactPhone: "0919-345-6789",
  },
];

const pendingAppointmentsData = [
  {
    id: 4,
    studentName: "Pedro Garcia",
    studentId: "2024-0004",
    grade: "Grade 9",
    concern: "Academic Counseling",
    date: "2025-11-09",
    time: "9:00 AM",
    status: "Approved - Awaiting Counselor",
    approvedDate: "2025-11-08",
  },
  {
    id: 5,
    studentName: "Sofia Martinez",
    studentId: "2024-0005",
    grade: "Grade 11",
    concern: "Personal Counseling",
    date: "2025-11-10",
    time: "1:00 PM",
    status: "Approved - Awaiting Counselor",
    approvedDate: "2025-11-07",
  },
  {
    id: 6,
    studentName: "Carlos Ramos",
    studentId: "2024-0006",
    grade: "Grade 10",
    concern: "Behavioral Counseling",
    date: "2025-11-11",
    time: "3:00 PM",
    status: "Approved - Awaiting Counselor",
    approvedDate: "2025-11-08",
  },
  {
    id: 7,
    studentName: "Lisa Torres",
    studentId: "2024-0007",
    grade: "Grade 12",
    concern: "Career Guidance",
    date: "2025-11-13",
    time: "10:30 AM",
    status: "Approved - Awaiting Counselor",
    approvedDate: "2025-11-08",
  },
  {
    id: 8,
    studentName: "Miguel Cruz",
    studentId: "2024-0008",
    grade: "Grade 9",
    concern: "Academic Counseling",
    date: "2025-11-14",
    time: "2:30 PM",
    status: "Approved - Awaiting Counselor",
    approvedDate: "2025-11-07",
  },
];

const pendingReferralsData = [
  {
    id: 9,
    studentName: "Elena Villanueva",
    studentId: "2024-0009",
    grade: "Grade 11",
    referredBy: "Ms. Johnson (Math Teacher)",
    reason: "Consistent decline in academic performance",
    referralDate: "2025-11-05",
    notes:
      "Student has shown significant drop in test scores over the past month",
  },
  {
    id: 10,
    studentName: "Roberto Santos",
    studentId: "2024-0010",
    grade: "Grade 10",
    referredBy: "Mr. Smith (Class Adviser)",
    reason: "Behavioral concerns - frequent absences",
    referralDate: "2025-11-06",
    notes: "Multiple unexplained absences. Family consultation recommended",
  },
];

const counselorsData = [
  {
    id: 1,
    name: "Dr. Sarah Johnson",
    specialization: "Academic Counseling",
    availability: "Available",
    currentCaseload: 12,
  },
  {
    id: 2,
    name: "Ms. Patricia Cruz",
    specialization: "Personal Counseling",
    availability: "Available",
    currentCaseload: 8,
  },
  {
    id: 3,
    name: "Mr. Robert Lee",
    specialization: "Career Guidance",
    availability: "Available",
    currentCaseload: 10,
  },
  {
    id: 4,
    name: "Dr. Michelle Tan",
    specialization: "Behavioral Counseling",
    availability: "Available",
    currentCaseload: 15,
  },
];

let currentAppointment = null;
let selectedCounselor = null;

// Render Functions
function renderNewAppointments() {
  const container = document.getElementById("newAppointments");
  if (newAppointmentsData.length === 0) {
    container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No New Appointment Requests</h5>
                        <p>All appointment requests have been processed</p>
                    </div>
                `;
    return;
  }

  container.innerHTML = newAppointmentsData
    .map(
      (apt) => `
                <div class="notification-card">
                    <div class="notification-header">
                        <div class="student-info">
                            <h5>${apt.studentName}</h5>
                            <p>${apt.studentId} | ${apt.grade}</p>
                        </div>
                        
                    </div>
                    <div class="notification-details">
                        <div class="detail-item">
                            <i class="fas fa-comments"></i>
                            <span><strong>Concern:</strong> ${apt.concern}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Date:</strong> ${apt.date}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span><strong>Time:</strong> ${apt.time}</span>
                        </div>
                    </div>
                    
                </div>
            `
    )
    .join("");
}

function renderPendingAppointments() {
  const container = document.getElementById("pendingAppointments");
  if (pendingAppointmentsData.length === 0) {
    container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No Pending Appointments</h5>
                        <p>All appointments have been assigned to counselors</p>
                    </div>
                `;
    return;
  }

  container.innerHTML = pendingAppointmentsData
    .map(
      (apt) => `
                <div class="notification-card">
                    <div class="notification-header">
                        <div class="student-info">
                            <h5>${apt.studentName}</h5>
                            <p>${apt.studentId} | ${apt.grade}</p>
                        </div>
                        <span class="badge badge-pending">Awaiting Assignment</span>
                    </div>
                    <div class="notification-details">
                        <div class="detail-item">
                            <i class="fas fa-comments"></i>
                            <span><strong>Concern:</strong> ${apt.concern}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Date:</strong> ${apt.date}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span><strong>Time:</strong> ${apt.time}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Approved:</strong> ${apt.approvedDate}</span>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button class="btn-custom btn-assign" onclick="openAssignModal(${apt.id})">
                            <i class="fas fa-user-plus"></i> Assign Counselor
                        </button>
                    </div>
                </div>
            `
    )
    .join("");
}

function renderPendingReferrals() {
  const container = document.getElementById("pendingReferrals");
  if (pendingReferralsData.length === 0) {
    container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No Pending Referrals</h5>
                        <p>All referrals have been processed</p>
                    </div>
                `;
    return;
  }

  container.innerHTML = pendingReferralsData
    .map(
      (ref) => `
                <div class="notification-card">
                    <div class="notification-header">
                        <div class="student-info">
                            <h5>${ref.studentName}</h5>
                            <p>${ref.studentId} | ${ref.grade}</p>
                        </div>
                        <span class="badge ${
                          ref.priority === "Urgent"
                            ? "badge-urgent"
                            : "badge-pending"
                        }">
                            ${ref.priority} Priority
                        </span>
                    </div>
                    <div class="notification-details">
                        <div class="detail-item">
                            <i class="fas fa-user-tie"></i>
                            <span><strong>Referred By:</strong> ${
                              ref.referredBy
                            }</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><strong>Reason:</strong> ${ref.reason}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Referral Date:</strong> ${
                              ref.referralDate
                            }</span>
                        </div>
                    </div>
                    <p style="margin: 10px 0; color: #666;"><strong>Notes:</strong> ${
                      ref.notes
                    }</p>
                    <div class="action-buttons">
                        <button class="btn-custom btn-assign" onclick="assignReferral(${
                          ref.id
                        })">
                            <i class="fas fa-user-plus"></i> Assign Counselor
                        </button>
                        <button class="btn-custom btn-view" onclick="viewReferralDetails(${
                          ref.id
                        })">
                            <i class="fas fa-eye"></i> View Full Details
                        </button>
                    </div>
                </div>
            `
    )
    .join("");
}

// Action Functions
function approveAppointment(id) {
  if (confirm("Are you sure you want to approve this appointment request?")) {
    const appointment = newAppointmentsData.find((apt) => apt.id === id);
    if (appointment) {
      pendingAppointmentsData.push({
        ...appointment,
        status: "Approved - Awaiting Counselor",
        approvedDate: new Date().toISOString().split("T")[0],
      });
      const index = newAppointmentsData.findIndex((apt) => apt.id === id);
      newAppointmentsData.splice(index, 1);
      updateCounts();
      renderNewAppointments();
      alert("Appointment approved successfully! Please assign a counselor.");
    }
  }
}

function rejectAppointment(id) {
  const reason = prompt("Please provide a reason for rejection:");
  if (reason) {
    const index = newAppointmentsData.findIndex((apt) => apt.id === id);
    newAppointmentsData.splice(index, 1);
    updateCounts();
    renderNewAppointments();
    alert("Appointment request has been rejected.");
  }
}

function viewDetails(id, type) {
  const appointment = newAppointmentsData.find((apt) => apt.id === id);
  if (appointment) {
    const modal = new bootstrap.Modal(
      document.getElementById("viewDetailsModal")
    );
    document.getElementById("detailsModalBody").innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Student Name:</strong> ${appointment.studentName}</p>
                            <p><strong>Student ID:</strong> ${appointment.studentId}</p>
                            <p><strong>Grade Level:</strong> ${appointment.grade}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Concern Type:</strong> ${appointment.concern}</p>
                            <p><strong>Preferred Date:</strong> ${appointment.date}</p>
                            <p><strong>Preferred Time:</strong> ${appointment.time}</p>
                        </div>
                    </div>
                    <hr>
                    <p><strong>Description:</strong></p>
                    <p>${appointment.description}</p>
                    <hr>
                    <p><strong>Contact Information:</strong></p>
                    <p><i class="fas fa-envelope"></i> ${appointment.contactEmail}</p>
                    <p><i class="fas fa-phone"></i> ${appointment.contactPhone}</p>
                `;
    modal.show();
  }
}

function openAssignModal(id) {
  currentAppointment = pendingAppointmentsData.find((apt) => apt.id === id);
  if (currentAppointment) {
    document.getElementById("modalStudentName").textContent =
      currentAppointment.studentName;
    document.getElementById("modalConcern").textContent =
      currentAppointment.concern;

    const counselorList = document.getElementById("counselorList");
    counselorList.innerHTML = counselorsData
      .map(
        (counselor) => `
                    <div class="counselor-card" onclick="selectCounselor(${counselor.id})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">${counselor.name}</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">
                                    <i class="fas fa-briefcase"></i> ${counselor.specialization}
                                </p>
                                <p class="mb-0 text-muted" style="font-size: 12px;">
                                    Current Caseload: ${counselor.currentCaseload} students
                                </p>
                            </div>
                            <div>
                                <span class="badge bg-success">${counselor.availability}</span>
                            </div>
                        </div>
                    </div>
                `
      )
      .join("");

    selectedCounselor = null;
    const modal = new bootstrap.Modal(
      document.getElementById("assignCounselorModal")
    );
    modal.show();
  }
}

function selectCounselor(counselorId) {
  selectedCounselor = counselorsData.find((c) => c.id === counselorId);

  document.querySelectorAll(".counselor-card").forEach((card) => {
    card.classList.remove("selected");
  });

  event.currentTarget.classList.add("selected");
}

function confirmAssignment() {
  if (!selectedCounselor) {
    alert("Please select a counselor before confirming.");
    return;
  }

  if (currentAppointment) {
    alert(
      `Appointment for ${currentAppointment.studentName} has been assigned to ${selectedCounselor.name}.`
    );

    const index = pendingAppointmentsData.findIndex(
      (apt) => apt.id === currentAppointment.id
    );
    pendingAppointmentsData.splice(index, 1);

    updateCounts();
    renderPendingAppointments();

    const modal = bootstrap.Modal.getInstance(
      document.getElementById("assignCounselorModal")
    );
    modal.hide();

    currentAppointment = null;
    selectedCounselor = null;
  }
}

function assignReferral(id) {
  currentAppointment = pendingReferralsData.find((ref) => ref.id === id);
  if (currentAppointment) {
    document.getElementById("modalStudentName").textContent =
      currentAppointment.studentName;
    document.getElementById("modalConcern").textContent =
      currentAppointment.reason;

    const counselorList = document.getElementById("counselorList");
    counselorList.innerHTML = counselorsData
      .map(
        (counselor) => `
                    <div class="counselor-card" onclick="selectCounselor(${counselor.id})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">${counselor.name}</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">
                                    <i class="fas fa-briefcase"></i> ${counselor.specialization}
                                </p>
                                <p class="mb-0 text-muted" style="font-size: 12px;">
                                    Current Caseload: ${counselor.currentCaseload} students
                                </p>
                            </div>
                            <div>
                                <span class="badge bg-success">${counselor.availability}</span>
                            </div>
                        </div>
                    </div>
                `
      )
      .join("");

    selectedCounselor = null;
    const modal = new bootstrap.Modal(
      document.getElementById("assignCounselorModal")
    );
    modal.show();
  }
}

function viewReferralDetails(id) {
  const referral = pendingReferralsData.find((ref) => ref.id === id);
  if (referral) {
    const modal = new bootstrap.Modal(
      document.getElementById("viewDetailsModal")
    );
    document.getElementById("detailsModalBody").innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Student Name:</strong> ${referral.studentName}</p>
                            <p><strong>Student ID:</strong> ${referral.studentId}</p>
                            <p><strong>Grade Level:</strong> ${referral.grade}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Referred By:</strong> ${referral.referredBy}</p>
                            <p><strong>Referral Date:</strong> ${referral.referralDate}</p>
                            <p><strong>Reason:</strong> ${referral.reason}</p>
                        </div>
                    </div>
                    <hr>
                    <p><strong>Detailed Notes:</strong></p>
                    <p>${referral.notes}</p>
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Please assign an appropriate counselor based on the referral reason and priority level.
                    </div>
                `;
    modal.show();
  }
}

function updateCounts() {
  document.getElementById("newCount").textContent = newAppointmentsData.length;
  document.getElementById("pendingCount").textContent =
    pendingAppointmentsData.length;
  document.getElementById("referralsCount").textContent =
    pendingReferralsData.length;
}

// Initialize
document.addEventListener("DOMContentLoaded", function () {
  renderNewAppointments();
  renderPendingAppointments();
  renderPendingReferrals();
  updateCounts();
});

// Handle tab changes
document.querySelectorAll('[data-bs-toggle="tab"]').forEach((tab) => {
  tab.addEventListener("shown.bs.tab", function (e) {
    const target = e.target.getAttribute("data-bs-target");
    if (target === "#new") {
      renderNewAppointments();
    } else if (target === "#pending") {
      renderPendingAppointments();
    } else if (target === "#referrals") {
      renderPendingReferrals();
    }
  });
});
