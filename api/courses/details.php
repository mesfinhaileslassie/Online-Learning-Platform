<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

$course_id = $_GET['id'] ?? 0;

if (!$course_id) {
    echo json_encode(['success' => false, 'message' => 'Course ID required']);
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT c.*, u.full_name as instructor_name, u.bio as instructor_bio,
                                cat.name as category_name
                                FROM courses c
                                JOIN users u ON c.instructor_id = u.id
                                JOIN categories cat ON c.category_id = cat.id
                                WHERE c.id = ? AND c.status = 'published'");
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$course = mysqli_fetch_assoc($result);

if (!$course) {
    echo json_encode(['success' => false, 'message' => 'Course not found']);
    exit();
}

$course['price_formatted'] = $course['price'] == 0 ? 'Free' : number_format($course['price'], 2) . ' ETB';

echo json_encode(['success' => true, 'course' => $course]);
?>