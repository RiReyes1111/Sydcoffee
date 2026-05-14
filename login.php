<?php
$host = getenv('DB_HOST') ?: 'mysql.railway.internal';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'railway';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'itNBYATxTbQnJsCqApUXObUVFdEulyZH';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

class FakeStatement {
    private $stmt;
    private $params = [];
    public $insert_id = 0;
    public function __construct($stmt, $pdo) { 
        $this->stmt = $stmt; 
        $this->pdo = $pdo;
    }
    public function bind_param($types, &...$params) {
        $this->params = $params;
    }
    public function execute() {
        try {
            $this->stmt->execute($this->params);
            $this->insert_id = $this->pdo->lastInsertId();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    public function get_result() { return new FakeResult($this->stmt); }
    public function store_result() { return true; }
    public function num_rows() { return $this->stmt->rowCount(); }
    public $num_rows = 0;
}

class FakeResult {
    private $rows = [];
    private $index = 0;
    public $num_rows = 0;
    public function __construct($stmt) {
        $this->rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->num_rows = count($this->rows);
    }
    public function fetch_assoc() {
        return $this->rows[$this->index++] ?? null;
    }
}

class FakeMysqli {
    private $pdo;
    public $connect_error = null;
    public function __construct($pdo) { $this->pdo = $pdo; }
    public function query($sql) {
        $stmt = $this->pdo->query($sql);
        return new FakeResult($stmt);
    }
    public function prepare($sql) {
        $stmt = $this->pdo->prepare($sql);
        return new FakeStatement($stmt, $this->pdo);
    }
    public function real_escape_string($s) { return addslashes($s); }
}

$conn = new FakeMysqli($pdo);
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