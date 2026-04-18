<?php
// Site Configuration
define('SITE_NAME', 'EthioSkillFactory');
define('SITE_URL', 'http://localhost/ethioskillfactory%20v2');
define('API_URL', SITE_URL . '/api');
define('SITE_EMAIL', 'info@ethioskillfactory.com');
define('CURRENCY', 'ETB');

// Upload paths
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ethioskillfactory/uploads/');
define('THUMBNAIL_PATH', UPLOAD_PATH . 'thumbnails/');
define('VIDEO_PATH', UPLOAD_PATH . 'videos/');

// File size limits
define('MAX_VIDEO_SIZE', 500 * 1024 * 1024); // 500MB
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024);   // 5MB

// Commission (70% to instructor)
define('INSTRUCTOR_COMMISSION', 0.70);

// Session timeout (2 hours)
define('SESSION_TIMEOUT', 7200);
?>