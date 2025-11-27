// Update Progress Bar
function updateProgress() {
  const form = document.getElementById("appointmentForm");
  const inputs = form.querySelectorAll("input[required], textarea[required]");
  let filled = 0;

  inputs.forEach((input) => {
    if (input.value.trim() !== "") {
      filled++;
    }
  });

  const percentage = (filled / inputs.length) * 100;
  document.getElementById("progressBar").style.width = percentage + "%";
}

// Add event listeners to all inputs
document.addEventListener("DOMContentLoaded", function () {
  const inputs = document.querySelectorAll("input, textarea");
  inputs.forEach((input) => {
    input.addEventListener("input", updateProgress);
  });
});

// Form Submission
document
  .getElementById("appointmentForm")
  .addEventListener("submit", function (e) {
    // Let the form submit normally to PHP
    // The PHP will handle saving to database

    // Optional: You can add validation here before submission
    const appointmentDate = document.getElementById("appointmentDate").value;
    const appointmentTime = document.getElementById("appointmentTime").value;

    if (!appointmentDate || !appointmentTime) {
      e.preventDefault();
      alert("Please select both appointment date and time.");
      return false;
    }

    // Form will submit normally to PHP
    console.log("Submitting appointment form...");
  });

// Reset Form
function resetForm() {
  if (
    confirm(
      "Are you sure you want to reset the form? All entered data will be lost."
    )
  ) {
    document.getElementById("appointmentForm").reset();
    updateProgress();
  }
}
