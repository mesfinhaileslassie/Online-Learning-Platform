<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

// Get parameters
$category = $_GET['category'] ?? '';
$level = $_GET['level'] ?? '';
$language = $_GET['language'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$where = "WHERE c.status = 'published'";
$params = [];
$types = "";

if (!empty($category)) {
    $where .= " AND c.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

if (!empty($level)) {
    $where .= " AND c.level = ?";
    $params[] = $level;
    $types .= "s";
}

if (!empty($language)) {
    $where .= " AND c.language = ?";
    $params[] = $language;
    $types .= "s";
}

// Order by
$orderBy = "c.created_at DESC";
switch ($sort) {
    case 'popular':
        $orderBy = "c.total_enrollments DESC";
        break;
    case 'price_low':
        $orderBy = "c.price ASC";
        break;
    case 'price_high':
        $orderBy = "c.price DESC";
        break;
    case 'rating':
        $orderBy = "c.average_rating DESC";
        break;
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM courses c $where";
$countStmt = mysqli_prepare($conn, $countQuery);
if (!empty($params)) {
    mysqli_stmt_bind_param($countStmt, $types, ...$params);
}
mysqli_stmt_execute($countStmt);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt))['total'];

// Get courses
$query = "SELECT c.*, u.full_name as instructor_name, cat.name as category_name 
          FROM courses c
          JOIN users u ON c.instructor_id = u.id
          JOIN categories cat ON c.category_id = cat.id
          $where
          ORDER BY $orderBy
          LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['price_formatted'] = formatPrice($row['price']);
    $courses[] = $row;
}

sendJsonResponse(true, 'Courses fetched', [
    'courses' => $courses,
    'total' => $total,
    'page' => $page,
    'total_pages' => ceil($total / $limit)
]);
?>