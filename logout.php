<?php
session_start();

$role = $_SESSION['role'] ?? null;

$_SESSION = [];
session_unset();
session_destroy();

// Prevent back button from showing cached page
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if ($role === 'admin') {
    header("Location: /login/adminlogin.php");
    exit();
} else {
    header("Location: /login/login.php");
    exit();
}
?>