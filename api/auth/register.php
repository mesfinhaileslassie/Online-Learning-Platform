<?php
require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Invalid request method');
}

$input = json_decode(file_get_contents('php://input'), true);
$full_name = trim($input['full_name'] ?? $_POST['full_name'] ?? '');
$email = trim($input['email'] ?? $_POST['email'] ?? '');
$phone = trim($input['phone'] ?? $_POST['phone'] ?? '');
$password = $input['password'] ?? $_POST['password'] ?? '';
$confirm_password = $input['confirm_password'] ?? $_POST['confirm_password'] ?? '';

// Validation
if (empty($full_name) || empty($email) || empty($password)) {
    sendJsonResponse(false, 'Please fill all required fields');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(false, 'Invalid email format');
}

if (strlen($password) < 6) {
    sendJsonResponse(false, 'Password must be at least 6 characters');
}

if ($password !== $confirm_password) {
    sendJsonResponse(false, 'Passwords do not match');
}

// Check if email exists
$checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($checkStmt, "s", $email);
mysqli_stmt_execute($checkStmt);
if (mysqli_stmt_get_result($checkStmt)->num_rows > 0) {
    sendJsonResponse(false, 'Email already registered');
}

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$insertStmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'student')");
mysqli_stmt_bind_param($insertStmt, "ssss", $full_name, $email, $phone, $password_hash);

if (mysqli_stmt_execute($insertStmt)) {
    sendJsonResponse(true, 'Registration successful! Please login.', null, '/ethioskillfactory/public/login.html');
} else {
    sendJsonResponse(false, 'Registration failed. Please try again.');
}
?>