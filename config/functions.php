<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserName() {
    return $_SESSION['user_name'] ?? 'User';
}

function requireLogin() {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Please login first', 'redirect' => '/ethioskillfactory/login.html']);
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (getUserRole() !== $role && getUserRole() !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit();
    }
}

function formatPrice($price) {
    if ($price == 0) return 'Free';
    return number_format($price, 2) . ' ' . CURRENCY;
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function sendJsonResponse($success, $message, $data = null, $redirect = null) {
    $response = ['success' => $success, 'message' => $message];
    if ($data) $response['data'] = $data;
    if ($redirect) $response['redirect'] = $redirect;
    echo json_encode($response);
    exit();
}
?>