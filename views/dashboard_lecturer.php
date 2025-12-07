<?php
// Include config and require lecturer role
require_once(__DIR__ . '/../config/config.php');
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


// Get lecturer's classes from database (use safe integer and check query result)
$lecturer_id = intval($_SESSION['user_id'] ?? 0);
$classes_sql = "
    SELECT c.*, 
        (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.id AND e.status = 'active') AS enrolled_count
    FROM classes c
    WHERE c.lecturer_id = {$lecturer_id}
";
$classes_result = $conn->query($classes_sql);
if ($classes_result === false) {
    error_log('Lecturer classes query failed: ' . $conn->error);
    $classes_result = null;
}




include 'header.php';
?>

<div class="dashboard">
    <h2>My Classes</h2>
    
    <!-- TODO: Display success/error messages -->
     <?php if (isset($success)): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
     <?php endif; ?>
     <?php if (isset($error)): ?>
          <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
     <?php endif; ?>
    
    <!-- TODO: Add "Create New Class" button -->
     <button id="create-class-btn" class="btn">Create New Class</button>

    
    <!-- TODO: Create class creation form (hidden by default) -->
       <!-- Form fields: class_code, class_name, description, max_students, 
         schedule_day, schedule_time, room -->

     <div id="create-class-form" style="display: none;">
        <h3>Create New Class</h3>
        <form method="post" action="create_class.php">
            <input type="text" name="class_code" required placeholder="Class Code">
           <input type="text" name="class_name" required placeholder="Class Name">
           <textarea name="description" placeholder="Description"></textarea>
           <input type="number" name="max_students" required placeholder="Max Students">
           <select name="schedule_day" required>
               <option value="">Select Day</option>
               <option value="Monday">Monday</option>
               <option value="Tuesday">Tuesday</option>
               <option value="Wednesday">Wednesday</option>
               <option value="Thursday">Thursday</option>
               <option value="Friday">Friday</option>
           </select>
           <input type="time" name="schedule_time" required>
           <input type="text" name="room" required placeholder="Room">
           <button type="submit" class="btn">Create Class</button>
        </form>
    </div>  
         
    <!-- TODO: Display lecturer's classes in a grid -->
    <!-- Each class card should show:
         - Class code and name
         - Description
         - Schedule and room
         - Enrolled count / max students
         - View Details and Delete buttons -->
     <div class="class-grid">
        <?php if ($classes_result): ?>
            <?php while ($class = mysqli_fetch_assoc($classes_result)): ?>
            <div class="class-card">
                <h3><?php echo htmlspecialchars($class['class_code']); ?> - <?php echo htmlspecialchars($class['name']); ?></h3>
                <p><?php echo htmlspecialchars($class['description']); ?></p>
                <p>Schedule: <?php echo htmlspecialchars($class['schedule']); ?></p>
                <p>Room: <?php echo htmlspecialchars($class['room']); ?></p>
                <p>Enrolled: <?php echo $class['enrolled_count']; ?> / <?php echo $class['capacity']; ?> students</p>
                <a href="class_details.php?class_id=<?php echo $class['id']; ?>" class="btn">View Details</a>
                <a href="delete_class.php?class_id=<?php echo $class['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No classes found.</p>
        <?php endif; ?>
    </div>
    
</div>

<!-- TODO: Add JavaScript for toggling create class form -->
<script>
document.getElementById('create-class-btn').addEventListener('click', function() {
    document.getElementById('create-class-form').classList.toggle('hidden');
});
</script>

</body>
</html>
