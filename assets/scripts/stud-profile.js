// Sample profile data
// Sample profile data
var profileData = window.profileData || {};

// Enable edit mode
function enableEdit() {
  document.getElementById("viewMode").style.display = "none";
  document.getElementById("editMode").style.display = "block";

  // Populate form with current data
  document.getElementById("editFirstName").value = profileData.firstName;
  document.getElementById("editMiddleName").value = profileData.middleName;
  document.getElementById("editLastName").value = profileData.lastName;
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
  if (!dateString) return '';
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString("en-US", options);
}

// Update view with profile data
function updateView() {
  const fullName = `${profileData.firstName} ${profileData.middleName ? profileData.middleName + ' ' : ''}${profileData.lastName}`;
  document.getElementById("displayName").textContent = fullName;
  document.getElementById("displayId").textContent = `Student ID: ${profileData.schoolId}`;
  document.getElementById("viewName").textContent = fullName;
  document.getElementById("viewSchoolId").textContent = profileData.schoolId;
  document.getElementById("viewBirthday").textContent = formatDate(profileData.birthday);
  document.getElementById("viewGender").textContent = profileData.gender;
  document.getElementById("viewPhone").textContent = profileData.phone;
  document.getElementById("viewDepartment").textContent = profileData.department;
  document.getElementById("viewCourse").textContent = profileData.course;
  document.getElementById("viewYearLevel").textContent = profileData.yearLevel;
}

// Handle form submission
document.getElementById("profileForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const firstName = document.getElementById("editFirstName").value;
  const middleName = document.getElementById("editMiddleName").value;
  const lastName = document.getElementById("editLastName").value;
  const birthday = document.getElementById("editBirthday").value;
  const gender = document.getElementById("editGender").value;
  const phone = document.getElementById("editPhone").value;
  const department = document.getElementById("editDepartment").value;
  const course = document.getElementById("editCourse").value;
  const yearLevel = document.getElementById("editYearLevel").value;

  const formData = new FormData();
  formData.append('firstName', firstName);
  formData.append('middleName', middleName);
  formData.append('lastName', lastName);
  formData.append('birthday', birthday);
  formData.append('gender', gender);
  formData.append('contactNumber', phone);
  formData.append('department', department);
  formData.append('course', course);
  formData.append('yearLevel', yearLevel);

  fetch('update_profile.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update local data
        profileData.firstName = firstName;
        profileData.middleName = middleName;
        profileData.lastName = lastName;
        profileData.birthday = birthday;
        profileData.gender = gender;
        profileData.phone = phone;
        profileData.department = department;
        profileData.course = course;
        profileData.yearLevel = yearLevel;

        updateView();
        cancelEdit();
        alert(data.message || "Profile updated successfully!");
      } else {
        alert("Error: " + (data.message || "Unknown error"));
      }
    })
    .catch(error => {
      alert("Network error: " + error.message);
    });
});

updateView();

