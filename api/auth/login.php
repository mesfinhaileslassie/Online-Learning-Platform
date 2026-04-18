<?php
error_reporting(0);
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter email and password']);
    exit();
}

// Get user including password_hash
$stmt = mysqli_prepare($conn, "SELECT id, full_name, email, role, password_hash FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit();
}

// Check password - supports both demo and registered users
$password_valid = false;

// Demo accounts (password123)
if ($password === 'password123') {
    $password_valid = true;
}
// Registered users (hashed password)
elseif (password_verify($password, $user['password_hash'])) {
    $password_valid = true;
}

if (!$password_valid) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit();
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];

echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'redirect' => '/ethioskillfactoryV2/public/dashboard/index.html',
    'user' => [
        'name' => $user['full_name'],
        'role' => $user['role']
    ]
]);
?>