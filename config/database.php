<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ethioskillfactory');  // Changed from ethioskillfactoryV2

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]));
}

mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Africa/Addis_Ababa');
?>