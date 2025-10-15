<?php
require 'config/database.php';

if(isset($_GET['id'])){
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Update post that belong to this category
    $update_query = "UPDATE posts SET category_id=7 WHERE id=$id";
    $update_result = mysqli_query($connection, $update_query);

    if(!mysqli_errno($connection)){
        // Delete category from database
        $delete_category_query = "DELETE FROM categories WHERE id=$id";
        $delete_category_result = mysqli_query($connection, $delete_category_query);
        $_SESSION['delete-category-success'] = "Category {$category['title']} deleted succesfully";
    } else {
        $_SESSION['delete-category'] = "Could not delete category {$category['title']}";
    }

}
header('location: '. ROOT_URL .'blog/admin/manage-categories.php');
die();