<?php
// TODO: Include config and require lecturer role
require_once(__DIR__ . '/../config.php');
requireLogin();
requireRole('lecturer');


// TODO: Handle class creation form submission
// 1. Get form data (class_code, class_name, description, schedule, etc.)
// 2. Validate input
// 3. Insert into database
// 4. Show success/error message

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_class'])) {
    // Get form data
    $class_code = $_POST['class_code'];
    $class_name = $_POST['class_name'];
    $description = $_POST['description'];
    $max_students = $_POST['max_students'];
    $schedule_day = $_POST['schedule_day'];
    $schedule_time = $_POST['schedule_time'];
    $room = $_POST['room'];

    // Validate input
    if (empty($class_code) || empty($class_name) || empty($max_students) || empty($schedule_day) || empty($schedule_time) || empty($room)) {
        $error = "All fields are required.";
    } else {
        // Insert into database
            $insert_sql = "INSERT INTO classes (class_code, name, description, lecturer_id, max_students, schedule_day, schedule_time, room) 
                           VALUES ('$class_code', '$class_name', '$description', {$_SESSION['user_id']}, $max_students, '$schedule_day', '$schedule_time', '$room')";
        if (mysqli_query($conn, $insert_sql)) {
            $success = "Class created successfully!";
        } else {
            $error = "Error creating class: " . mysqli_error($conn);
        }
    }
}
     // Show success/error message
     
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_class'])) {
    $class_id = $_POST['class_id'];

    // Delete the class from the database
    $delete_sql = "DELETE FROM classes WHERE id = $class_id AND lecturer_id = {$_SESSION['user_id']}";

    if (mysqli_query($conn, $delete_sql)) {
        $success = "Class deleted successfully!";
    } else {
        $error = "Error deleting class: " . mysqli_error($conn);
    }
}
        

// TODO: Handle class deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_class'])) {
    $class_id = $_POST['class_id'];

    // Delete the class from the database
    $delete_sql = "DELETE FROM classes WHERE id = $class_id AND lecturer_id = {$_SESSION['user_id']}";

    if (mysqli_query($conn, $delete_sql)) {
        $success = "Class deleted successfully!";
    } else {
        $error = "Error deleting class: " . mysqli_error($conn);
    }
}


// TODO: Get lecturer's classes from database
$classes_sql = "
    SELECT c.*, 
        (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.id AND e.status = 'active') AS enrolled_count
    FROM classes c
    WHERE c.lecturer_id = {$_SESSION['user_id']}
";
$classes_result = mysqli_query($conn, $classes_sql);




include 'header.php';
?>

<div class="dashboard">
    <h2>My Classes</h2>
    
    <!-- TODO: Display success/error messages -->
    
    <!-- TODO: Add "Create New Class" button -->
    
    <!-- TODO: Create class creation form (hidden by default) -->
    <!-- Form fields: class_code, class_name, description, max_students, 
         schedule_day, schedule_time, room -->
    
    <!-- TODO: Display lecturer's classes in a grid -->
    <!-- Each class card should show:
         - Class code and name
         - Description
         - Schedule and room
         - Enrolled count / max students
         - View Details and Delete buttons -->
    
</div>

<!-- TODO: Add JavaScript for toggling create class form -->

</body>
</html>
