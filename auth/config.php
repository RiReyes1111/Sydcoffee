<?php
$host = getenv('DB_HOST') ?: 'mysql.railway.internal';
$port = (int)(getenv('DB_PORT') ?: 3306);
$dbname = getenv('DB_NAME') ?: 'railway';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'itNBYATxTbQnJsCqApUXObUVFdEulyZH';

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>