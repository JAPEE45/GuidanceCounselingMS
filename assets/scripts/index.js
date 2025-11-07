// Store verification code for demo purposes
let generatedCode = "";
let userEmail = "";

// Login form submission
document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  const rememberMe = document.getElementById("rememberMe").checked;

  // Demo login logic
  alert(
    `Login attempted with:\nUsername: ${username}\nRemember Me: ${rememberMe}`
  );
});

// Step 1: Send verification code
function sendVerificationCode() {
  const email = document.getElementById("resetEmail").value;
  const alertDiv = document.getElementById("emailAlert");

  if (!email || !email.includes("@")) {
    alertDiv.innerHTML =
      '<div class="alert alert-danger">Please enter a valid email address.</div>';
    return;
  }

  // Generate random 6-digit code
  generatedCode = Math.floor(100000 + Math.random() * 900000).toString();
  userEmail = email;
  console.log(generatedCode);
  alertDiv.innerHTML =
    '<div class="alert alert-success">Verification code sent! (Demo code: ' +
    generatedCode +
    ")</div>";

  // Move to verification modal after 2 seconds
  setTimeout(() => {
    bootstrap.Modal.getInstance(
      document.getElementById("forgotPasswordModal")
    ).hide();
    new bootstrap.Modal(document.getElementById("verificationModal")).show();
  }, 2000);
}

// Step 2: Verify code
function verifyCode() {
  const code = document.getElementById("verificationCode").value;
  const alertDiv = document.getElementById("codeAlert");

  if (code === generatedCode) {
    alertDiv.innerHTML =
      '<div class="alert alert-success">Code verified successfully!</div>';

    // Move to reset password modal after 1.5 seconds
    setTimeout(() => {
      bootstrap.Modal.getInstance(
        document.getElementById("verificationModal")
      ).hide();
      new bootstrap.Modal(document.getElementById("resetPasswordModal")).show();
    }, 1500);
  } else {
    alertDiv.innerHTML =
      '<div class="alert alert-danger">Invalid verification code. Please try again.</div>';
  }
}

// Step 3: Reset password
function resetPassword() {
  const newPassword = document.getElementById("newPassword").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const alertDiv = document.getElementById("resetAlert");

  if (!newPassword || newPassword.length < 6) {
    alertDiv.innerHTML =
      '<div class="alert alert-danger">Password must be at least 6 characters long.</div>';
    return;
  }

  if (newPassword !== confirmPassword) {
    alertDiv.innerHTML =
      '<div class="alert alert-danger">Passwords do not match.</div>';
    return;
  }

  alertDiv.innerHTML =
    '<div class="alert alert-success">Password reset successfully!</div>';

  // Close modal and reset form after 2 seconds
  setTimeout(() => {
    bootstrap.Modal.getInstance(
      document.getElementById("resetPasswordModal")
    ).hide();
    document.getElementById("resetEmail").value = "";
    document.getElementById("verificationCode").value = "";
    document.getElementById("newPassword").value = "";
    document.getElementById("confirmPassword").value = "";
    generatedCode = "";
    userEmail = "";
  }, 2000);
}

// Clear alerts when modals are closed
document.querySelectorAll(".modal").forEach((modal) => {
  modal.addEventListener("hidden.bs.modal", function () {
    this.querySelectorAll(".alert").forEach((alert) => alert.remove());
  });
});
