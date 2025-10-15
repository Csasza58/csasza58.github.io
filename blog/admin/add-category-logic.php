<?php
require 'config/database.php';

if (isset($_POST['submit'])){
    // Űrlapadatok lekérése
    $title          = filter_var($_POST['title'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description    = filter_var($_POST['description'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(!$title){
        $_SESSION['add-category'] = "Kérlek add meg a kategória nevét!";
    } elseif(!$description){
        $_SESSION['add-category'] = "Kérlek add meg a leírást!";
    }
    
    if(isset( $_SESSION['add-category'])){
    // Ha hibás a validáció, vissza az űrlaphoz
    $_SESSION['add-category-data'] = $_POST;
    header('location: '. ROOT_URL .'blog/admin/add-category.php');
    die();
    } else {
        // Kategória hozzáadása az adatbázishoz
        $insert_category_query = "INSERT INTO categories (title, description) VALUES ('$title', '$description')";
        $insert_category_result = mysqli_query($connection, $insert_category_query);
        if(mysqli_errno($connection)){
            // Hiba történt
            $_SESSION['add-category'] = "Hiba történt a kategória hozzáadásakor!";
            header('location: '. ROOT_URL .'blog/admin/add-category.php');
            die();
        } else {
            // Sikeres hozzáadás
            $_SESSION['add-category-success'] = "Új kategória: $title sikeresen hozzáadva!";
            header('location: '. ROOT_URL .'blog/admin/manage-categories.php');
            die();
        }
    }
}
