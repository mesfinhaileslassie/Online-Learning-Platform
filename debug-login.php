<?php
require_once 'config/database.php';

echo "<h2>Login Debug</h2>";

// Test with student account
$email = 'student@example.com';
$password = 'password123';

echo "Testing: $email / $password<br>";

$stmt = mysqli_prepare($conn, "SELECT id, full_name, email, role, password_hash FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($stmt);

if ($user) {
    echo "User found: " . $user['full_name'] . "<br>";
    echo "Password hash in DB: " . $user['password_hash'] . "<br>";
    
    // Test password verification
    if (password_verify($password, $user['password_hash'])) {
        echo "<span style='color:green'>✅ password_verify() returns TRUE</span><br>";
    } else {
        echo "<span style='color:red'>❌ password_verify() returns FALSE</span><br>";
    }
    
    if ($password === 'password123') {
        echo "<span style='color:green'>✅ Direct comparison works</span><br>";
    }
} else {
    echo "User not found!<br>";
}

echo "<hr>";

// Show all users
$result = mysqli_query($conn, "SELECT id, email, password_hash FROM users");
echo "<h3>All Users in Database:</h3>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: {$row['id']} - Email: {$row['email']} - Hash: " . substr($row['password_hash'], 0, 20) . "...<br>";
}
?>