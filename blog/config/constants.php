<?php
// Biztonsági konfigurációk betöltése
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/secure_input.php';
require_once __DIR__ . '/secure_upload.php';

// HTTPS kényszerítés (működő SSL tanúsítvány esetén)
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    if (!in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'])) {
        $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $redirectURL", true, 301);
        exit();
    }
}

// Biztonságos session beállítások
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $secure,
    'httponly' => SESSION_HTTPONLY,
    'samesite' => SESSION_SAMESITE
]);

session_start();

// Biztonsági fejlécek
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Font-barát CSP policy
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com; connect-src 'self';");

if ($secure) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// URL beállítás - automatikus protokoll detektálás
$protocol = $secure ? 'https://' : 'http://';
define('ROOT_URL', $protocol . $_SERVER['HTTP_HOST'] . '/');

// Adatbázis konstansok
define('DB_HOST', DB_HOST_SECURE);
define('DB_USER', DB_USER_SECURE);
define('DB_PASS', DB_PASS_SECURE);
define('DB_NAME', DB_NAME_SECURE);