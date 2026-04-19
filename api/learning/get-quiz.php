<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

$lesson_id = $_GET['lesson_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit();
}

// Check enrollment (through lesson -> module -> course)
$check = mysqli_query($conn, "
    SELECT e.id FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    JOIN modules m ON m.course_id = c.id
    JOIN lessons l ON l.module_id = m.id
    WHERE l.id = $lesson_id AND e.user_id = $user_id AND e.payment_status = 'completed'
");
if (!mysqli_num_rows($check)) {
    echo json_encode(['success' => false, 'message' => 'You are not enrolled in this course']);
    exit();
}

// Get quiz for this lesson
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM quizzes WHERE lesson_id = $lesson_id"));
if (!$quiz) {
    echo json_encode(['success' => false, 'message' => 'No quiz found for this lesson']);
    exit();
}

// Get questions and answers
$questions = [];
$qResult = mysqli_query($conn, "SELECT * FROM quiz_questions WHERE quiz_id = {$quiz['id']} ORDER BY order_index");
while ($q = mysqli_fetch_assoc($qResult)) {
    $answers = [];
    $aResult = mysqli_query($conn, "SELECT * FROM quiz_answers WHERE question_id = {$q['id']} ORDER BY order_index");
    while ($a = mysqli_fetch_assoc($aResult)) {
        $answers[] = ['id' => $a['id'], 'text' => $a['answer_text'], 'is_correct' => (bool)$a['is_correct']];
    }
    $q['answers'] = $answers;
    $questions[] = $q;
}
$quiz['questions'] = $questions;

echo json_encode(['success' => true, 'quiz' => $quiz]);
?>