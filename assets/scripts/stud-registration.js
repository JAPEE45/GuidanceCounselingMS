const registrationForm = document.getElementById("registrationForm");
const phoneVerificationModal = new bootstrap.Modal(
  document.getElementById("phoneVerificationModal")
);
const otpVerificationModal = new bootstrap.Modal(
  document.getElementById("otpVerificationModal")
);
const successModal = new bootstrap.Modal(
  document.getElementById("successModal")
);

const displayPhoneNumber = document.getElementById("displayPhoneNumber");
const confirmPhoneBtn = document.getElementById("confirmPhoneBtn");
const backToPhoneBtn = document.getElementById("backToPhoneBtn");
const verifyOtpBtn = document.getElementById("verifyOtpBtn");
const otpInput = document.getElementById("otpInput");
const resendCode = document.getElementById("resendCode");

let generatedOtp = "";

// Form submission
registrationForm.addEventListener("submit", function (e) {
  e.preventDefault();

  const phoneNumber = document.getElementById("contactNumber").value;
  displayPhoneNumber.textContent = phoneNumber;

  phoneVerificationModal.show();
});

// Confirm phone number and send OTP
confirmPhoneBtn.addEventListener("click", function () {
  generatedOtp = Math.floor(100000 + Math.random() * 900000).toString();
  console.log("Generated OTP:", generatedOtp);

  phoneVerificationModal.hide();

  setTimeout(() => {
    otpVerificationModal.show();
    otpInput.value = "";
    otpInput.focus();
  }, 300);
});

// Back to phone verification
backToPhoneBtn.addEventListener("click", function () {
  otpVerificationModal.hide();
  setTimeout(() => {
    phoneVerificationModal.show();
  }, 300);
});

// Verify OTP
verifyOtpBtn.addEventListener("click", function () {
  const enteredOtp = otpInput.value;

  if (enteredOtp.length !== 6) {
    alert("Please enter a 6-digit code.");
    return;
  }

  if (enteredOtp === generatedOtp) {
    otpVerificationModal.hide();

    setTimeout(() => {
      successModal.show();

      setTimeout(() => {
        window.location.href = "../../index.html";
      }, 2000);
    }, 300);
  } else {
    alert("Invalid verification code. Please try again.");
    otpInput.value = "";
    otpInput.focus();
  }
});

// Resend code
resendCode.addEventListener("click", function (e) {
  e.preventDefault();
  generatedOtp = Math.floor(100000 + Math.random() * 900000).toString();
  console.log("Resent OTP:", generatedOtp);
  alert("A new verification code has been sent to your phone number.");
  otpInput.value = "";
  otpInput.focus();
});

// Allow only numbers in OTP input
otpInput.addEventListener("input", function (e) {
  this.value = this.value.replace(/[^0-9]/g, "");
});

// Auto-submit OTP when 6 digits are entered
otpInput.addEventListener("input", function () {
  if (this.value.length === 6) {
    setTimeout(() => {
      verifyOtpBtn.click();
    }, 500);
  }
});
