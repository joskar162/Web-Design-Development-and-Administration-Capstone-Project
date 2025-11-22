# Quick Reference Guide

Common code snippets and patterns for building the DCMA application.

## Database Connection

```php
<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'dcma';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8');
?>
```

## Password Hashing

### Creating a Hash
```php
$password = 'password123';
$hashed = password_hash($password, PASSWORD_DEFAULT);
// Store $hashed in database
```

### Verifying a Password
```php
$input_password = $_POST['password'];
$stored_hash = $user['password']; // from database

if (password_verify($input_password, $stored_hash)) {
    // Password is correct
} else {
    // Password is incorrect
}
```

## Session Management

### Setting Session Variables
```php
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['role'] = $user['role'];
```

### Checking if Logged In
```php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
```

### Requiring Login
```php
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
```

### Destroying Session (Logout)
```php
session_destroy();
header('Location: login.php');
exit();
```

## SQL Queries

### Sanitizing Input
```php
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

$username = sanitize($_POST['username']);
```

### SELECT Query
```php
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    // Use $user['column_name']
}
```

### INSERT Query
```php
$query = "INSERT INTO classes (class_code, class_name, lecturer_id) 
          VALUES ('$code', '$name', $lecturer_id)";

if (mysqli_query($conn, $query)) {
    // Success
    $new_id = mysqli_insert_id($conn);
} else {
    // Error
    $error = mysqli_error($conn);
}
```

### UPDATE Query
```php
$query = "UPDATE enrollments SET grade = $grade WHERE id = $enrollment_id";

if (mysqli_query($conn, $query)) {
    // Success
} else {
    // Error
}
```

### DELETE Query
```php
$query = "DELETE FROM classes WHERE id = $class_id AND lecturer_id = $lecturer_id";

if (mysqli_query($conn, $query)) {
    // Success
} else {
    // Error
}
```

### JOIN Query
```php
$query = "SELECT c.*, u.full_name as lecturer_name, COUNT(e.id) as enrolled_count
          FROM classes c
          JOIN users u ON c.lecturer_id = u.id
          LEFT JOIN enrollments e ON c.id = e.class_id AND e.status = 'enrolled'
          WHERE c.lecturer_id = $lecturer_id
          GROUP BY c.id";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    // Process each row
}
```

## Form Handling

### POST Form
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field1 = sanitize($_POST['field1']);
    $field2 = sanitize($_POST['field2']);
    
    // Validate
    if (empty($field1)) {
        $error = "Field 1 is required";
    } else {
        // Process form
    }
}
```

### GET Parameters
```php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Use $id
}
```

## HTML Output

### Displaying Data Safely
```php
<h2><?php echo htmlspecialchars($class['class_name']); ?></h2>
```

### Conditional Display
```php
<?php if (isset($error)): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
```

### Looping Through Results
```php
<?php while ($class = mysqli_fetch_assoc($result)): ?>
    <div class="class-card">
        <h3><?php echo htmlspecialchars($class['class_code']); ?></h3>
        <p><?php echo htmlspecialchars($class['class_name']); ?></p>
    </div>
<?php endwhile; ?>
```

### Empty Check
```php
<?php if (mysqli_num_rows($result) > 0): ?>
    <!-- Display data -->
<?php else: ?>
    <p>No data found.</p>
<?php endif; ?>
```

## Redirects

### Simple Redirect
```php
header('Location: dashboard.php');
exit();
```

### Redirect with Query Parameter
```php
header('Location: class_details.php?id=' . $class_id);
exit();
```

## Date/Time Formatting

### Format Date
```php
$formatted = date('F j, Y', strtotime($date_string));
// Output: November 21, 2025
```

### Format Time
```php
$formatted = date('g:i A', strtotime($time_string));
// Output: 2:30 PM
```

## Common Patterns

### Check Ownership
```php
$query = "SELECT * FROM classes WHERE id = $class_id AND lecturer_id = $lecturer_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    // User doesn't own this resource
    header('Location: dashboard.php');
    exit();
}
```

### Count Records
```php
$query = "SELECT COUNT(*) as total FROM enrollments WHERE student_id = $student_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total = $row['total'];
```

### Check if Exists
```php
$query = "SELECT id FROM enrollments WHERE student_id = $student_id AND class_id = $class_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // Already exists
}
```

## JavaScript Snippets

### Toggle Element Visibility
```javascript
function toggleForm() {
    const form = document.getElementById('createClassForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
```

### Show Modal
```javascript
function showModal(id) {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('itemId').value = id;
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}
```

### Confirm Action
```html
<a href="delete.php?id=<?php echo $id; ?>" 
   onclick="return confirm('Are you sure?')">Delete</a>
```

## CSS Patterns

### Center Container
```css
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
```

### Grid Layout
```css
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}
```

### Card Hover Effect
```css
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
```

### Responsive Design
```css
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }
}
```

## Debugging Tips

### Display PHP Errors
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Debug Variable
```php
echo '<pre>';
print_r($variable);
echo '</pre>';
```

### Debug Query
```php
$query = "SELECT * FROM users";
echo $query; // See the actual query
$result = mysqli_query($conn, $query);
if (!$result) {
    echo mysqli_error($conn); // See the error
}
```

## Security Checklist

- [ ] Always use `sanitize()` or prepared statements for SQL
- [ ] Always use `htmlspecialchars()` for output
- [ ] Always use `password_hash()` for passwords
- [ ] Always check user authentication before showing data
- [ ] Always verify ownership before allowing actions
- [ ] Always validate input (check if empty, correct format, etc.)
- [ ] Always use `exit()` after `header()` redirects

## Common Errors & Solutions

### "Headers already sent"
**Cause:** Output before `header()` call
**Solution:** Ensure no echo/HTML before `header()`, check for spaces before `<?php`

### "Undefined index"
**Cause:** Accessing array key that doesn't exist
**Solution:** Use `isset()` to check first
```php
$value = isset($_POST['field']) ? $_POST['field'] : '';
```

### "Call to undefined function"
**Cause:** Function not defined or typo
**Solution:** Check function name spelling, ensure function is defined before use

### "Access denied for user"
**Cause:** Wrong database credentials
**Solution:** Check username/password in config.php

### SQL Syntax Error
**Cause:** Invalid SQL query
**Solution:** Echo the query to see it, test in phpMyAdmin

---

**Pro Tip:** Keep this reference open while coding. Copy and adapt these patterns to your needs!
