// Sample profile data
let profileData = {
  name: "Juan Dela Cruz",
  schoolId: "2024-00001",
  birthday: "2003-01-15",
  gender: "Male",
  phone: "+63 912 345 6789",
  department: "College of Computer Studies",
  course: "Bachelor of Science in Information Technology",
  yearLevel: "3rd Year",
};

// Enable edit mode
function enableEdit() {
  document.getElementById("viewMode").style.display = "none";
  document.getElementById("editMode").style.display = "block";

  // Populate form with current data
  document.getElementById("editName").value = profileData.name;
  document.getElementById("editSchoolId").value = profileData.schoolId;
  document.getElementById("editBirthday").value = profileData.birthday;
  document.getElementById("editGender").value = profileData.gender;
  document.getElementById("editPhone").value = profileData.phone;
  document.getElementById("editDepartment").value = profileData.department;
  document.getElementById("editCourse").value = profileData.course;
  document.getElementById("editYearLevel").value = profileData.yearLevel;
}

// Cancel edit mode
function cancelEdit() {
  document.getElementById("editMode").style.display = "none";
  document.getElementById("viewMode").style.display = "block";
}

// Format date for display
function formatDate(dateString) {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString("en-US", options);
}

// Update view with profile data
function updateView() {
  document.getElementById("displayName").textContent = profileData.name;
  document.getElementById(
    "displayId"
  ).textContent = `Student ID: ${profileData.schoolId}`;
  document.getElementById("viewName").textContent = profileData.name;
  document.getElementById("viewSchoolId").textContent = profileData.schoolId;
  document.getElementById("viewBirthday").textContent = formatDate(
    profileData.birthday
  );
  document.getElementById("viewGender").textContent = profileData.gender;
  document.getElementById("viewPhone").textContent = profileData.phone;
  document.getElementById("viewDepartment").textContent =
    profileData.department;
  document.getElementById("viewCourse").textContent = profileData.course;
  document.getElementById("viewYearLevel").textContent = profileData.yearLevel;
}

// Handle form submission
document.getElementById("profileForm").addEventListener("submit", function (e) {
  e.preventDefault();

  // Update profile data
  profileData.name = document.getElementById("editName").value;
  profileData.birthday = document.getElementById("editBirthday").value;
  profileData.gender = document.getElementById("editGender").value;
  profileData.phone = document.getElementById("editPhone").value;
  profileData.department = document.getElementById("editDepartment").value;
  profileData.course = document.getElementById("editCourse").value;
  profileData.yearLevel = document.getElementById("editYearLevel").value;

  // Update view
  updateView();

  // Switch back to view mode
  cancelEdit();

  // Show success message
  alert("Profile updated successfully!");
});


updateView();
