// Sample counselor data
// Sample counselor data
var counselors = window.counselors || [];


let nextId = 6;

// Display counselors in table
function displayCounselors(data = counselors) {
  const tbody = document.getElementById("counselorTableBody");
  tbody.innerHTML = "";

  data.forEach((counselor) => {
    const statusBadge =
      counselor.status === "active"
        ? '<span class="badge badge-active">Active</span>'
        : '<span class="badge badge-inactive">Inactive</span>';

    const deactivateBtn =
      counselor.status === "active"
        ? `<button class="action-btn btn-deactivate" onclick="showDeactivateModal(${counselor.id}, 'deactivate')" title="Deactivate">
                        <i class="fas fa-ban"></i>
                       </button>`
        : `<button class="action-btn btn-view" onclick="showDeactivateModal(${counselor.id}, 'activate')" title="Activate">
                        <i class="fas fa-check"></i>
                       </button>`;

    const row = `
                    <tr>
                        <td>${counselor.id}</td>
                        <td>${counselor.name}</td>
                        <td>${counselor.specialization}</td>
                        <td>${counselor.email}</td>
                        <td>${counselor.consultationTime}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="action-btn btn-edit" onclick="editCounselor(${counselor.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${deactivateBtn}
                        </td>
                    </tr>
                `;
    tbody.innerHTML += row;
  });
}

// Search functionality
document.getElementById("searchInput").addEventListener("input", function (e) {
  const searchTerm = e.target.value.toLowerCase();
  const filtered = counselors.filter(
    (c) =>
      c.name.toLowerCase().includes(searchTerm) ||
      c.email.toLowerCase().includes(searchTerm) ||
      c.specialization.toLowerCase().includes(searchTerm)
  );
  displayCounselors(filtered);
});

// Filter by specialization
document
  .getElementById("filterSpecialization")
  .addEventListener("change", function (e) {
    const spec = e.target.value;
    if (spec === "") {
      displayCounselors();
    } else {
      const filtered = counselors.filter((c) => c.specialization === spec);
      displayCounselors(filtered);
    }
  });

// Reset filters
function resetFilters() {
  document.getElementById("searchInput").value = "";
  document.getElementById("filterSpecialization").value = "";
  displayCounselors();
}

// Add counselor
function addCounselor() {
  const name = document.getElementById("addName").value;
  const specialization = document.getElementById("addSpecialization").value;
  const email = document.getElementById("addEmail").value;
  const time = document.getElementById("addTime").value;

  if (name && specialization && email && time) {
    const newCounselor = {
      id: nextId++,
      name: name,
      specialization: specialization,
      email: email,
      consultationTime: time,
      status: "active",
    };

    counselors.push(newCounselor);
    displayCounselors();

    // Close modal and reset form
    const modal = bootstrap.Modal.getInstance(
      document.getElementById("addCounselorModal")
    );
    modal.hide();
    document.getElementById("addCounselorForm").reset();
  }
}

// Edit counselor
function editCounselor(id) {
  const counselor = counselors.find((c) => c.id === id);
  if (counselor) {
    document.getElementById("editId").value = counselor.id;
    document.getElementById("editName").value = counselor.name;
    document.getElementById("editSpecialization").value =
      counselor.specialization;
    document.getElementById("editEmail").value = counselor.email;
    document.getElementById("editTime").value = counselor.consultationTime;

    const modal = new bootstrap.Modal(
      document.getElementById("editCounselorModal")
    );
    modal.show();
  }
}

// Update counselor
function updateCounselor() {
  const id = parseInt(document.getElementById("editId").value);
  const counselor = counselors.find((c) => c.id === id);

  if (counselor) {
    counselor.name = document.getElementById("editName").value;
    counselor.specialization =
      document.getElementById("editSpecialization").value;
    counselor.email = document.getElementById("editEmail").value;
    counselor.consultationTime = document.getElementById("editTime").value;

    displayCounselors();

    const modal = bootstrap.Modal.getInstance(
      document.getElementById("editCounselorModal")
    );
    modal.hide();
  }
}

// Show deactivate/activate modal
function showDeactivateModal(id, action) {
  const counselor = counselors.find((c) => c.id === id);
  if (counselor) {
    document.getElementById("deactivateId").value = id;
    document.getElementById("deactivateName").textContent = counselor.name;
    document.getElementById("deactivateAction").textContent = action;
    document.getElementById("deactivateButtonText").textContent =
      action.charAt(0).toUpperCase() + action.slice(1);

    const modal = new bootstrap.Modal(
      document.getElementById("deactivateModal")
    );
    modal.show();
  }
}

// Confirm deactivation/activation
function confirmDeactivation() {
  const id = parseInt(document.getElementById("deactivateId").value);
  const counselor = counselors.find((c) => c.id === id);

  if (counselor) {
    counselor.status = counselor.status === "active" ? "inactive" : "active";
    displayCounselors();

    const modal = bootstrap.Modal.getInstance(
      document.getElementById("deactivateModal")
    );
    modal.hide();
  }
}

// Initial display
displayCounselors();
