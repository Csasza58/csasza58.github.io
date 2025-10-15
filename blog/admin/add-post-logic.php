<?php
require '../config/database.php';
require '../config/featured_posts.php';
require '../config/optimize-image.php';

// Ha nincs bejelentkezve, vissza a login oldalra
if (!isset($_SESSION['user-id'])) {
    header('location: ' . ROOT_URL . 'blog/admin/login.php');
    die();
}

if (isset($_POST['submit'])) {
    $author_id = $_SESSION['user-id'];
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = isset($_POST['is_featured']) ? filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $thumbnail = $_FILES['thumbnail'];

    // Ha nincs bejelölve a kiemelt, legyen 0
    $is_featured = ($is_featured == 1) ? 1 : 0;

    // Debug információk
    error_log("DEBUG - is_featured POST value: " . (isset($_POST['is_featured']) ? $_POST['is_featured'] : 'NOT SET'));
    error_log("DEBUG - is_featured final value: " . $is_featured);

    // Borítókép feltöltése, optimalizálása és WebP konverzió
    if ($thumbnail['name']) {
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $allowed_files = ['png', 'jpg', 'jpeg', 'webp'];
        $extension = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed_files)) {
            $_SESSION['add-post'] = "A kép nem megfelelő formátumú! (PNG/JPG/JPEG/WEBP)";
        } else {
            // Optimalizáljuk és konvertáljuk WebP formátumba
            $optimized_name = optimizePostThumbnail($thumbnail_tmp_name, $title);
            
            if (!$optimized_name) {
                $_SESSION['add-post'] = "Hiba a kép optimalizálásakor! Ellenőrizd hogy a GD library engedélyezve van.";
            } else {
                $thumbnail_destination_path = dirname(__DIR__) . '/images/' . $optimized_name;
                
                if (!move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path)) {
                    $_SESSION['add-post'] = "Hiba a kép mentésekor!";
                } else {
                    $thumbnail_name = $optimized_name;
                }
            }
        }
    } else {
        // Ha nincs kép feltöltve, használjuk az alapértelmezett képet
        $thumbnail_name = 'default-thumbnail.png';
    }

    // Validate input values
    if (!$title) {
        $_SESSION['add-post'] = "Kérlek add meg a poszt címét!";
    } elseif (!$category_id) {
        $_SESSION['add-post'] = "Kérlek válassz kategóriát!";
    } elseif (!$body) {
        $_SESSION['add-post'] = "Kérlek add meg a poszt tartalmát!";
    }

    // Return if validation fails
    if (isset($_SESSION['add-post'])) {
    // Ha hibás a validáció, vissza az űrlaphoz
    $_SESSION['add-post-data'] = $_POST;
    header('location: ' . ROOT_URL . 'blog/admin/add-post.php');
    die();
    } else {
        // Ha új kiemelt posztot választunk, kezeljük a 3 poszt limitet
        if ($is_featured > 0) {
            // Először hozzáadjuk a featured_date oszlopot ha még nincs
            $check_column = "SHOW COLUMNS FROM posts LIKE 'featured_date'";
            $column_exists = mysqli_query($connection, $check_column);
            if (mysqli_num_rows($column_exists) == 0) {
                $add_column = "ALTER TABLE posts ADD COLUMN featured_date TIMESTAMP NULL DEFAULT NULL AFTER is_featured";
                mysqli_query($connection, $add_column);
            }
            
            // Ellenőrizzük hány kiemelt poszt van már
            $count_featured = "SELECT COUNT(*) as count FROM posts WHERE is_featured > 0";
            $count_result = mysqli_query($connection, $count_featured);
            $count_row = mysqli_fetch_assoc($count_result);
            
            if ($count_row['count'] >= 3) {
                // Ha már 3 kiemelt van, a legrégebbit visszaállítjuk nem kiemeltre
                $oldest_featured = "SELECT id FROM posts WHERE is_featured > 0 ORDER BY featured_date ASC LIMIT 1";
                $oldest_result = mysqli_query($connection, $oldest_featured);
                $oldest_post = mysqli_fetch_assoc($oldest_result);
                
                if ($oldest_post) {
                    $remove_oldest = "UPDATE posts SET is_featured = 0, featured_date = NULL WHERE id = " . $oldest_post['id'];
                    mysqli_query($connection, $remove_oldest);
                }
            }
        }

        // Poszt hozzáadása az adatbázishoz prepared statement-tel (SQL injection védelem)
        $insert_post_query = "INSERT INTO posts (
                                title,
                                body,
                                thumbnail,
                                category_id,
                                author_id,
                                is_featured
                                ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($connection, $insert_post_query);
        mysqli_stmt_bind_param($stmt, 'sssiii', $title, $body, $thumbnail_name, $category_id, $author_id, $is_featured);
        $insert_post_result = mysqli_stmt_execute($stmt);

        if ($insert_post_result && !mysqli_errno($connection)) {
            // Ha sikerült a beszúrás, beállítjuk a kiemelt státuszt
            $new_post_id = mysqli_insert_id($connection);
            
            if ($is_featured > 0) {
                setManualFeatured($new_post_id, $connection, true);
            }
            
            mysqli_stmt_close($stmt);
            
            // Sikeres hozzáadás
            $_SESSION['add-post-success'] = "Új poszt címmel: $title sikeresen hozzáadva!";
            header('location: ' . ROOT_URL . 'blog/admin/index.php');
            die();
        } else {
            mysqli_stmt_close($stmt);
            // Hiba történt
            $_SESSION['add-post-data'] = $_POST;
            $_SESSION['add-post'] = "Hiba történt a hozzáadás során!";
            header('location: ' . ROOT_URL . 'blog/admin/add-post.php');
            die();
        }
    }
} else {
    // Ha nem kattintottak a gombra, vissza az űrlaphoz
    header('location: ' . ROOT_URL . 'blog/admin/add-post.php');
    die();
}