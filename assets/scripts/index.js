// Login is handled by PHP, so we don't need the submit listener here preventing default.
// If there was one, it should be removed.

function sendVerificationCode() {
  const email = document.getElementById("resetEmail").value.trim();
  const alertDiv = document.getElementById("emailAlert");
  const sendBtn = document.querySelector('#forgotPasswordModal .btn-verify');

  if (!email) {
    alertDiv.innerHTML = '<div class="alert alert-danger">Please enter your email.</div>';
    return;
  }

  // Email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alertDiv.innerHTML = '<div class="alert alert-danger">Please enter a valid email address.</div>';
    return;
  }

  // Show loading state
  const originalText = sendBtn.innerHTML;
  sendBtn.disabled = true;
  sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
  alertDiv.innerHTML = '';

  const formData = new FormData();
  formData.append('action', 'send_code');
  formData.append('email', email);

  fetch('forgot_password.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      sendBtn.disabled = false;
      sendBtn.innerHTML = originalText;
      
      if (data.success) {
        alertDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' + data.message + '</div>';
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById("forgotPasswordModal")).hide();
          new bootstrap.Modal(document.getElementById("verificationModal")).show();
        }, 2000);
      } else {
        alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + data.message + '</div>';
      }
    })
    .catch(error => {
      sendBtn.disabled = false;
      sendBtn.innerHTML = originalText;
      alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>An error occurred. Please try again.</div>';
    });
}

function verifyCode() {
  const code = document.getElementById("verificationCode").value.trim();
  const alertDiv = document.getElementById("codeAlert");
  const verifyBtn = document.querySelector('#verificationModal .btn-verify');

  if (!code) {
    alertDiv.innerHTML = '<div class="alert alert-danger">Please enter the verification code.</div>';
    return;
  }

  if (code.length !== 6 || !/^\d+$/.test(code)) {
    alertDiv.innerHTML = '<div class="alert alert-danger">Please enter a valid 6-digit code.</div>';
    return;
  }

  // Show loading state
  const originalText = verifyBtn.innerHTML;
  verifyBtn.disabled = true;
  verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
  alertDiv.innerHTML = '';

  const formData = new FormData();
  formData.append('action', 'verify_code');
  formData.append('code', code);

  fetch('forgot_password.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      verifyBtn.disabled = false;
      verifyBtn.innerHTML = originalText;
      
      if (data.success) {
        alertDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Code verified successfully!</div>';
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById("verificationModal")).hide();
          new bootstrap.Modal(document.getElementById("resetPasswordModal")).show();
        }, 1500);
      } else {
        alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + data.message + '</div>';
      }
    })
    .catch(error => {
      verifyBtn.disabled = false;
      verifyBtn.innerHTML = originalText;
      alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>An error occurred. Please try again.</div>';
    });
}

function resendCode() {
  const alertDiv = document.getElementById("codeAlert");
  const resendBtn = document.querySelector('#verificationModal .btn-resend');
  
  // Show loading state
  if (resendBtn) {
    resendBtn.disabled = true;
    resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
  }
  alertDiv.innerHTML = '';

  const formData = new FormData();
  formData.append('action', 'resend_code');

  fetch('forgot_password.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (resendBtn) {
        resendBtn.disabled = false;
        resendBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Resend Code';
      }
      
      if (data.success) {
        alertDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' + data.message + '</div>';
        document.getElementById("verificationCode").value = '';
      } else {
        alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + data.message + '</div>';
      }
    })
    .catch(error => {
      if (resendBtn) {
        resendBtn.disabled = false;
        resendBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Resend Code';
      }
      alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>An error occurred. Please try again.</div>';
    });
}

function resetPassword() {
  const newPassword = document.getElementById("newPassword").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const alertDiv = document.getElementById("resetAlert");
  const resetBtn = document.querySelector('#resetPasswordModal .btn-reset');

  if (!newPassword || newPassword.length < 6) {
    alertDiv.innerHTML =
      '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Password must be at least 6 characters long.</div>';
    return;
  }

  if (newPassword !== confirmPassword) {
    alertDiv.innerHTML =
      '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Passwords do not match.</div>';
    return;
  }

  // Show loading state
  const originalText = resetBtn.innerHTML;
  resetBtn.disabled = true;
  resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...';
  alertDiv.innerHTML = '';

  const formData = new FormData();
  formData.append('action', 'reset_password');
  formData.append('password', newPassword);
  formData.append('confirm_password', confirmPassword);

  fetch('forgot_password.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      resetBtn.disabled = false;
      resetBtn.innerHTML = originalText;
      
      if (data.success) {
        alertDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' + (data.message || 'Password reset successfully!') + '</div>';
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById("resetPasswordModal")).hide();
          // Clear inputs
          document.getElementById("resetEmail").value = "";
          document.getElementById("verificationCode").value = "";
          document.getElementById("newPassword").value = "";
          document.getElementById("confirmPassword").value = "";
          // Clear all alerts
          document.getElementById("emailAlert").innerHTML = "";
          document.getElementById("codeAlert").innerHTML = "";
          document.getElementById("resetAlert").innerHTML = "";
        }, 2000);
      } else {
        alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + data.message + '</div>';
      }
    })
    .catch(error => {
      resetBtn.disabled = false;
      resetBtn.innerHTML = originalText;
      alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>An error occurred. Please try again.</div>';
    });
}

document.querySelectorAll(".modal").forEach((modal) => {
  modal.addEventListener("hidden.bs.modal", function () {
    this.querySelectorAll(".alert").forEach((alert) => alert.remove());
  });
});
