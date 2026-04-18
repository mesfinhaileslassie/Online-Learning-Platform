<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

$result = mysqli_query($conn, "SELECT c.*, u.full_name as instructor_name 
                                FROM courses c 
                                JOIN users u ON c.instructor_id = u.id 
                                WHERE c.status = 'published' 
                                ORDER BY c.created_at DESC");

$courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['price_formatted'] = $row['price'] == 0 ? 'Free' : number_format($row['price'], 2) . ' ETB';
    $courses[] = $row;
}

echo json_encode(['success' => true, 'courses' => $courses]);
?>