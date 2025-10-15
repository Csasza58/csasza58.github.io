<?php
session_start();

// SYNOLOGY SZERVER KONFIGURÁCIÓS FÁJL
// Módosítsa ezeket az értékeket a saját Synology szerver beállításai szerint

// ============= WEBOLDAL BEÁLLÍTÁSOK =============
// Cserélje le 'your-synology-ip-or-domain' részt a tényleges Synology szerver IP címére vagy domain nevére
// Például: 'http://192.168.1.100/blog/' vagy 'https://yourdomain.com/blog/'
define('ROOT_URL', 'http://your-synology-ip-or-domain/blog/');

// ============= MYSQL/MARIADB ADATBÁZIS BEÁLLÍTÁSOK =============
// Synology DSM MySQL/MariaDB beállítások
define('DB_HOST', 'localhost');                    // Vagy '127.0.0.1' ha a localhost nem működik
define('DB_USER', 'your-mysql-username');          // A MySQL felhasználónév
define('DB_PASS', 'your-mysql-password');          // A MySQL jelszó
define('DB_NAME', 'blog');                         // Az adatbázis neve (ezt megtarthatja 'blog'-ként)

// ============= SYNOLOGY SPECIFIKUS BEÁLLÍTÁSOK =============
// Időzóna beállítás (Synology szerver időzónája szerint)
date_default_timezone_set('Europe/Budapest');

// Karakterkódolás beállítás
ini_set('default_charset', 'UTF-8');

// ============= BIZTONSÁGI BEÁLLÍTÁSOK =============
// Hibakijelzés kikapcsolása production környezetben
// Fejlesztés során kommentelje ki ezt a sort
// error_reporting(0);
// ini_set('display_errors', 0);

// ============= FÁJLKEZELÉSI BEÁLLÍTÁSOK =============
// Maximális fájlfeltöltés méret (Synology korlátai szerint)
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

// ============= TELEPÍTÉSI ÚTMUTATÓ =============
/*
SYNOLOGY SZERVER BEÁLLÍTÁSI LÉPÉSEK:

1. MYSQL/MARIADB TELEPÍTÉSE:
   - DSM Package Center > MariaDB telepítése
   - phpMyAdmin telepítése (opcionális, de ajánlott)
   - MySQL/MariaDB szolgáltatás indítása

2. ADATBÁZIS LÉTREHOZÁSA:
   - Jelentkezzen be MySQL-be root felhasználóval
   - Hozzon létre új adatbázist: CREATE DATABASE blog CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;
   - Hozzon létre új felhasználót: CREATE USER 'your-mysql-username'@'localhost' IDENTIFIED BY 'your-mysql-password';
   - Adjon jogosultságokat: GRANT ALL PRIVILEGES ON blog.* TO 'your-mysql-username'@'localhost';
   - Frissítse a jogosultságokat: FLUSH PRIVILEGES;

3. WEB STATION BEÁLLÍTÁSA:
   - DSM Package Center > Web Station telepítése
   - PHP 7.4+ vagy 8.x telepítése
   - Apache HTTP Server 2.4 telepítése
   - Új virtuális host létrehozása a blog számára

4. FÁJLOK FELTÖLTÉSE:
   - Másolja az összes blog fájlt a /volume1/web/ vagy a megfelelő web könyvtárba
   - Állítsa be a megfelelő jogosultságokat: chmod 755 mappákra, chmod 644 fájlokra
   - Az images/ mappához írási jogosultság szükséges: chmod 775

5. SSL/HTTPS BEÁLLÍTÁSA (ajánlott):
   - DSM Control Panel > Security > Certificate > Add új tanúsítvány
   - Web Station-ban állítsa be a HTTPS-t
   - Módosítsa a ROOT_URL-t https:// protokollra

6. TESZTELÉS:
   - Ellenőrizze a MySQL kapcsolatot
   - Tesztelje a fájlfeltöltés működését
   - Ellenőrizze a jogosultságokat és bejelentkezést
*/