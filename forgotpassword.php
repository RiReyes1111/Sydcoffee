<?php
session_start();
include "auth/config.php";

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

$step    = 'find';   // find → verify → reset → done
$error   = '';
$success = '';

$redirect     = isset($_GET['redirect']) ? $_GET['redirect'] : '';
$safeRedirect = ($redirect === 'checkout') ? 'checkout.php' : 'menu.php';

// ── STEP 1: Find account by username or email ──
if (isset($_POST['find_account'])) {
    $lookup = trim($_POST['lookup']);
    if (!$lookup) {
        $error = "Please enter your username or email.";
        $step  = 'find';
    } else {
        $stmt = $conn->prepare("SELECT id, name, username, email FROM users WHERE (username=? OR email=?) AND role='user'");
        $stmt->bind_param("ss", $lookup, $lookup);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "No account found with that username or email.";
            $step  = 'find';
        } else {
            $user = $result->fetch_assoc();
            $_SESSION['fp_user_id'] = $user['id'];
            $_SESSION['fp_name']    = $user['name'];
            // Security question: last 3 chars of username + first letter of name
            // We'll use a simple "confirm your name" approach since there are no security questions in the DB
            $step = 'verify';
        }
    }
}

// ── STEP 2: Verify identity (confirm full name) ──
if (isset($_POST['verify_identity'])) {
    if (!isset($_SESSION['fp_user_id'])) {
        header("Location: forgotpassword.php"); exit();
    }
    $inputName = trim($_POST['confirm_name']);
    if (strtolower($inputName) !== strtolower($_SESSION['fp_name'])) {
        $error = "Name does not match our records.";
        $step  = 'verify';
    } else {
        $_SESSION['fp_verified'] = true;
        $step = 'reset';
    }
}

// ── STEP 3: Set new password ──
if (isset($_POST['reset_password'])) {
    if (!isset($_SESSION['fp_verified']) || !$_SESSION['fp_verified'] || !isset($_SESSION['fp_user_id'])) {
        header("Location: forgotpassword.php"); exit();
    }
    $newPw  = $_POST['new_password'];
    $confPw = $_POST['confirm_password'];

    if (strlen($newPw) < 6) {
        $error = "Password must be at least 6 characters.";
        $step  = 'reset';
    } elseif ($newPw !== $confPw) {
        $error = "Passwords do not match.";
        $step  = 'reset';
    } else {
        $hashed = md5($newPw);
        $uid    = intval($_SESSION['fp_user_id']);
        $conn->query("UPDATE users SET password='$hashed' WHERE id=$uid");

        unset($_SESSION['fp_user_id'], $_SESSION['fp_name'], $_SESSION['fp_verified']);
        $step = 'done';
    }
}

// Restore step from session if returning after POST with errors
if ($step === 'find' && empty($error) && isset($_SESSION['fp_user_id'])) {
    $step = isset($_SESSION['fp_verified']) && $_SESSION['fp_verified'] ? 'reset' : 'verify';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password — SYD Coffee</title>
<link rel="icon" type="image/png" href="images/logosydnobg.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/forgotpasswordcss.css">
</head>
<body>

<div class="logo-wrap">
  <a href="login.php<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>">
    <img src="images/logosydnobg.png" alt="SYD Coffee">
  </a>
</div>

<div class="fp-card">

  <!-- Progress steps -->
  <div class="steps">
    <div class="step <?= in_array($step, ['find','verify','reset','done']) ? 'done' : '' ?> <?= $step==='find' ? 'active' : '' ?>">
      <div class="step-dot">1</div>
      <div class="step-label">Find Account</div>
    </div>
    <div class="step-line <?= in_array($step, ['verify','reset','done']) ? 'filled' : '' ?>"></div>
    <div class="step <?= in_array($step, ['verify','reset','done']) ? 'done' : '' ?> <?= $step==='verify' ? 'active' : '' ?>">
      <div class="step-dot">2</div>
      <div class="step-label">Verify</div>
    </div>
    <div class="step-line <?= in_array($step, ['reset','done']) ? 'filled' : '' ?>"></div>
    <div class="step <?= in_array($step, ['reset','done']) ? 'done' : '' ?> <?= $step==='reset' ? 'active' : '' ?>">
      <div class="step-dot">3</div>
      <div class="step-label">New Password</div>
    </div>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- STEP 1: Find account -->
  <?php if ($step === 'find'): ?>
  <div class="fp-header">
    <h2>Forgot your password?</h2>
    <p>Enter your username or email to get started.</p>
  </div>
  <form method="POST">
    <div class="input-group">
      <label>Username or Email</label>
      <input type="text" name="lookup" required autofocus>
    </div>
    <button type="submit" name="find_account" class="btn-primary">Find My Account</button>
    <a href="login.php<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>" class="back-link">← Back to Login</a>
  </form>
  <?php endif; ?>

  <!-- STEP 2: Verify identity -->
  <?php if ($step === 'verify'): ?>
  <div class="fp-header">
    <h2>Verify your identity</h2>
    <p>To confirm it's you, please enter your full name as registered.</p>
  </div>
  <form method="POST">
    <div class="input-group">
      <label>Full Name</label>
      <input type="text" name="confirm_name" required autofocus placeholder="As you registered it">
    </div>
    <button type="submit" name="verify_identity" class="btn-primary">Verify</button>
    <a href="forgotpassword.php<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>" class="back-link">← Start over</a>
  </form>
  <?php endif; ?>

  <!-- STEP 3: Reset password -->
  <?php if ($step === 'reset'): ?>
  <div class="fp-header">
    <h2>Set a new password</h2>
    <p>Choose something secure — at least 6 characters.</p>
  </div>
  <form method="POST">
    <div class="input-group">
      <label>New Password</label>
      <div class="password-wrap">
        <input type="password" name="new_password" id="new-pw" minlength="6" required autofocus>
        <button type="button" class="toggle-pw" onclick="togglePw('new-pw', this)" aria-label="Show password">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
    </div>
    <div class="input-group">
      <label>Confirm Password</label>
      <div class="password-wrap">
        <input type="password" name="confirm_password" id="conf-pw" minlength="6" required>
        <button type="button" class="toggle-pw" onclick="togglePw('conf-pw', this)" aria-label="Show password">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
    </div>
    <button type="submit" name="reset_password" class="btn-primary">Reset Password</button>
  </form>
  <?php endif; ?>

  <!-- STEP 4: Done -->
  <?php if ($step === 'done'): ?>
  <div class="done-state">
    <div class="done-icon">✓</div>
    <h2>Password updated!</h2>
    <p>Your password has been changed successfully. You can now log in.</p>
    <a href="login.php<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>" class="btn-primary" style="display:block;text-align:center;text-decoration:none;margin-top:20px;">Go to Login</a>
  </div>
  <?php endif; ?>

</div>

<script>
function togglePw(inputId, btn) {
  const input = document.getElementById(inputId);
  const isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  const eyeOn  = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
  const eyeOff = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
  btn.innerHTML = isHidden ? eyeOff : eyeOn;
}
</script>
</body>
</html>