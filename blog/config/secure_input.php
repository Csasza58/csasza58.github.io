<?php
/**
 * Biztonságos Input Validáció és Sanitizálás
 */
class SecureInput {
    
    /**
     * Szöveg biztonságos tisztítása HTML entitásokkal
     */
    public static function sanitizeText($input, $maxLength = 255) {
        if (!is_string($input)) {
            return '';
        }
        
        // Trimmelés és maximális hossz korlátozása
        $input = trim($input);
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }
        
        // HTML entitások escape-elése
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Email validáció és sanitizálás
     */
    public static function sanitizeEmail($email) {
        $email = trim($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return $email;
    }
    
    /**
     * Egész szám validáció
     */
    public static function sanitizeInt($input, $min = null, $max = null) {
        $value = filter_var($input, FILTER_VALIDATE_INT);
        
        if ($value === false) {
            return false;
        }
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return $value;
    }
    
    /**
     * Jelszó erősségének ellenőrzése
     */
    public static function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'A jelszónak legalább 8 karakter hosszúnak kell lennie.';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'A jelszónak tartalmaznia kell legalább egy nagybetűt.';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'A jelszónak tartalmaznia kell legalább egy kisbetűt.';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'A jelszónak tartalmaznia kell legalább egy számot.';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'A jelszónak tartalmaznia kell legalább egy speciális karaktert.';
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Felhasználónév validáció
     */
    public static function validateUsername($username) {
        $username = trim($username);
        
        if (strlen($username) < 3) {
            return 'A felhasználónév legalább 3 karakter hosszú legyen.';
        }
        
        if (strlen($username) > 30) {
            return 'A felhasználónév maximum 30 karakter hosszú lehet.';
        }
        
        if (!preg_match('/^[a-zA-Z0-9_áéíóöőúüű]+$/', $username)) {
            return 'A felhasználónév csak betűket, számokat és aláhúzást tartalmazhat.';
        }
        
        return true;
    }
    
    /**
     * XSS védelem HTML tartalomhoz (markdown esetén)
     */
    public static function sanitizeHtml($html) {
        // Engedélyezett HTML tagek
        $allowed_tags = '<p><br><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre>';
        
        // HTML tisztítása
        $html = strip_tags($html, $allowed_tags);
        
        // JavaScript védelem
        $html = preg_replace('/on\w+="[^"]*"/i', '', $html);
        $html = preg_replace('/javascript:/i', '', $html);
        
        return $html;
    }
    
    /**
     * Rate limiting segédgép (egyszerű session-alapú)
     */
    public static function checkRateLimit($action, $maxAttempts = 5, $timeWindow = 300) {
        $key = "rate_limit_{$action}_" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
        }
        
        $data = $_SESSION[$key];
        
        // Időablak lejárt, nullázás
        if (time() - $data['first_attempt'] > $timeWindow) {
            $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
            $data = $_SESSION[$key];
        }
        
        // Limitáció ellenőrzése
        if ($data['count'] >= $maxAttempts) {
            return false;
        }
        
        // Számláló növelése
        $_SESSION[$key]['count']++;
        
        return true;
    }
}
?>