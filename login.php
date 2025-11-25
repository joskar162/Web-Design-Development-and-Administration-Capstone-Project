<?php
// TODO: Include config file
require_once(__DIR__ . '/config.php');

// TODO: If already logged in, redirect to dashboard
if (isLoggedIn()) {
    $role = $_SESSION['role'];
    if ($role === 'student') {
        header("Location: dashboard_student.php");
        exit();
    } elseif ($role === 'lecturer') {
        header("Location: dashboard_lecturer.php");
        exit();
    }
}


// TODO: Handle login form submission
// 1. Get username and password from POST
// 2. Query database for user
// 3. Verify password (use password_verify if using password_hash)
// 4. Set session variables
// 5. Redirect to appropriate dashboard
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query database for user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect to appropriate dashboard
            if ($user['role'] === 'student') {
                header("Location: dashboard_student.php");
                exit();
            } elseif ($user['role'] === 'lecturer') {
                header("Location: dashboard_lecturer.php");
                exit();
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
            <h1>Dynamic Class Management</h1>
            <h2>Login</h2>
            
            <!-- TODO: Display error messages if login fails -->
             <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <!-- TODO: Create login form with username and password fields -->
             
            <form method="POST" action="">
                <!-- Add form fields here -->
            </form>
            
            <!-- TODO: Add demo credentials display -->
        </div>
    </div>
</body>
</html>
