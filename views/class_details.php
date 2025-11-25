<?php
// TODO: Include config and require lecturer role

require_once(__DIR__ . '/../config.php');
requireLogin();
requireRole('lecturer');
// TODO: Get class_id from URL parameter
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

// TODO: Verify lecturer owns this class
$class = getClassById($class_id);
if (!$class || $class['lecturer_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit();
}

// TODO: Handle grade update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grade'])) {
    $enrollment_id = intval($_POST['enrollment_id']);
    $new_grade = sanitize($_POST['grade']);
    
    $update_sql = "UPDATE enrollments SET grade = '$new_grade' WHERE id = $enrollment_id AND class_id = $class_id";
    mysqli_query($conn, $update_sql);
}
// TODO: Handle attendance marking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $student_id = intval($_POST['student_id']);
    $date = date('Y-m-d');
    
    // Check if attendance already marked
    $check_sql = "SELECT id FROM attendance WHERE student_id = $student_id AND class_id = $class_id AND date = '$date'";
    $result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($result) == 0) {
        $insert_sql = "INSERT INTO attendance (student_id, class_id, date) VALUES ($student_id, $class_id, '$date')";
        mysqli_query($conn, $insert_sql);
    }
}
// TODO: Get enrolled students with attendance stats
$students_sql = "
    SELECT e.id AS enrollment_id, s.id AS student_id, s.name, s.email,
        (SELECT COUNT(*) FROM attendance a WHERE a.student_id = s.id AND a.class_id = $class_id) AS attended_classes,
        (SELECT COUNT(*) FROM classes c JOIN enrollments e2 ON c.id = e2  .class_id WHERE e2.student_id = s.id) AS total_classes, e.grade
    FROM enrollments e
    JOIN users s ON e.student_id = s.id
    WHERE e.class_id = $class_id AND e.status = 'active'
";
$students_result = mysqli_query($conn, $students_sql);


include 'header.php';
?>

<div class="dashboard">
    <!-- TODO: Add breadcrumb navigation -->
    <h2>Class Details</h2>

    <!-- TODO: Display class information header -->
     
    <div class="class-info">
        <h3><?php echo htmlspecialchars($class['name']); ?></h3>
        <p><?php echo htmlspecialchars($class['description']); ?></p>
        <p>Schedule: <?php echo htmlspecialchars($class['schedule']); ?></p>
        <p>Room: <?php echo htmlspecialchars($class['room']); ?></p>
        <p>Capacity: <?php echo $class['capacity']; ?> students</p>
    </div>    
    <!-- TODO: Display success/error messages -->
    
    <h3>Enrolled Students</h3>
    
    <!-- Create table showing:
         - Student name and email
         - Attendance rate
         - Grade with update form
         - Mark Attendance button -->

    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Attendance Rate</th>
                <th>Grade</th>
                <th>Actions</th>
            </tr>
        </thead>
       <tbody>
            <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td>
                        <?php
                            $attendance_rate = $student['total_classes'] > 0 ?
                                round(($student['attended_classes'] / $student['total_classes']) * 100) : 0;
                            echo $attendance_rate . '%';
                        ?>
                    </td>
                    <td>
                        <form method="post" action="update_grade.php">
                            <input type="hidden" name="enrollment_id" value="<?php echo $student['enrollment_id']; ?>">
                            <input type="text" name="grade" value="<?php echo htmlspecialchars($student['grade'] ?? ''); ?>" size="3">
                            <button type="submit">Update</button>
                        </form>
                    </td>
                    <td>
                        <form method="post" action="mark_attendance.php">
                            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                            <button type="button" class="mark-attendance-btn">Mark Attendance</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
               
 </div>
<!-- TODO: Create modal for marking attendance -->
<div id="attendance-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Mark Attendance</h3>
        <p>Are you sure you want to mark attendance for this student?</p>
        <form method="post" action="class_details.php?class_id=<?php echo $class_id; ?>">
            <input type="hidden" name="student_id" id="modal-student-id" value="">
            <button type="submit" name="mark_attendance">Yes, Mark Attendance</button>
            <button type="button" class="close-btn">Cancel</button>
        </form>
    </div>
<!-- TODO: Add JavaScript for modal functionality -->
<script>
    // Get modal element
    var modal = document.getElementById("attendance-modal");
    // Get close elements
    var closeElements = document.getElementsByClassName("close");
    var closeBtns = document.getElementsByClassName("close-btn");
    // Get mark attendance buttons
    var markAttendanceBtns = document.getElementsByClassName("mark-attendance-btn");
    // When the user clicks a mark attendance button
    for (var i = 0; i < markAttendanceBtns.length; i++) {
        markAttendanceBtns[i].onclick = function() {
            var studentId = this.parentElement.querySelector('input[name="student_id"]').value;
            document.getElementById("modal-student-id").value = studentId;
            modal.style.display = "block";
        }
    }
    // When the user clicks on close elements
    for (var i = 0; i < closeElements.length; i++) {
        closeElements[i].onclick = function() {
            modal.style.display = "none";
        }
    }
    for (var i = 0; i < closeBtns.length; i++) {
        closeBtns[i].onclick = function() {
            modal.style.display = "none";
        }
    }
    // When the user clicks anywhere outside of the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</div>
    
</body>
</html>
