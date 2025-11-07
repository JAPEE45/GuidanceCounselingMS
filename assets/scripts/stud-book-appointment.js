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
    e.preventDefault();

    // Collect form data
    const formData = new FormData(this);
    const data = {};

    formData.forEach((value, key) => {
      data[key] = value;
    });

    // Display success message
    alert(
      "Your appointment has been successfully submitted! The Guidance Office will review your information and contact you soon."
    );

    // Optional: Reset form after submission
    // this.reset();
    // updateProgress();

    console.log("Form Data:", data);
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
