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
    private $pdo;
    private $params = [];
    private $cachedRows = [];
    public $insert_id = 0;
    public $num_rows = 0;

    public function __construct($stmt, $pdo) {
        $this->stmt = $stmt;
        $this->pdo  = $pdo;
    }

    public function bind_param($types, ...$params) {
        $this->params = $params;
    }

    public function execute() {
        try {
            $this->stmt->execute($this->params);
            $this->insert_id = (int) $this->pdo->lastInsertId();
            // Cache rows immediately so num_rows and get_result both work
            try {
                $this->cachedRows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $this->cachedRows = [];
            }
            $this->num_rows = count($this->cachedRows);
            return true;
        } catch (PDOException $e) {
            error_log("FakeStatement execute error: " . $e->getMessage());
            return false;
        }
    }

    public function get_result() {
        return new FakeResult($this->cachedRows);
    }

    public function store_result() {
        // num_rows already set in execute(), nothing to do
        return true;
    }
}

class FakeResult {
    private $rows  = [];
    private $index = 0;
    public $num_rows = 0;

    public function __construct($rows) {
        $this->rows     = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_assoc() {
        return $this->rows[$this->index++] ?? null;
    }
}

class FakeMysqli {
    private $pdo;
    public $connect_error = null;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function query($sql) {
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return new FakeResult($rows);
    }

    public function prepare($sql) {
        $stmt = $this->pdo->prepare($sql);
        return new FakeStatement($stmt, $this->pdo);
    }

    public function real_escape_string($s) {
        return addslashes($s);
    }
}

$conn = new FakeMysqli($pdo);