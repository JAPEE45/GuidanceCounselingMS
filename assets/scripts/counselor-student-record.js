// Sample student data
var studentData = window.studentData || [];


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
