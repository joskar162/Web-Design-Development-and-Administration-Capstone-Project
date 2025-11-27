<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Development debug mode; set to false on production
if (!defined('DEBUG')) {
    define('DEBUG', true);
}


$sqlFilePath = __DIR__ . '/../database/dcma_template.sql';

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'dcma';

// TODO: Create database connection

// Connect to MySQL server without selecting a database first.
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Try to select the database; if it does not exist, attempt to create it
if (!$conn->select_db($db)) {
    // Attempt to create the database
    $createDbSql = "CREATE DATABASE IF NOT EXISTS `" . $conn->real_escape_string($db) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!$conn->query($createDbSql)) {
        die("Failed to create database '{$db}': " . $conn->error . ". Please create the database manually or give the MySQL user sufficient privileges.");
    }

    // Select the database
    if (!$conn->select_db($db)) {
        die("Unable to select database '{$db}' after creation: " . $conn->error);
    }

    // If an SQL template file exists, try to import it to create tables and sample data
    if (file_exists($sqlFilePath) && is_readable($sqlFilePath)) {
        $sqlContents = file_get_contents($sqlFilePath);
        if ($sqlContents !== false) {
            // Use multi_query to execute multiple statements
            if (!$conn->multi_query($sqlContents)) {
                // If the import fails, log a helpful error and continue: the DB exists but may be empty
                error_log('Failed to import SQL template: ' . $conn->error);
            } else {
                // Flush all result sets produced by multi_query
                do {
                    if ($res = $conn->store_result()) {
                        $res->free();
                    }
                } while ($conn->more_results() && $conn->next_result());
            }
        }
    }
}

// After attempting to import schema, check if 'users' table exists; if not, create minimal structure and demo accounts
$usersCheck = $conn->query("SHOW TABLES LIKE 'users'");
if (!$usersCheck || $usersCheck->num_rows === 0) {
    // Create a minimal users table to allow login and seed demo users
    $createUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        role ENUM('student','lecturer') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    if (!$conn->query($createUsers)) {
        error_log('Failed to create users table: ' . $conn->error);
    } else {
        // Seed demo credentials if not present
        $conn->query("INSERT IGNORE INTO users (username, password, full_name, email, role) VALUES
            ('student1', 'studentpass', 'Demo Student', 'student1@example.com', 'student'),
            ('lecturer1', 'lecturerpass', 'Demo Lecturer', 'lecturer1@example.com', 'lecturer')");
    }
}

// TODO: Check connection and handle errors



// Set charset to UTF-8
$conn->set_charset('utf8mb4');



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
