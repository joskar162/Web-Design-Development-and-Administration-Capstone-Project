<?php
// TODO: Include config file
require_once(__DIR__ . '/config.php');

// TODO: Check if user is logged in
// If logged in, redirect to appropriate dashboard based on role
// If not logged in, redirect to login page

if (isLoggedIn()) {
    $role = $_SESSION['role'];
    if ($role === 'student') {
        header("Location: dashboard_student.php");
        exit();
    } elseif ($role === 'lecturer') {
        header("Location: dashboard_lecturer.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

?>
