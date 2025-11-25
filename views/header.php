<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - DCMA' : 'DCMA'; ?></title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <h1>Dynamic Class Management</h1>
            <nav>
                <!-- TODO: Display logged in user's name and role -->
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</span>
                <!-- TODO: Add logout button -->
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </nav>
        </div>
    </header>
    <div class="container">
