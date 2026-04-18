<?php
require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';

if (isLoggedIn()) {
    sendJsonResponse(true, 'Logged in', [
        'user' => [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ]
    ]);
} else {
    sendJsonResponse(false, 'Not logged in');
}
?>