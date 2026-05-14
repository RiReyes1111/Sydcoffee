<?php
session_start();

$role = $_SESSION['role'] ?? null;

$_SESSION = [];
session_unset();
session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if ($role === 'admin') {
    header("Location: admin/adminlogin.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>