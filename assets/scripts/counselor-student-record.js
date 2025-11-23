const studentData = [
  {
    name: "Maria Santos",
    year: "3rd Year",
    course: "BS Psychology",
    counseling: [
      {
        date: "August 25, 2025",
        type: "Career Guidance",
        status: "Completed",
      },
      {
        date: "September 10, 2025",
        type: "Academic Counseling",
        status: "Completed",
      },
      {
        date: "October 5, 2025",
        type: "Personal Counseling",
        status: "Completed",
      },
    ],
    notes:
      "Maria shows strong interest in clinical psychology. She has been actively participating in counseling sessions and demonstrates good progress in managing academic stress. Recommended to continue with bi-weekly sessions to monitor her career planning progress.",
    referrals:
      "Referred to Career Services Office for internship opportunities in clinical psychology. Also referred to Student Wellness Center for stress management workshops.",
    remarks:
      "Excellent progress. Student is highly motivated and receptive to guidance. Schedule follow-up session in two weeks to discuss internship applications.",
  },
  {
    name: "Juan Dela Cruz",
    year: "2nd Year",
    course: "BS Computer Science",
    counseling: [
      {
        date: "September 15, 2025",
        type: "Academic Counseling",
        status: "Completed",
      },
      {
        date: "October 20, 2025",
        type: "Career Guidance",
        status: "Completed",
      },
    ],
    notes:
      "Juan is struggling with programming courses but shows determination to improve. Discussed study techniques and time management strategies. He has shown improvement in recent assessments and is becoming more confident in his abilities.",
    referrals:
      "Referred to Academic Support Center for tutoring in programming subjects. Connected with peer mentoring program for additional support.",
    remarks:
      "Continue monitoring academic performance. Encourage participation in coding workshops and hackathons to build confidence and practical skills.",
  },
  {
    name: "Ana Reyes",
    year: "4th Year",
    course: "BS Business Administration",
    counseling: [
      {
        date: "July 30, 2025",
        type: "Career Guidance",
        status: "Completed",
      },
      {
        date: "August 18, 2025",
        type: "Graduate School Planning",
        status: "Completed",
      },
      {
        date: "September 28, 2025",
        type: "Career Guidance",
        status: "Completed",
      },
      {
        date: "November 8, 2025",
        type: "Exit Interview",
        status: "Pending",
      },
    ],
    notes:
      "Ana is preparing for graduation and exploring graduate school options. She has strong leadership skills demonstrated through her role as class president. Currently preparing applications for MBA programs and considering job opportunities in management consulting.",
    referrals:
      "Referred to Graduate School Admissions Office for application guidance. Connected with Alumni Relations for networking opportunities in the business sector.",
    remarks:
      "Graduating student with excellent potential. Provide support in finalizing graduate school applications and job search strategies. Schedule exit interview before graduation.",
  },
  {
    name: "Carlos Garcia",
    year: "1st Year",
    course: "BS Engineering",
    counseling: [
      {
        date: "October 12, 2025",
        type: "Orientation Session",
        status: "Completed",
      },
      {
        date: "November 2, 2025",
        type: "Academic Counseling",
        status: "Completed",
      },
    ],
    notes:
      "Carlos is a first-year student adjusting to college life. He expressed concerns about the difficulty of engineering courses but is eager to succeed. Discussed effective study habits, campus resources, and the importance of work-life balance during the transition period.",
    referrals:
      "Referred to Student Support Services for freshman orientation programs. Recommended to join engineering student organizations for peer support.",
    remarks:
      "Normal adjustment period for first-year student. Monitor closely during first semester. Encourage engagement in campus activities to build social connections and support network.",
  },
  {
    name: "Sofia Martinez",
    year: "3rd Year",
    course: "BS Nursing",
    counseling: [
      {
        date: "August 5, 2025",
        type: "Personal Counseling",
        status: "Completed",
      },
      {
        date: "September 22, 2025",
        type: "Stress Management",
        status: "Completed",
      },
      {
        date: "October 30, 2025",
        type: "Academic Counseling",
        status: "Completed",
      },
    ],
    notes:
      "Sofia is experiencing burnout from the demanding nursing program and clinical rotations. We've worked on developing coping strategies and self-care routines. She has shown improvement in managing stress levels and maintaining a healthier work-life balance.",
    referrals:
      "Referred to Student Wellness Center for ongoing mental health support. Connected with nursing peer support group for shared experiences and coping strategies.",
    remarks:
      "Student is making good progress in stress management. Continue monitoring mental health and academic performance. Schedule monthly check-ins throughout clinical rotation period.",
  },
];

function viewStudent(index) {
  const student = studentData[index];
  document.getElementById("modalName").textContent = student.name;
  document.getElementById("modalYear").textContent = student.year;
  document.getElementById("modalCourse").textContent = student.course;

  let counselingHTML = "";
  student.counseling.forEach((session) => {
    const statusClass =
      session.status === "Completed" ? "status-completed" : "status-pending";
    counselingHTML += `
                    <div class="counseling-item">
                        <div class="counseling-date">${session.date}</div>
                        <div class="counseling-type">
                            ${session.type}
                            <span class="counseling-status ${statusClass}">${session.status}</span>
                        </div>
                    </div>
                `;
  });
  document.getElementById("counselingHistory").innerHTML = counselingHTML;

  document.getElementById("counselorNotes").textContent = student.notes;
  document.getElementById("referrals").textContent = student.referrals;
  document.getElementById("remarks").textContent = student.remarks;

  const modal = new bootstrap.Modal(document.getElementById("studentModal"));
  modal.show();
}

function searchStudents() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const table = document.getElementById("studentTable");
  const rows = table
    .getElementsByTagName("tbody")[0]
    .getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    const name = rows[i]
      .getElementsByTagName("td")[0]
      .textContent.toLowerCase();
    const year = rows[i]
      .getElementsByTagName("td")[1]
      .textContent.toLowerCase();
    const course = rows[i]
      .getElementsByTagName("td")[2]
      .textContent.toLowerCase();

    if (
      name.includes(input) ||
      year.includes(input) ||
      course.includes(input)
    ) {
      rows[i].style.display = "";
    } else {
      rows[i].style.display = "none";
    }
  }
}
