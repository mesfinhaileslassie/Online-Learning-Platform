<?php
$url = 'http://localhost/ethioskillfactoryV2/api/auth/login.php';

$data = [
    'email' => 'student@example.com',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<h2>API Response:</h2>";
echo "<pre>";
print_r(json_decode($response, true));
echo "</pre>";
?>