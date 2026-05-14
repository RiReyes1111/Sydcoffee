<?php
session_start();
include "auth/config.php";

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

$error = '';
$success = '';

$redirect     = isset($_GET['redirect'])    ? $_GET['redirect']    
              : (isset($_POST['redirect'])  ? $_POST['redirect']   : '');
$safeRedirect = ($redirect === 'checkout')  ? 'checkout.php'       : 'menu.php';

$defaultTab = isset($_GET['tab']) && $_GET['tab'] === 'register' ? 'register' : 'login';

// ---- LOGIN ----
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username && $password) {
        $hashed = md5($password);
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND role = 'user'");
        $stmt->bind_param("ss", $username, $hashed);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['logged_in'] = true;
            $_SESSION['name']      = $user['name'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['user_id']   = $user['id'];

            header("Location: " . ($user['role'] === 'admin' ? 'admin.php' : $safeRedirect));
            exit();
        } else {
            $error      = "Invalid username or password.";
            $defaultTab = 'login';
        }
    } else {
        $error      = "Please fill in all fields.";
        $defaultTab = 'login';
    }
}

// ---- REGISTER ----
if (isset($_POST['register'])) {
    $name     = trim($_POST['reg_name']);
    $email    = trim($_POST['reg_email']);
    $username = trim($_POST['reg_username']);
    $password = $_POST['reg_password'];

    if ($name && $email && $username && $password) {
        $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error      = "Username or email already exists.";
            $defaultTab = 'register';
        } else {
            $hashed = md5($password);
            $stmt   = $conn->prepare("INSERT INTO users (name, email, username, password, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->bind_param("ssss", $name, $email, $username, $hashed);

            if ($stmt->execute()) {
                $_SESSION['logged_in'] = true;
                $_SESSION['name']      = $name;
                $_SESSION['role']      = 'user';
                $_SESSION['user_id']   = $stmt->insert_id;

                header("Location: " . $safeRedirect);
                exit();
            } else {
                $error      = "Registration failed. Please try again.";
                $defaultTab = 'register';
            }
        }
    } else {
        $error      = "Please fill in all fields.";
        $defaultTab = 'register';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — SYD Coffee</title>
<link rel="icon" type="image/png" href="images/logosydnobg.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/logincss.css">
</head>
<body>

<div class="logo-wrap">
  <img src="images/logosydnobg.png" alt="SYD Coffee">
</div>

<div class="auth-card">

  <div class="tab-switcher">
    <button type="button" class="tab-btn <?= $defaultTab === 'login'    ? 'active' : '' ?>" onclick="switchTab('login')">Login</button>
    <button type="button" class="tab-btn <?= $defaultTab === 'register' ? 'active' : '' ?>" onclick="switchTab('register')">Register</button>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <!-- LOGIN PANEL -->
  <div class="form-panel <?= $defaultTab === 'login' ? 'active' : '' ?>" id="panel-login">
    <div class="form-header">
      <h2>Welcome back.</h2>
      <p>Sign in to your account</p>
    </div>
    <form method="POST">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" required>
      </div>
      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" name="login" class="btn-primary">Sign In</button>
    </form>
  </div>

  <!-- REGISTER PANEL -->
  <div class="form-panel <?= $defaultTab === 'register' ? 'active' : '' ?>" id="panel-register">
    <div class="form-header">
      <h2>Create account.</h2>
      <p>Join SYD Coffee</p>
    </div>
    <form method="POST">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <div class="input-group">
        <label>Full Name</label>
        <input type="text" name="reg_name" required>
      </div>
      <div class="input-group">
        <label>Email</label>
        <input type="email" name="reg_email" required>
      </div>
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="reg_username" required>
      </div>
      <div class="input-group">
        <label>Password</label>
        <input type="password" name="reg_password" required>
      </div>
      <button type="submit" name="register" class="btn-primary">Create Account</button>
    </form>
  </div>

</div>

<script>
function switchTab(tab) {
  document.getElementById('panel-login').classList.toggle('active', tab === 'login');
  document.getElementById('panel-register').classList.toggle('active', tab === 'register');
  document.querySelectorAll('.tab-btn')[0].classList.toggle('active', tab === 'login');
  document.querySelectorAll('.tab-btn')[1].classList.toggle('active', tab === 'register');
}
</script>

</body>
</html>