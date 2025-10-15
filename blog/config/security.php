<?php
// BIZTONSÁGOS KÖRNYEZETI VÁLTOZÓK
// SOHA ne tedd ezt a fájlt nyilvános helyre!

define('DB_HOST_SECURE','127.0.0.1:3306');
define('DB_USER_SECURE','1507_user'); // Korlátozott jogosultságú felhasználó
define('DB_PASS_SECURE','&3dH3n7$n#!kB*U'); // Erős, egyedi jelszó
define('DB_NAME_SECURE','blog');

// Session biztonság
define('SESSION_SECURE', true);
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');

// CSRF védelem
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_SECRET_KEY', 'BlogSecure1507#Key!Random');

// Upload biztonság
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('UPLOAD_PATH', __DIR__ . '/../images/');
?>