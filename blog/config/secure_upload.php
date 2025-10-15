<?php
/**
 * Biztonságos Fájlfeltöltés Kezelő
 */
class SecureFileUpload {
    
    /**
     * Fájl validálása és biztonságos feltöltése
     */
    public static function uploadImage($file, $uploadDir = null) {
        if (!$uploadDir) {
            $uploadDir = UPLOAD_PATH;
        }
        
        // Fájl létezésének ellenőrzése
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Fájl feltöltési hiba.'];
        }
        
        // Fájlméret ellenőrzése
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'A fájl túl nagy. Maximum ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB megengedett.'];
        }
        
        // Fájlkiterjesztés ellenőrzése
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'error' => 'Nem engedélyezett fájltípus. Csak ' . implode(', ', ALLOWED_EXTENSIONS) . ' formátumok.'];
        }
        
        // MIME típus ellenőrzése (dupla védelem)
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg', 
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];
        
        $detectedMime = mime_content_type($file['tmp_name']);
        if (!isset($allowedMimes[$fileExtension]) || $detectedMime !== $allowedMimes[$fileExtension]) {
            return ['success' => false, 'error' => 'Fájl tartalma nem egyezik a kiterjesztéssel.'];
        }
        
        // Kép méret ellenőrzése (ha kép)
        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageInfo = getimagesize($file['tmp_name']);
            if (!$imageInfo) {
                return ['success' => false, 'error' => 'Érvénytelen kép fájl.'];
            }
            
            // Maximum kép méret: 2000x2000 pixel
            if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
                return ['success' => false, 'error' => 'A kép túl nagy. Maximum 2000x2000 pixel megengedett.'];
            }
        }
        
        // Biztonságos fájlnév generálása
        $safeName = self::generateSafeFileName($file['name']);
        $destination = $uploadDir . $safeName;
        
        // Fájl áthelyezése
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'error' => 'Fájl mentése sikertelen.'];
        }
        
        // Fájl jogosultságok beállítása
        chmod($destination, 0644);
        
        return [
            'success' => true, 
            'filename' => $safeName,
            'path' => $destination,
            'size' => $file['size'],
            'type' => $detectedMime
        ];
    }
    
    /**
     * Biztonságos fájlnév generálása
     */
    public static function generateSafeFileName($originalName) {
        // Eredeti kiterjesztés megőrzése
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Timestamp + random string a egyediség érdekében
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        
        return $timestamp . $randomString . '.' . $extension;
    }
    
    /**
     * Fájl biztonságos törlése
     */
    public static function deleteFile($filename, $uploadDir = null) {
        if (!$uploadDir) {
            $uploadDir = UPLOAD_PATH;
        }
        
        $filePath = $uploadDir . $filename;
        
        // Ellenőrizzük, hogy a fájl a megengedett könyvtárban van
        $realPath = realpath($filePath);
        $realUploadDir = realpath($uploadDir);
        
        if ($realPath === false || strpos($realPath, $realUploadDir) !== 0) {
            return false; // Path traversal támadás védelme
        }
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return true;
    }
    
    /**
     * Kép átméretezése (opcionális, GD extension szükséges)
     */
    public static function resizeImage($sourcePath, $destPath, $maxWidth = 800, $maxHeight = 600) {
        if (!extension_loaded('gd')) {
            return false;
        }
        
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }
        
        list($originalWidth, $originalHeight, $imageType) = $imageInfo;
        
        // Arányos átméretezés kalkuláció
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = round($originalWidth * $ratio);
        $newHeight = round($originalHeight * $ratio);
        
        // Forrás kép betöltése
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }
        
        if (!$sourceImage) {
            return false;
        }
        
        // Új kép létrehozása
        $destImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // PNG átlátszóság megőrzése
        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
        }
        
        // Átméretezés
        imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Mentés
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($destImage, $destPath, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($destImage, $destPath);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($destImage, $destPath);
                break;
        }
        
        // Memória felszabadítása
        imagedestroy($sourceImage);
        imagedestroy($destImage);
        
        return $result;
    }
}
?>