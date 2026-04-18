<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($full_name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit();
}

$checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($checkStmt, "s", $email);
mysqli_stmt_execute($checkStmt);
if (mysqli_stmt_get_result($checkStmt)->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$insertStmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'student')");
mysqli_stmt_bind_param($insertStmt, "ssss", $full_name, $email, $phone, $password_hash);

if (mysqli_stmt_execute($insertStmt)) {
    echo json_encode(['success' => true, 'message' => 'Registration successful! Please login.', 'redirect' => '/ethioskillfactoryV2/public/login.html']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}
?>