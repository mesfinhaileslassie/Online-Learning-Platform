<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT c.id, c.title, c.description, c.thumbnail_url, e.enrolled_at, e.progress_percent
          FROM enrollments e
          JOIN courses c ON e.course_id = c.id
          WHERE e.user_id = ? AND e.payment_status = 'completed'
          ORDER BY e.enrolled_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row;
}

echo json_encode(['success' => true, 'courses' => $courses]);
?>