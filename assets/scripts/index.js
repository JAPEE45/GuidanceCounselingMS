// Login is handled by PHP, so we don't need the submit listener here preventing default.
// If there was one, it should be removed.

function sendVerificationCode() {
  const email = document.getElementById("resetEmail").value;
  const alertDiv = document.getElementById("emailAlert");

  if (!email) {
    alertDiv.innerHTML = '<div class="alert alert-danger">Please enter your email.</div>';
    return;
  }

  const formData = new FormData();
  formData.append('action', 'send_code');
  formData.append('email', email);

  fetch('forgot_password.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alertDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById("forgotPasswordModal")).hide();
          new bootstrap.Modal(document.getElementById("verificationModal")).show();
        }, 2000);
      } else {
        alertDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
      }
    });
}

function verifyCode() {
  const code = document.getElementById("verificationCode").value;
  const alertDiv = document.getElementById("codeAlert");

  const formData = new FormData();
  formData.append('action', 'verify_code');
  formData.append('code', code);

  fetch('forgot_password.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alertDiv.innerHTML = '<div class="alert alert-success">Code verified successfully!</div>';
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById("verificationModal")).hide();
          new bootstrap.Modal(document.getElementById("resetPasswordModal")).show();
        }, 1500);
      } else {
        alertDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
      }
    });
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

  const formData = new FormData();
  formData.append('action', 'reset_password');
  formData.append('password', newPassword);

  fetch('forgot_password.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alertDiv.innerHTML = '<div class="alert alert-success">Password reset successfully!</div>';
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById("resetPasswordModal")).hide();
          // Clear inputs
          document.getElementById("resetEmail").value = "";
          document.getElementById("verificationCode").value = "";
          document.getElementById("newPassword").value = "";
          document.getElementById("confirmPassword").value = "";
        }, 2000);
      } else {
        alertDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
      }
    });
}

document.querySelectorAll(".modal").forEach((modal) => {
  modal.addEventListener("hidden.bs.modal", function () {
    this.querySelectorAll(".alert").forEach((alert) => alert.remove());
  });
});
