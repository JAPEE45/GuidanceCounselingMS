<?php
session_start();
require_once 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Check if email is verified (skip for admin and counselor)
            if ($user['role'] === 'student' && isset($user['email_verified']) && $user['email_verified'] == 0) {
                $message = "Please verify your email address before logging in. Check your inbox for the verification link.";
            } else {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'student':
                        header("Location: html/student/dashboard.php");
                        break;
                    case 'counselor':
                        header("Location: html/counselor/dashboard.php");
                        break;
                    case 'admin':
                        header("Location: html/admin/dashboard.php");
                        break;
                    default:
                        $message = "Invalid role assigned to user.";
                }
                exit;
            }
        } else {
            $message = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Guidance Counseling - Login</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <link rel="stylesheet" href="./assets/styles/layouts/index.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/scripts/index.js"></script>
  </head>
  <body>
    <div class="login-container">
      <div class="login-header">
        <i class="fas fa-user-tie"></i>
        <h2>Guidance Counseling</h2>
        <p>Management System</p>
      </div>
      <div class="login-body">
        <?php if ($message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form id="loginForm" method="POST" action="">
          <div class="mb-3">
            <label for="username" class="form-label">Email Address</label>
            <div class="input-group">
              <!-- <span class="input-group-text"><i class="fas fa-user"></i></span> -->
              <input
                type="email"
                class="form-control"
                id="username"
                name="username"
                placeholder="Enter your email"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <!-- <span class="input-group-text"><i class="fas fa-lock"></i></span> -->
              <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
              />
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe" />
              <label class="form-check-label" for="rememberMe">
                Remember me
              </label>
            </div>
            <p
              class="forgot-password mb-0"
              data-bs-toggle="modal"
              data-bs-target="#forgotPasswordModal"
            >
              Forgot password?
            </p>
          </div>

          <button type="submit" class="btn btn-login">
            Login
          </button>

          <div class="divider">
            <span>OR</span>
          </div>

          <button
            type="button"
            class="btn btn-register"
            onclick="window.location.href='./html/student/registration.php'"
          >
            <i class="fas fa-user-plus me-2"></i>Create New Account
          </button>
        </form>
      </div>
    </div>

    <!-- Forgot Password Modal - Step 1: Email -->
    <div
      class="modal fade"
      id="forgotPasswordModal"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-key me-2"></i>Forgot Password
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-4">
              Enter your email address to receive a verification code.
            </p>
            <div class="mb-3">
              <label for="resetEmail" class="form-label">Email Address</label>
              <input
                type="email"
                class="form-control"
                id="resetEmail"
                placeholder="Enter email address"
                required
              />
            </div>
            <div id="emailAlert"></div>
            <button
              type="button"
              class="btn btn-verify"
              onclick="sendVerificationCode()"
            >
              <i class="fas fa-paper-plane me-2"></i>Send Verification Code
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Verification Code Modal - Step 2 -->
    <div
      class="modal fade"
      id="verificationModal"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-shield-alt me-2"></i>Enter Verification Code
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-4">
              We've sent a 6-digit verification code to your email. Please enter it
              below. The code will expire in 15 minutes.
            </p>
            <div class="mb-3">
              <label for="verificationCode" class="form-label"
                >Verification Code</label
              >
              <input
                type="text"
                class="form-control"
                id="verificationCode"
                placeholder="Enter 6-digit code"
                maxlength="6"
                required
              />
            </div>
            <div id="codeAlert"></div>
            <button type="button" class="btn btn-verify" onclick="verifyCode()">
              <i class="fas fa-check-circle me-2"></i>Verify Code
            </button>
            <div class="text-center mt-3">
              <p class="text-muted mb-2">Didn't receive the code?</p>
              <button type="button" class="btn btn-link btn-resend p-0" onclick="resendCode()">
                <i class="fas fa-redo me-2"></i>Resend Code
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reset Password Modal - Step 3 -->
    <div
      class="modal fade"
      id="resetPasswordModal"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-lock-open me-2"></i>Reset Password
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-4">
              Create a new password for your account.
            </p>
            <div class="mb-3">
              <label for="newPassword" class="form-label">New Password</label>
              <input
                type="password"
                class="form-control"
                id="newPassword"
                placeholder="Enter new password"
                required
              />
            </div>
            <div class="mb-3">
              <label for="confirmPassword" class="form-label"
                >Confirm New Password</label
              >
              <input
                type="password"
                class="form-control"
                id="confirmPassword"
                placeholder="Confirm new password"
                required
              />
            </div>
            <div id="resetAlert"></div>
            <button
              type="button"
              class="btn btn-reset"
              onclick="resetPassword()"
            >
              <i class="fas fa-save me-2"></i>Reset Password
            </button>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
