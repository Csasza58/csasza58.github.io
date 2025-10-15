<?php
// Fejlett kiemelt poszt kezelés

function getFeaturedPosts($connection) {
    // 1. Legutóbbi 3 poszt automatikusan kiemelt (kivéve azokat amik manuálisan nem kiemeltek)
    $recent_query = "SELECT id FROM posts 
                     WHERE (featured_date IS NULL OR featured_date >= DATE_SUB(NOW(), INTERVAL 15 DAY))
                     ORDER BY date_time DESC LIMIT 3";
    $recent_result = mysqli_query($connection, $recent_query);
    $recent_posts = [];
    while ($row = mysqli_fetch_assoc($recent_result)) {
        $recent_posts[] = $row['id'];
    }
    
    // 2. Manuálisan kiemelt posztok (15 napnál nem régebbiek)
    $manual_query = "SELECT id FROM posts 
                     WHERE featured_date IS NOT NULL 
                     AND featured_date >= DATE_SUB(NOW(), INTERVAL 15 DAY)";
    $manual_result = mysqli_query($connection, $manual_query);
    $manual_posts = [];
    while ($row = mysqli_fetch_assoc($manual_result)) {
        $manual_posts[] = $row['id'];
    }
    
    // Egyesítjük a két listát (duplikációk eltávolítása)
    $featured_ids = array_unique(array_merge($recent_posts, $manual_posts));
    
    return $featured_ids;
}

function isPostFeatured($post_id, $connection) {
    $featured_posts = getFeaturedPosts($connection);
    return in_array($post_id, $featured_posts);
}

function setManualFeatured($post_id, $connection, $featured = true) {
    if ($featured) {
        // Beállítjuk manuálisan kiemeltre
        $query = "UPDATE posts SET featured_date = NOW() WHERE id = ?";
    } else {
        // Eltávolítjuk a manuális kiemelést
        $query = "UPDATE posts SET featured_date = NULL WHERE id = ?";
    }
    
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $post_id);
    return mysqli_stmt_execute($stmt);
}

function cleanupExpiredFeatured($connection) {
    // Eltávolítja a 15 napnál régebbi manuális kiemeléseket
    $query = "UPDATE posts SET featured_date = NULL 
              WHERE featured_date IS NOT NULL 
              AND featured_date < DATE_SUB(NOW(), INTERVAL 15 DAY)";
    return mysqli_query($connection, $query);
}
?>