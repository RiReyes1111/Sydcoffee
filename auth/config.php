<?php
$host = getenv('DB_HOST') ?: 'mysql.railway.internal';
$port = (int)(getenv('DB_PORT') ?: 3306);
$dbname = getenv('DB_NAME') ?: 'railway';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'itNBYATxTbQnJsCqApUXObUVFdEulyZH';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

class FakeMysqli {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    public function query($sql) {
        $stmt = $this->pdo->query($sql);
        return new FakeResult($stmt);
    }
    public function prepare($sql) { return $this->pdo->prepare($sql); }
    public function real_escape_string($s) { return addslashes($s); }
    public $connect_error = null;
}

class FakeResult {
    private $stmt;
    public function __construct($stmt) { $this->stmt = $stmt; }
    public function fetch_assoc() { return $this->stmt->fetch(PDO::FETCH_ASSOC) ?: null; }
}

$conn = new FakeMysqli($pdo);
?>