<?php
// TODO: Include config and require student role
require_once(__DIR__ . '/../config.php');
requireLogin();
requireRole('student');

// TODO: Get enrollment_id from URL parameter
$enrollment_id = isset($_GET['enrollment_id']) ? intval($_GET['enrollment_id']) : 0;

// TODO: Verify student owns this enrollment
$enrollment = getEnrollmentById($enrollment_id);
if (!$enrollment || $enrollment['student_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit();
}


// TODO: Get class and enrollment details
$class_id = $enrollment['class_id'];
$class = getClassById($class_id);

// TODO: Get attendance records
$attendance_sql = "
    SELECT a.date, a.status, a.notes
    FROM attendance a
    WHERE a.student_id = {$_SESSION['user_id']} AND a.class_id = $class_id
    ORDER BY a.date DESC
";
$attendance_result = mysqli_query($conn, $attendance_sql);


// TODO: Calculate attendance statistics
$attendance_stats_sql = "
    SELECT COUNT(*) AS total_classes,
           SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS attended_classes
    FROM attendance a
    WHERE a.student_id = {$_SESSION['user_id']} AND a.class_id = $class_id
";
$attendance_stats_result = mysqli_query($conn, $attendance_stats_sql);
$attendance_stats = mysqli_fetch_assoc($attendance_stats_result);



include 'header.php';
?>

<div class="dashboard">
    <!-- TODO: Add breadcrumb navigation -->  
    <h2>Class Details</h2>
    
    <!-- TODO: Display class information header with grade -->
    <div class="class-info">
        <h3><?php echo htmlspecialchars($class['name']); ?></h3>
        <p><strong>Grade:</strong> <?php echo htmlspecialchars($enrollment['grade']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($enrollment['status']); ?></p>
    </div>
    
    <!-- TODO: Display class information and attendance summary in cards -->
    <div class="class-summary">
        <div class="card">
            <h4>Attendance Summary</h4>
            <p>Total Classes: <?php echo $attendance_stats['total_classes']; ?></p>
            <p>Classes Attended: <?php echo $attendance_stats['attended_classes']; ?></p>
        </div>
        <div class="card">
            <h4>Class Description</h4>
            <p><?php echo htmlspecialchars($class['description']); ?></p>
    
    <h3>Attendance History</h3>
    
    <!-- TODO: Create table showing attendance records:
         - Date
         - Status (present/absent/late)
         - Notes -->
         
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($attendance = mysqli_fetch_assoc($attendance_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($attendance['date']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['status']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['notes']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
</div>

</body>
</html>
