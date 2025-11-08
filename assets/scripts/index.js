let generatedCode = "";
let userEmail = "";

document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  const rememberMe = document.getElementById("rememberMe").checked;

  if (username === "stud" && password === "stud") {
    window.location.href = "./html/student/dashboard.html";
    localStorage.setItem("rememberMe", rememberMe);
  } else {
    alert("Invalid username or password.");
  }
});

function sendVerificationCode() {
  const phoneNumber = document.getElementById("resetPhoneNumber").value;
  const alertDiv = document.getElementById("emailAlert");

  generatedCode = Math.floor(100000 + Math.random() * 900000).toString();
  userEmail = phoneNumber;
  console.log(generatedCode);
  alertDiv.innerHTML =
    '<div class="alert alert-success">Verification code sent! (Demo code: ' +
    generatedCode +
    ")</div>";

  setTimeout(() => {
    bootstrap.Modal.getInstance(
      document.getElementById("forgotPasswordModal")
    ).hide();
    new bootstrap.Modal(document.getElementById("verificationModal")).show();
  }, 2000);
}

function verifyCode() {
  const code = document.getElementById("verificationCode").value;
  const alertDiv = document.getElementById("codeAlert");

  if (code === generatedCode) {
    alertDiv.innerHTML =
      '<div class="alert alert-success">Code verified successfully!</div>';

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

  setTimeout(() => {
    bootstrap.Modal.getInstance(
      document.getElementById("resetPasswordModal")
    ).hide();
    document.getElementById("resetPhoneNumber").value = "";
    document.getElementById("verificationCode").value = "";
    document.getElementById("newPassword").value = "";
    document.getElementById("confirmPassword").value = "";
    generatedCode = "";
    userEmail = "";
  }, 2000);
}

document.querySelectorAll(".modal").forEach((modal) => {
  modal.addEventListener("hidden.bs.modal", function () {
    this.querySelectorAll(".alert").forEach((alert) => alert.remove());
  });
});
