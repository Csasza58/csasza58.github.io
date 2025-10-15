<?php
/**
 * CSRF Protection Utility
 * Védi a weboldalat Cross-Site Request Forgery támadásoktól
 */
class CSRFProtection {
    
    /**
     * CSRF token generálása
     */
    public static function generateToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * CSRF token ellenőrzése
     */
    public static function validateToken($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * HTML input mező generálása CSRF tokennel
     */
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * CSRF token ellenőrzése POST adatokból
     */
    public static function validatePost() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? '';
            if (!self::validateToken($token)) {
                http_response_code(403);
                die('CSRF token érvénytelen. Kérjük próbálja újra.');
            }
        }
    }
    
    /**
     * Új token generálása (form újratöltés után)
     */
    public static function regenerateToken() {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        return $_SESSION[CSRF_TOKEN_NAME];
    }
}
?>