<?php
// Include config file
require_once(__DIR__ . '/config/config.php');

// TODO: If already logged in, redirect to dashboard
if (isLoggedIn()) {
    $role = $_SESSION['role'];
    if ($role === 'student') {
        header("Location: views/dashboard_student.php");
exit();

        exit();
    } elseif ($role === 'lecturer') {
      header("Location: views/dashboard_lecturer.php");
exit();
    }
}


// TODO: Handle login form submission
// 1. Get username and password from POST
// 2. Query database for user
// 3. Verify password (use password_verify if using password_hash)
// 4. Set session variables
// 5. Redirect to appropriate dashboard

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Get username and password from POST
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 2. Query database for user
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 3. Verify password
        // Supports both plain-text and hashed passwords
        if ($password === $user['password'] || password_verify($password, $user['password'])) {
            
            // 4. Set session variables
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];

            // 5. Redirect to appropriate dashboard
            if ($user['role'] === 'student') {
                header("Location: student_dashboard.php");
                exit;
            } elseif ($user['role'] === 'lecturer') {
                header("Location: lecturer_dashboard.php");
                exit;
            } else {
                header("Location: index.php"); // fallback
                exit;
            }

        } else {
            $error = "Invalid username or password.";
        }

    } else {
        $error = "Invalid username or password.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DCMA</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Dynamic Class Management</h1><br>
            
            <h2>Login</h2>
            
            <!-- TODO: Display error messages if login fails -->
             <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <!-- TODO: Create login form with username and password fields -->         

            <form method="POST" action="">
                <!-- Add form fields here -->
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>
                <button type="submit" name="login">Login</button>
            </form>
            <br>
            
            <!-- TODO: Add demo credentials display -->
            <div class="demo-credentials">
                <h3>Demo Credentials</h3><br>
                <p><strong>Student:</strong><br>
             <i><b>username:</i></b> student1  <i><b> <br>
             password:</i></b> @student1</p><br>
                <p><strong>Lecturer:</strong>
                <br> <i><b>username:</i></b> lecturer1 
                <br>
                <i><b> password:</i></b> @lecturer1</p>
        </div>
    </div>
</body>
</html>
