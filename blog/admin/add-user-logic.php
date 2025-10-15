<?php
require 'config/database.php';
require 'config/optimize-image.php';

// Get Signup form data if signup button was clicked
if (isset($_POST['submit'])) {
    // Űrlapadatok lekérése
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);
    $avatar = $_FILES['avatar'];

    // Ellenőrzés: változók átadva?
    //var_dump($avatar);
    // echo $firstname, $lastname, $username, $email, $createpassword, $confirmpassword;

    // Validate input values
    if (!$firstname) {
        $_SESSION['add-user'] = "Kérlek add meg a keresztnevet!";
    } elseif (!$lastname) {
        $_SESSION['add-user'] = "Kérlek add meg a vezetéknevet!";
    } elseif (!$username) {
        $_SESSION['add-user'] = "Kérlek add meg a felhasználónevet!";
    } elseif (!$email) {
        $_SESSION['add-user'] = "Kérlek adj meg egy érvényes email címet!";
        // } elseif(!$is_admin){
        //     $_SESSION['add-user'] = "Please select a user role";
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['add-user'] = "A jelszónak legalább 8 karakter hosszúnak kell lennie!";
        // Avatar is now optional
    } else {
        // Check if passwords dont match
        if ($createpassword !== $confirmpassword) {
            $_SESSION['add-user'] = "A jelszavak nem egyeznek!";
        } else {
            // Jelszó titkosítása
            $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

            // Check if password is hashed successfully
            // echo $createpassword .'<br/>';
            // echo $hashed_password;

            // Check if username/email already exist
            $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
            $user_check_result = mysqli_query($connection, $user_check_query);

            if (mysqli_num_rows($user_check_result) > 0) {
                $_SESSION['add-user'] = "A felhasználónév vagy email már létezik!";
            } else {
                // Handle avatar upload or set default
                // PROFILKÉP OPTIMALIZÁLÁS ÉS WEBP KONVERZIÓ
                if ($avatar['name']) {
                    $avatar_tmp_name = $avatar['tmp_name'];

                    // WebP optimalizálás username alapján
                    $optimized_name = optimizeProfileImage($avatar_tmp_name, $username);

                    if ($optimized_name) {
                        $avatar_destination_path = '../images/' . $optimized_name;

                        if (move_uploaded_file($avatar_tmp_name, $avatar_destination_path)) {
                            $avatar_name = $optimized_name;
                        } else {
                            $_SESSION['add-user'] = 'Profilkép mentése sikertelen.';
                        }
                    } else {
                        $_SESSION['add-user'] = 'A kép optimalizálása sikertelen. Érvényes képformátumot (JPG, PNG) használj.';
                    }
                } else {
                    $avatar_name = 'default-avatar.png';
                }
            }
        }
    }

    // Return if validation fails
    if (isset($_SESSION['add-user'])) {
        // Ha hibás a validáció, vissza az űrlaphoz
        $_SESSION['add-user-data'] = $_POST;
        header('location: ' . ROOT_URL . 'blog/admin/add-user.php');
        die();
    } else {
        // Felhasználó hozzáadása az adatbázishoz
        $insert_user_query = "INSERT INTO users (
                                firstname,
                                lastname,
                                username,
                                email,
                                password,
                                avatar,
                                is_admin
                                ) VALUES (
                                '$firstname',
                                '$lastname',
                                '$username',
                                '$email',
                                '$hashed_password',
                                '$avatar_name',
                                '$is_admin'
                                )";
        $insert_user_result = mysqli_query($connection, $insert_user_query);
        if (!mysqli_errno($connection)) {
            // Sikeres hozzáadás
            $_SESSION['add-user-success'] = "Új felhasználó: $firstname $lastname sikeresen hozzáadva!";
            header('location:' . ROOT_URL . 'blog/admin/manage-users.php');
            die();
        } else {
            // Hiba történt
            $_SESSION['add-user-data'] = $_POST;
            $_SESSION['add-user'] = "Hiba történt a hozzáadás során!";
            header('location: ' . ROOT_URL . 'blog/admin/add-user.php');
            die();
        }
    }
} else {
    // Ha nem kattintottak a gombra, vissza az űrlaphoz
    header('location: ' . ROOT_URL . 'blog/admin/add-user.php');
    die();
}
