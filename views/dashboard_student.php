<?php
// TODO: Include config and require student role
require_once(__DIR__ . '/../config.php');
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


// TODO: Get available classes (not enrolled, not full)


include 'header.php';
?>

<div class="dashboard">
    <h2>My Enrolled Classes</h2>
    
    <!-- TODO: Display success/error messages -->
    
    <!-- TODO: Display enrolled classes in a grid -->
    <!-- Each class card should show:
         - Class code and name
         - Description
         - Lecturer name
         - Schedule and room
         - Grade (if assigned)
         - View Details and Drop Class buttons -->
    
    <h2>Available Classes</h2>
    
    <!-- TODO: Display available classes in a grid -->
    <!-- Each class card should show:
         - Class code and name
         - Description
         - Lecturer name
         - Schedule and room
         - Enrolled count / max students
         - Enroll button -->
    
</div>

</body>
</html>
