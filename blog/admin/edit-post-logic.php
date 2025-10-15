<?php
session_start();
require '../config/database.php';
require '../config/featured_posts.php';
require '../config/optimize-image.php';

if(isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validáció
    if (!$title) {
        $_SESSION['edit-post'] = 'Kérlek, add meg a poszt címét!';
    } elseif (!$category_id) {
        $_SESSION['edit-post'] = 'Kérlek, válassz egy kategóriát!';
    } elseif (!$body) {
        $_SESSION['edit-post'] = 'Kérlek, add meg a poszt tartalmát!';
    } 
    
    // Kép kezelése - opcionális szerkesztéskor
    $thumbnail_name = $previous_thumbnail_name; // Alapértelmezetten a régi kép marad
    
    if ($thumbnail['name']) {
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $allowed_files = ['png', 'jpg', 'jpeg', 'webp'];
        $extension = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed_files)) {
            $_SESSION['edit-post'] = "A kép nem megfelelő formátumú! (PNG/JPG/JPEG/WEBP)";
        } else {
            // WebP optimalizálás
            $optimized_name = optimizePostThumbnail($thumbnail_tmp_name, $title);
            
            if (!$optimized_name) {
                $_SESSION['edit-post'] = 'A kép optimalizálása sikertelen.';
            } else {
                $thumbnail_destination_path = dirname(__DIR__) . '/images/' . $optimized_name;
                
                if (!move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path)) {
                    $_SESSION['edit-post'] = 'A kép mentése sikertelen.';
                } else {
                    // Töröljük a régi képet ha nem default
                    if ($previous_thumbnail_name != 'default-thumbnail.png') {
                        $old_thumbnail_path = dirname(__DIR__) . '/images/' . $previous_thumbnail_name;
                        if (file_exists($old_thumbnail_path)) {
                            unlink($old_thumbnail_path);
                        }
                    }
                    $thumbnail_name = $optimized_name;
                }
            }
        }
    }

    // Ha volt hiba, vissza a szerkesztéshez
    if (isset($_SESSION['edit-post'])) {
        header('location: ' . ROOT_URL . 'blog/admin/edit-post.php?id=' . $id);
        die();
    }

    // Kiemelt státusz kezelése
    if ($is_featured > 0) {
        $check_column = "SHOW COLUMNS FROM posts LIKE 'featured_date'";
        $column_exists = mysqli_query($connection, $check_column);
        if (mysqli_num_rows($column_exists) == 0) {
            $add_column = "ALTER TABLE posts ADD COLUMN featured_date TIMESTAMP NULL DEFAULT NULL AFTER is_featured";
            mysqli_query($connection, $add_column);
        }
        
        $count_featured = "SELECT COUNT(*) as count FROM posts WHERE is_featured > 0 AND id != ?";
        $stmt = mysqli_prepare($connection, $count_featured);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $count_result = mysqli_stmt_get_result($stmt);
        $count_row = mysqli_fetch_assoc($count_result);
        mysqli_stmt_close($stmt);
        
        if ($count_row['count'] >= 3) {
            $oldest_featured = "SELECT id FROM posts WHERE is_featured > 0 AND id != ? ORDER BY featured_date ASC LIMIT 1";
            $stmt = mysqli_prepare($connection, $oldest_featured);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $oldest_result = mysqli_stmt_get_result($stmt);
            $oldest_post = mysqli_fetch_assoc($oldest_result);
            mysqli_stmt_close($stmt);
            
            if ($oldest_post) {
                $remove_oldest = "UPDATE posts SET is_featured = 0, featured_date = NULL WHERE id = ?";
                $stmt = mysqli_prepare($connection, $remove_oldest);
                mysqli_stmt_bind_param($stmt, 'i', $oldest_post['id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }

    // UPDATE a poszt
    $update_query = "UPDATE posts SET title = ?, body = ?, category_id = ?, thumbnail = ?, is_featured = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $update_query);
    mysqli_stmt_bind_param($stmt, 'ssissi', $title, $body, $category_id, $thumbnail_name, $is_featured, $id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        if ($is_featured > 0) {
            setManualFeatured($id, $connection, true);
        }
        mysqli_stmt_close($stmt);
        
        $_SESSION['edit-post-success'] = "Poszt sikeresen frissítve!";
        header('location: ' . ROOT_URL . 'blog/admin/index.php');
        die();
    } else {
        mysqli_stmt_close($stmt);
        $_SESSION['edit-post'] = "Hiba történt a frissítés során!";
        header('location: ' . ROOT_URL . 'blog/admin/edit-post.php?id=' . $id);
        die();
    }

} else {
    header('location: ' . ROOT_URL . 'blog/admin/index.php');
    die();
}
