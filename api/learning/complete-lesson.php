<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$lesson_id = $input['lesson_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id || !$lesson_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Check enrollment
$check = mysqli_query($conn, "
    SELECT e.id FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    JOIN modules m ON m.course_id = c.id
    JOIN lessons l ON l.module_id = m.id
    WHERE l.id = $lesson_id AND e.user_id = $user_id AND e.payment_status = 'completed'
    LIMIT 1
");
$enrollment = mysqli_fetch_assoc($check);
if (!$enrollment) {
    echo json_encode(['success' => false, 'message' => 'Not enrolled']);
    exit();
}
$enrollment_id = $enrollment['id'];

// Insert or update progress
$upsert = mysqli_query($conn, "
    INSERT INTO lesson_progress (enrollment_id, lesson_id, is_completed, completed_at, updated_at)
    VALUES ($enrollment_id, $lesson_id, 1, NOW(), NOW())
    ON DUPLICATE KEY UPDATE is_completed = 1, completed_at = NOW(), updated_at = NOW()
");
if ($upsert) {
    // Update overall enrollment progress percent (optional)
    $totalLessons = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM lessons l JOIN modules m ON l.module_id = m.id WHERE m.course_id = (SELECT course_id FROM modules WHERE id = (SELECT module_id FROM lessons WHERE id = $lesson_id))"))['cnt'];
    $completedLessons = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM lesson_progress WHERE enrollment_id = $enrollment_id AND is_completed = 1"))['cnt'];
    $percent = ($totalLessons > 0) ? round(($completedLessons / $totalLessons) * 100) : 0;
    mysqli_query($conn, "UPDATE enrollments SET progress_percent = $percent WHERE id = $enrollment_id");
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>