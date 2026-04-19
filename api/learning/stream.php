<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../config/database.php';

$lesson_id = $_GET['lesson_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id || !$lesson_id) {
    http_response_code(403);
    die('Access denied');
}

// Get lesson info
$stmt = mysqli_prepare($conn, "SELECT l.content_url, l.module_id, m.course_id 
                                FROM lessons l 
                                JOIN modules m ON l.module_id = m.id 
                                WHERE l.id = ?");
mysqli_stmt_bind_param($stmt, "i", $lesson_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lesson = mysqli_fetch_assoc($result);

if (!$lesson) {
    http_response_code(404);
    die('Video not found');
}

// Check enrollment
$checkStmt = mysqli_prepare($conn, "SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? AND payment_status = 'completed'");
mysqli_stmt_bind_param($checkStmt, "ii", $user_id, $lesson['course_id']);
mysqli_stmt_execute($checkStmt);
if (!mysqli_stmt_get_result($checkStmt)->num_rows) {
    http_response_code(403);
    die('You are not enrolled in this course');
}

// Get video file path
$video_path = $_SERVER['DOCUMENT_ROOT'] . '/ethioskillfactoryV2/uploads/videos/' . basename($lesson['content_url']);

if (!file_exists($video_path)) {
    http_response_code(404);
    die('Video file not found');
}

// Stream video
$mime = mime_content_type($video_path);
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($video_path));
header('Content-Disposition: inline; filename="' . basename($video_path) . '"');
header('Cache-Control: no-cache');

readfile($video_path);
exit;
?>