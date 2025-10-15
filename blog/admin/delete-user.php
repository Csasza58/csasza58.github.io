<?php
require 'config/database.php';

if(isset($_GET['id'])){
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch users
    $query = "SELECT * FROM users WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);

    // Make sure user equal to one
    if(mysqli_num_rows($result) == 1){
        $avatar_name = $user['avatar'];
        $avatar_path = '../images/' . $avatar_name;

        // Only delete if file exists and is not default avatar
        if($avatar_name && $avatar_name != 'default-avatar.jpg' && file_exists($avatar_path)){
            unlink($avatar_path);
        }
    }

    // Fetch all thumbnails of the user and delete them
    $thumbnail_query = "SELECT thumbnail FROM posts WHERE author_id=$id";
    $thumbnail_result = mysqli_query($connection, $thumbnail_query);

    if(mysqli_num_rows($thumbnail_result) > 0){
        while($thumbnail = mysqli_fetch_assoc($thumbnail_result)){
            $thumbnail_name = $thumbnail['thumbnail'];
            $thumbnail_path = '../images/' . $thumbnail_name;
            // Delete thumbnail from images if file exists and is not default
            if($thumbnail_name && $thumbnail_name != 'default-thumbnail.png' && file_exists($thumbnail_path)){
                unlink($thumbnail_path);
            }
        }
    }


    // Delete user from database
    $delete_user_query = "DELETE FROM users WHERE id=$id";
    $delete_user_result = mysqli_query($connection, $delete_user_query);

    if(mysqli_errno($connection)){
        $_SESSION['delete-user'] = "Nem tudod törölni a következő felhasználót: {$user['firstname']} {$user['lastname']}";
    } else {
        $_SESSION['delete-user-success'] = "Felhasználó {$user['firstname']} {$user['lastname']} sikeresen törölve!";
    }

}

header('location: '. ROOT_URL .'blog/admin/manage-users.php');
die();
