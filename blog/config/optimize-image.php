<?php

/**
 * Ékezetek eltávolítása és URL-barát név generálása
 */
function sanitizeFileName($string)
{
    $replacements = [
        'á' => 'a',
        'Á' => 'A',
        'é' => 'e',
        'É' => 'E',
        'í' => 'i',
        'Í' => 'I',
        'ó' => 'o',
        'Ó' => 'O',
        'ö' => 'o',
        'Ő' => 'O',
        'ő' => 'o',
        'Ö' => 'O',
        'ú' => 'u',
        'Ú' => 'U',
        'ü' => 'u',
        'Ű' => 'U',
        'ű' => 'u',
        'Ü' => 'U'
    ];

    $string = strtr($string, $replacements);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    $string = trim($string, '-');

    return $string;
}

/**
 * Kép optimalizálás és WebP konverzió
 */
function optimizeAndConvertImage($tmpPath, $baseName, $maxWidth = 1200, $maxHeight = 1200, $quality = 80)
{
    if (!file_exists($tmpPath)) {
        return false;
    }

    $imageInfo = getimagesize($tmpPath);
    if (!$imageInfo) {
        return false;
    }

    $mimeType = $imageInfo['mime'];

    // Kép betöltése típus szerint
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($tmpPath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($tmpPath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($tmpPath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($tmpPath);
            break;
        default:
            return false;
    }

    if (!$sourceImage) {
        return false;
    }

    $originalWidth = imagesx($sourceImage);
    $originalHeight = imagesy($sourceImage);

    // Átméretezés számítása
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight, 1);
    $newWidth = (int)($originalWidth * $ratio);
    $newHeight = (int)($originalHeight * $ratio);

    // Új kép létrehozása
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    // Átlátszóság megőrzése
    imagealphablending($resizedImage, false);
    imagesavealpha($resizedImage, true);

    // Átméretezés
    imagecopyresampled(
        $resizedImage,
        $sourceImage,
        0,
        0,
        0,
        0,
        $newWidth,
        $newHeight,
        $originalWidth,
        $originalHeight
    );

    // Fájlnév generálása
    $sanitizedName = sanitizeFileName($baseName);
    $uniqueId = uniqid();
    $newFileName = $sanitizedName . '-' . $uniqueId . '.webp';

    // WebP ÁTMENETI MENTÉSE egy új helyre (nem írjuk felül a tmp-t!)
    $webp_temp_path = sys_get_temp_dir() . '/' . $newFileName;
    $success = imagewebp($resizedImage, $webp_temp_path, $quality);

    // Memória felszabadítása
    imagedestroy($sourceImage);
    imagedestroy($resizedImage);

    if ($success) {
        // FELÜLÍRJUK az eredeti temp fájlt a WebP-vel
        if (copy($webp_temp_path, $tmpPath)) {
            unlink($webp_temp_path); // töröljük az átmeneti WebP-t
            return $newFileName;
        }
    }

    return false;
}

/**
 * Profilképek optimalizálása (400x400 max, 85% minőség)
 */
function optimizeProfileImage($tmpPath, $username)
{
    return optimizeAndConvertImage($tmpPath, $username, 400, 400, 85);
}

/**
 * Poszt borítóképek optimalizálása (1600x1600 max, 80% minőség)
 */
function optimizePostThumbnail($tmpPath, $postTitle)
{
    return optimizeAndConvertImage($tmpPath, $postTitle, 1600, 1600, 80);
}
