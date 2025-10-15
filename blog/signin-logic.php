<?php
require 'config/database.php';

// CSRF védelem ellenőrzése
CSRFProtection::validatePost();

if (isset($_POST['submit'])){
    // Rate limiting ellenőrzése
    if (!SecureInput::checkRateLimit('login', 5, 900)) { // 5 kísérlet 15 percenként
        $_SESSION['signin'] = "Túl sok sikertelen bejelentkezési kísérlet. Kérjük próbálja újra 15 perc múlva.";
        header('location: ' . ROOT_URL . 'blog/signin.php');
        die();
    }
    
    // Űrlapadatok biztonságos lekérése
    $username_email = SecureInput::sanitizeText($_POST['username_email'] ?? '', 100);
    $password       = $_POST['password'] ?? '';

    // Validate input values
    if(!$username_email){
        $_SESSION['signin'] = "Kérlek add meg a felhasználónevet vagy email címet!";
    } elseif(!$password){
        $_SESSION['signin'] = "Kérlek add meg a jelszót!";
    } elseif(strlen($password) > 255) {
        $_SESSION['signin'] = "Jelszó túl hosszú!";
    } else {
        // Fetch user from users table
        $fetch_user_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($connection, $fetch_user_query);
        mysqli_stmt_bind_param($stmt, 'ss', $username_email, $username_email);
        mysqli_stmt_execute($stmt);
        $fetch_user_result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($fetch_user_result) == 1){
            // Rekord átalakítása asszociatív tömbbé
            $user_record = mysqli_fetch_assoc($fetch_user_result);
            $db_password = $user_record['password'];

            // Jelszó ellenőrzése
            if(password_verify($password, $db_password)){
                // Sikeres bejelentkezés
                $_SESSION['user-id'] = $user_record['id'];
                if($user_record['is_admin'] == 1){
                    $_SESSION['user_is_admin'] = true;  
                }

                header('location: '. ROOT_URL .'blog/admin/');
            } else {
                $_SESSION['signin'] = "Hibás jelszó!";
            }
        } else {
            $_SESSION['signin'] = "Hibás felhasználónév vagy email!";
        }
    }

    if(isset($_SESSION['signin'])){
    // Ha hibás a validáció, vissza az űrlaphoz
    $_SESSION['signin-data'] = $_POST;
    header('location: '. ROOT_URL .'blog/signin.php');
    die();
    }

}   else {
    // Ha nem kattintottak a gombra, vissza az űrlaphoz
    header('location: '. ROOT_URL .'blog/signin.php');
    die();
}