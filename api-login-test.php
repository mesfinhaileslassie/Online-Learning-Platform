<?php
session_start();
require_once 'config/database.php';

// Handle both GET and POST
$email = $_POST['email'] ?? $_GET['email'] ?? '';
$password = $_POST['password'] ?? $_GET['password'] ?? '';

// If this is a POST request from the login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($email)) {
    $stmt = mysqli_prepare($conn, "SELECT id, full_name, email, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if ($user && $password === 'password123') {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        echo "SUCCESS";
    } else {
        echo "FAILED";
    }
    exit();
}

// If just viewing the page (GET request)
echo "<h2 style='color:green'>✅ API is working!</h2>";
echo "Send POST request to this file with email and password to login.";
?>