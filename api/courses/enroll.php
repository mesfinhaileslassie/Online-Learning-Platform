<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first', 'redirect' => '/ethioskillfactoryV2/public/login.html']);
    exit();
}

$course_id = $_POST['course_id'] ?? 0;
$user_id = $_SESSION['user_id'];

if (!$course_id) {
    echo json_encode(['success' => false, 'message' => 'Course ID required']);
    exit();
}

// Check if already enrolled
$checkStmt = mysqli_prepare($conn, "SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
mysqli_stmt_bind_param($checkStmt, "ii", $user_id, $course_id);
mysqli_stmt_execute($checkStmt);
if (mysqli_stmt_get_result($checkStmt)->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You are already enrolled in this course']);
    exit();
}

// Get course price
$courseStmt = mysqli_prepare($conn, "SELECT price FROM courses WHERE id = ?");
mysqli_stmt_bind_param($courseStmt, "i", $course_id);
mysqli_stmt_execute($courseStmt);
$courseResult = mysqli_stmt_get_result($courseStmt);
$course = mysqli_fetch_assoc($courseResult);

if (!$course) {
    echo json_encode(['success' => false, 'message' => 'Course not found']);
    exit();
}

$price_paid = $course['price'];
$payment_method = 'demo'; // For now, using demo mode
$payment_status = 'completed';

// Create enrollment
$insertStmt = mysqli_prepare($conn, "INSERT INTO enrollments (user_id, course_id, price_paid, payment_method, payment_status) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($insertStmt, "iidss", $user_id, $course_id, $price_paid, $payment_method, $payment_status);

if (mysqli_stmt_execute($insertStmt)) {
    echo json_encode([
        'success' => true,
        'message' => 'Successfully enrolled!',
        'redirect' => '/ethioskillfactoryV2/public/dashboard/index.html'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Enrollment failed. Please try again.']);
}
?>