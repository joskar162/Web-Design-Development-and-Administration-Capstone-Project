<?php
// Include config and require student role
require_once(__DIR__ . '/../config/config.php');
requireLogin();
requireRole('student');

// TODO: Handle class enrollment
// 1. Check if student is already enrolled
// 2. Check if class is full
// 3. Insert enrollment record
// 4. Show success/error message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_class'])) {
    $class_id = intval($_POST['class_id']);
    $student_id = $_SESSION['user_id'];

    // Check if already enrolled
    $check_sql = "SELECT * FROM enrollments WHERE student_id = ? AND class_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $student_id, $class_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['message'] = "You are already enrolled in this class.";
        $_SESSION['message_type'] = "error";
    } else {
        // Check if class is full
        $class_sql = "SELECT capacity, (SELECT COUNT(*) FROM enrollments WHERE class_id = ?) as enrolled_count FROM classes WHERE id = ?";
        $class_stmt = $conn->prepare($class_sql);
        $class_stmt->bind_param("ii", $class_id, $class_id);
        $class_stmt->execute();
        $class_result = $class_stmt->get_result();
        $class = $class_result->fetch_assoc();

        if ($class['enrolled_count'] >= $class['capacity']) {
            $_SESSION['message'] = "This class is full.";
            $_SESSION['message_type'] = "error";
        } else {
            // Enroll student
            $enroll_sql = "INSERT INTO enrollments (student_id, class_id, enrollment_date) VALUES (?, ?, NOW())";
            $enroll_stmt = $conn->prepare($enroll_sql);
            $enroll_stmt->bind_param("ii", $student_id, $class_id);

            if ($enroll_stmt->execute()) {
                $_SESSION['message'] = "Successfully enrolled in the class!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error enrolling in class: " . $conn->error;
                $_SESSION['message_type'] = "error";
            }
        }
    }
    header("Location: dashboard_student.php");
    exit();
}

// TODO: Handle drop class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drop_class'])) {
    $class_id = intval($_POST['class_id']);
    $student_id = $_SESSION['user_id'];

    // Delete enrollment record
    $drop_sql = "DELETE FROM enrollments WHERE student_id = ? AND class_id = ?";
    $drop_stmt = $conn->prepare($drop_sql);
    $drop_stmt->bind_param("ii", $student_id, $class_id);

    if ($drop_stmt->execute()) {
        $_SESSION['message'] = "Successfully dropped the class!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error dropping class: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
    header("Location: dashboard_student.php");
    exit();
}


// TODO: Get student's enrolled classes
$enrolled_classes_sql = "
    SELECT c.*, l.name AS lecturer_name, e.grade
    FROM classes c
    JOIN enrollments e ON c.id = e.class_id
    JOIN users l ON c.lecturer_id = l.id
    WHERE e.student_id = {$_SESSION['user_id']} AND e.status = 'active'
";
$enrolled_classes_result = $conn->query($enrolled_classes_sql);


// TODO: Get available classes (not enrolled, not full)
$available_classes_sql = "
    SELECT c.*, l.name AS lecturer_name,
           (SELECT COUNT(*) FROM enrollments WHERE class_id = c.id AND status = 'active') AS enrolled_count
    FROM classes c
    JOIN users l ON c.lecturer_id = l.id
    WHERE c.id NOT IN (
        SELECT class_id FROM enrollments WHERE student_id = {$_SESSION['user_id']} AND status = 'active'
    )
    AND (SELECT COUNT(*) FROM enrollments WHERE class_id = c.id AND status = 'active') < c.capacity
";
$available_classes_result = $conn->query($available_classes_sql);


include 'header.php';
?>

<div class="dashboard">
    <h2>My Enrolled Classes</h2>
    
    <!-- TODO: Display success/error messages -->
     <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>">
              <?php 
                  echo htmlspecialchars($_SESSION['message']); 
                  unset($_SESSION['message']);
                  unset($_SESSION['message_type']);
              ?>
          </div>
     <?php endif; ?>
    
    <!-- TODO: Display enrolled classes in a grid -->
      <!-- Each class card should show:
         - Class code and name
         - Description
         - Lecturer name
         - Schedule and room
         - Grade (if assigned)
         - View Details and Drop Class buttons -->
     
     <?php while ($class = mysqli_fetch_assoc($enrolled_classes_result)): ?>
        <div class="class-card">
            <h3><?php echo htmlspecialchars($class['class_code']); ?> - <?php echo htmlspecialchars($class['name']); ?></h3>
            <p><?php echo htmlspecialchars($class['description']); ?></p>
            <p>Lecturer: <?php echo htmlspecialchars($class['lecturer_name']); ?></p>
            <p>Schedule: <?php echo htmlspecialchars($class['schedule']); ?></p>
            <p>Room: <?php echo htmlspecialchars($class['room']); ?></p>
            <?php if ($class['grade']): ?>
                <p>Grade: <?php echo htmlspecialchars($class['grade']); ?></p>
            <?php endif; ?>
            <a href="class_view.php?class_id=<?php echo $class['id']; ?>" class="btn">View Details</a>
            <a href="drop_class.php?class_id=<?php echo $class['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to drop this class?');">Drop Class</a>
        </div>
    <?php endwhile; ?>
   
    
    <h2>Available Classes</h2>
    
    <!-- TODO: Display available classes in a grid -->
    <!-- Each class card should show:
         - Class code and name
         - Description
         - Lecturer name
         - Schedule and room
         - Enrolled count / max students
         - Enroll button -->
     <?php while ($class = mysqli_fetch_assoc($available_classes_result)): ?>
        <div class="class-card">
            <h3><?php echo htmlspecialchars($class['class_code']); ?> - <?php echo htmlspecialchars($class['name']); ?></h3>
            <p><?php echo htmlspecialchars($class['description']); ?></p>
            <p>Lecturer: <?php echo htmlspecialchars($class['lecturer_name']); ?></p>
            <p>Schedule: <?php echo htmlspecialchars($class['schedule']); ?></p>
            <p>Room: <?php echo htmlspecialchars($class['room']); ?></p>
            <p>Enrolled: <?php echo $class['enrolled_count']; ?> / <?php echo $class['capacity']; ?> students</p>
            <a href="enroll.php?class_id=<?php echo $class['id']; ?>" class="btn">Enroll</a>
        </div>
    <?php endwhile; ?>
    
</div>

</body>
</html>
