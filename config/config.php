<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'dcma';

// TODO: Create database connection

// Create mysqli connection (object-style)
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// TODO: Check connection and handle errors



// TODO: Set charset to utf8
mysqli_set_charset($conn, "utf8");



// Helper functions
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string($data);
}

// - isLoggedIn(): Check if user is logged in

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
// - hasRole($role): Check if user has specific role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}
// - requireLogin(): Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}
// - requireRole($role): Redirect if user doesn't have required role
function requireRole($role) {
    if (!hasRole($role)) {
        header("Location: unauthorized.php");
        exit();
    }
}
// - sanitize($data): Sanitize user input to prevent SQL injection
 
// (Sanitize already defined earlier)

?>
