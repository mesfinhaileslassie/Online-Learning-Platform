<?php
require_once 'config/database.php';

$result = mysqli_query($conn, "SELECT * FROM users");
echo "<h2>Database Test</h2>";
echo "Total users: " . mysqli_num_rows($result) . "<br>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "- " . $row['full_name'] . " (" . $row['email'] . ")<br>";
}
?>