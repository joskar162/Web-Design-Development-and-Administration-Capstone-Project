<?php
// Include config file
require_once(__DIR__ . '/config/config.php');

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
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        // Prepared statement to get user by username
        $sql = "SELECT id, username, password, full_name, email, role FROM users WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            // Log the error for the developer
            $dberr = $conn->error ?? 'unknown error';
            error_log('DB prepare failed: ' . $dberr);
            // If in DEBUG mode provide more info on-screen; otherwise show a generic message
            $error = defined('DEBUG') && DEBUG ? 'DB error: ' . htmlspecialchars($dberr) : 'Internal server error. Please try again later.';
            // Fallback: Try a safe non-prepared query using real_escape_string
            $usernameEsc = $conn->real_escape_string($username);
            $fallbackSql = "SELECT id, username, password, full_name, email, role FROM users WHERE username = '" . $usernameEsc . "' LIMIT 1";
            $fallbackResult = $conn->query($fallbackSql);
            if ($fallbackResult && $fallbackResult->num_rows === 1) {
                $row = $fallbackResult->fetch_assoc();
                $db_id = $row['id'];
                $db_username = $row['username'];
                $db_password_hash = $row['password'];
                $db_full_name = $row['full_name'];
                $db_email = $row['email'];
                $db_role = $row['role'];
                $password_ok = !empty($db_password_hash) && (password_verify($password, $db_password_hash) || $password === $db_password_hash);
                if ($password_ok) {
                    // If the stored password matched by direct equality (legacy plain-text), re-hash and update DB
                    if ($password === $db_password_hash) {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $safeHash = $conn->real_escape_string($newHash);
                        $safeId = intval($db_id);
                        if (!$conn->query("UPDATE users SET password = '{$safeHash}' WHERE id = {$safeId}")) {
                            error_log('Failed to update password hash for user id ' . $safeId . ': ' . $conn->error);
                        } else {
                            error_log('Re-hashed password for user id ' . $safeId . ' after legacy login (fallback).');
                        }
                    }
                    // Set session variables
                    $_SESSION['user_id'] = $db_id;
                    $_SESSION['user_name'] = $db_full_name;
                    $_SESSION['role'] = $db_role;
                    if ($db_role === 'student') {
                        header('Location: dashboard_student.php');
                        exit();
                    } elseif ($db_role === 'lecturer') {
                        header('Location: dashboard_lecturer.php');
                        exit();
                    }
                } else {
                    error_log(sprintf('Login failed for username "%s": password mismatch (fallback)', $username));
                    $error = 'Invalid username or password.';
                }
            } else {
                error_log(sprintf('Login failed for username "%s": user not found (fallback)', $username));
            }
        } else {
            $stmt->bind_param('s', $username);
            if (!$stmt->execute()) {
                error_log('DB execute failed: ' . $stmt->error);
                $error = 'Internal server error. Please try again later.';
            } else {
                $stmt->store_result();
                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($db_id, $db_username, $db_password_hash, $db_full_name, $db_email, $db_role);
                    $stmt->fetch();
                    // Verify password; support both password_hash and plain-text for legacy seed data
                    $password_ok = false;
                    if (!empty($db_password_hash) && (password_verify($password, $db_password_hash) || $password === $db_password_hash)) {
                        $password_ok = true;
                    }
                    if ($password_ok) {
                        // Re-hash legacy plain-text passwords if matched directly
                        if ($password === $db_password_hash) {
                            $newHash = password_hash($password, PASSWORD_DEFAULT);
                            $safeHash = $conn->real_escape_string($newHash);
                            $safeId = intval($db_id);
                            if (!$conn->query("UPDATE users SET password = '{$safeHash}' WHERE id = {$safeId}")) {
                                error_log('Failed to update password hash for user id ' . $safeId . ': ' . $conn->error);
                            } else {
                                error_log('Re-hashed password for user id ' . $safeId . ' after legacy login (prepared).');
                            }
                        }
                        // Set session variables
                        $_SESSION['user_id'] = $db_id;
                        $_SESSION['user_name'] = $db_full_name;
                        $_SESSION['role'] = $db_role;

                        // Redirect to appropriate dashboard
                        if ($db_role === 'student') {
                            header('Location: dashboard_student.php');
                            exit();
                        } elseif ($db_role === 'lecturer') {
                            header('Location: dashboard_lecturer.php');
                            exit();
                        }
                    } else {
                        error_log(sprintf('Login failed for username "%s": password mismatch (prepared)', $username));
                        $error = 'Invalid username or password.';
                    }
                } else {
                    $error = 'Invalid username or password.';
                }
            }
            $stmt->close();
        }
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
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="login">Login</button>
            </form>
            
            <!-- TODO: Add demo credentials display -->
            <div class="demo-credentials">
                <h3>Demo Credentials</h3>
                <p><strong>Student:</strong> username: student1 | password: studentpass</p>
                <p><strong>Lecturer:</strong> username: lecturer1 | password: lecturerpass</p>
        </div>
    </div>
</body>
</html>
