<?php
require 'config/database.php';
require 'config/optimize-image.php';

if (isset($_POST['submit'])){
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $firstname = trim(filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $lastname = trim(filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);
    $avatar = $_FILES['avatar'] ?? null;
    $previous_avatar = filter_var($_POST['previous_avatar'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Check for valid input
    if(!$firstname || !$lastname){
        $_SESSION['edit-user'] = "Érvénytelen adatok!";
    } else {
        $avatar_name = $previous_avatar; // Alapértelmezetten a régi avatar marad
        
        // Avatar módosítása ha új kép lett feltöltve
        if ($avatar && $avatar['name']) {
            $avatar_tmp_name = $avatar['tmp_name'];
            $allowed_files = ['png', 'jpg', 'jpeg', 'webp'];
            $extension = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowed_files)) {
                $_SESSION['edit-user'] = "A kép nem megfelelő formátumú! (PNG/JPG/JPEG/WEBP)";
            } else {
                // WebP optimalizálás username alapján
                $username = $firstname . ' ' . $lastname;
                $optimized_name = optimizeProfileImage($avatar_tmp_name, $username);
                
                if (!$optimized_name) {
                    $_SESSION['edit-user'] = 'A kép optimalizálása sikertelen.';
                } else {
                    $avatar_destination_path = '../images/' . $optimized_name;
                    
                    if (!move_uploaded_file($avatar_tmp_name, $avatar_destination_path)) {
                        $_SESSION['edit-user'] = 'A kép mentése sikertelen.';
                    } else {
                        // Töröljük a régi képet ha nem default
                        if ($previous_avatar != 'default-avatar.png') {
                            $old_avatar_path = '../images/' . $previous_avatar;
                            if (file_exists($old_avatar_path)) {
                                unlink($old_avatar_path);
                            }
                        }
                        $avatar_name = $optimized_name;
                    }
                }
            }
        }
        
        // Ha volt hiba, vissza
        if (isset($_SESSION['edit-user'])) {
            header('location: '. ROOT_URL .'blog/admin/edit-user.php?id=' . $id);
            die();
        }
        
        // UPDATE prepared statement-tel
        $query = "UPDATE users SET firstname = ?, lastname = ?, is_admin = ?, avatar = ? WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'ssisi', $firstname, $lastname, $is_admin, $avatar_name, $id);
        $result = mysqli_stmt_execute($stmt);

        if(!$result || mysqli_errno($connection)){
            mysqli_stmt_close($stmt);
            $_SESSION['edit-user'] = "Ismeretlen hiba, nem sikerült frissíteni!";
        } else {
            mysqli_stmt_close($stmt);
            $_SESSION['edit-user-success'] = "Felhasználó $firstname $lastname sikeresen frissítve!";
        }
    }
} 

header('location: '. ROOT_URL .'blog/admin/manage-users.php');
die();
