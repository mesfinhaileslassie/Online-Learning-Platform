<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$quiz_id = $input['quiz_id'] ?? 0;
$lesson_id = $input['lesson_id'] ?? 0;
$user_answers = $input['answers'] ?? [];
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id || !$quiz_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Get quiz passing score
$quizRes = mysqli_query($conn, "SELECT passing_score FROM quizzes WHERE id = $quiz_id");
$quiz = mysqli_fetch_assoc($quizRes);
if (!$quiz) {
    echo json_encode(['success' => false, 'message' => 'Quiz not found']);
    exit();
}

$passing_score = $quiz['passing_score'];

// Get all questions for this quiz with points and correct answers
$questions = [];
$qRes = mysqli_query($conn, "SELECT id, points FROM quiz_questions WHERE quiz_id = $quiz_id");
while ($q = mysqli_fetch_assoc($qRes)) {
    $correct = [];
    $aRes = mysqli_query($conn, "SELECT id FROM quiz_answers WHERE question_id = {$q['id']} AND is_correct = 1");
    while ($a = mysqli_fetch_assoc($aRes)) {
        $correct[] = $a['id'];
    }
    $questions[$q['id']] = ['points' => $q['points'], 'correct' => $correct];
}

// Calculate score
$total_points = 0;
$earned_points = 0;
foreach ($user_answers as $ans) {
    $qid = $ans['question_id'];
    $selected = $ans['answer_ids'];
    if (!isset($questions[$qid])) continue;
    $total_points += $questions[$qid]['points'];
    sort($selected);
    sort($questions[$qid]['correct']);
    if ($selected == $questions[$qid]['correct']) {
        $earned_points += $questions[$qid]['points'];
    }
}

$score_percent = ($total_points > 0) ? round(($earned_points / $total_points) * 100) : 0;
$passed = $score_percent >= $passing_score;

// Get enrollment ID
$courseRes = mysqli_query($conn, "SELECT course_id FROM lessons WHERE id = $lesson_id");
$course = mysqli_fetch_assoc($courseRes);
$course_id = $course['course_id'];
$enrollRes = mysqli_query($conn, "SELECT id FROM enrollments WHERE user_id = $user_id AND course_id = $course_id AND payment_status = 'completed' LIMIT 1");
$enrollment = mysqli_fetch_assoc($enrollRes);
$enrollment_id = $enrollment ? $enrollment['id'] : null;

if (!$enrollment_id) {
    echo json_encode(['success' => false, 'message' => 'Enrollment not found']);
    exit();
}

// Get attempt number
$attemptCountRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM quiz_attempts WHERE user_id = $user_id AND quiz_id = $quiz_id");
$attemptCount = mysqli_fetch_assoc($attemptCountRes)['cnt'];
$attempt_number = $attemptCount + 1;

// Insert attempt
$stmt = mysqli_prepare($conn, "INSERT INTO quiz_attempts (user_id, quiz_id, enrollment_id, score, is_passed, started_at, completed_at, attempt_number) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)");
mysqli_stmt_bind_param($stmt, "iiiiii", $user_id, $quiz_id, $enrollment_id, $score_percent, $passed, $attempt_number);
mysqli_stmt_execute($stmt);

echo json_encode([
    'success' => true,
    'score' => $score_percent,
    'passed' => $passed,
    'message' => $passed ? 'Congratulations! You passed.' : 'You did not pass. Try again.'
]);
?>