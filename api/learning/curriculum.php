<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

$course_id = $_GET['course_id'] ?? 0;

if (!$course_id) {
    echo json_encode(['success' => false, 'message' => 'Course ID required']);
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Check enrollment
$check = mysqli_query($conn, "SELECT id FROM enrollments WHERE user_id = $user_id AND course_id = $course_id AND payment_status = 'completed'");
if (!mysqli_num_rows($check)) {
    echo json_encode(['success' => false, 'message' => 'You are not enrolled in this course']);
    exit();
}

$modules = [];
$modResult = mysqli_query($conn, "SELECT * FROM modules WHERE course_id = $course_id ORDER BY order_index");
while ($module = mysqli_fetch_assoc($modResult)) {
    $lessons = [];
    $lessResult = mysqli_query($conn, "SELECT * FROM lessons WHERE module_id = {$module['id']} ORDER BY order_index");
    while ($lesson = mysqli_fetch_assoc($lessResult)) {
        $lessons[] = $lesson;
    }
    $module['lessons'] = $lessons;
    $modules[] = $module;
}

echo json_encode(['success' => true, 'modules' => $modules]);
?>