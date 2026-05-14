<?php
session_start();
include('../auth/config.php');

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("
        SELECT * FROM users 
        WHERE username = ? AND password = ? AND role = 'admin'
    ");

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        $_SESSION['logged_in'] = true;
        $_SESSION['name']      = $admin['name'];
        $_SESSION['role']      = 'admin';
        $_SESSION['user_id']   = $admin['id'];

        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid admin login.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — SYD Coffee</title>
<link rel="icon" type="image/png" href="../images/logosydnobg.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/logincss.css">
</head>
<body>

<div class="logo-wrap">
  <img src="../images/logosydnobg.png" alt="SYD Coffee">
</div>

<div class="auth-card">

  <div class="form-header">
    <h2>Admin Login.</h2>
    <p>Restricted access only</p>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
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

</body>
</html>