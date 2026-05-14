<?php

$dbUrl = getenv("DATABASE_URL");

$url = parse_url($dbUrl);

$host = $url["host"];
$user = $url["user"];
$pass = $url["pass"];
$db   = ltrim($url["path"], "/");
$port = $url["port"];

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>