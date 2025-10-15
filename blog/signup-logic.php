<?php
require 'config/database.php';
require 'config/optimize-image.php';

// Get Signup form data if signup button was clicked
if (isset($_POST['submit'])) {
    // Űrlapadatok lekérése - ékezetes karakterek megőrzésével
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $createpassword = trim($_POST['createpassword']);
    $confirmpassword = trim($_POST['confirmpassword']);
    $referral_code = trim($_POST['referral_code']);
    $avatar = $_FILES['avatar'];

    // Validate input values

    // Validate input values
    if (!$firstname) {
        $_SESSION['signup'] = "Kérlek add meg a keresztnevet!";
    } elseif (!$lastname) {
        $_SESSION['signup'] = "Kérlek add meg a vezetéknevet!";
    } elseif (!$username) {
        $_SESSION['signup'] = "Kérlek add meg a felhasználónevet!";
    } elseif (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['signup'] = "Kérlek adj meg egy érvényes email címet!";
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['signup'] = "A jelszónak legalább 8 karakter hosszúnak kell lennie!";
    } elseif (!$referral_code) {
        $_SESSION['signup'] = "Kérlek add meg az ajánlási kódot!";
    } else {
        // Ajánlási kód ellenőrzése
        $code_query = "SELECT * FROM referral_codes WHERE code = ? LIMIT 1";
        $stmt = mysqli_prepare($connection, $code_query);
        mysqli_stmt_bind_param($stmt, 's', $referral_code);
        mysqli_stmt_execute($stmt);
        $code_result = mysqli_stmt_get_result($stmt);
        $code_row = mysqli_fetch_assoc($code_result);
        mysqli_stmt_close($stmt);
        if (!$code_row) {
            $_SESSION['signup'] = "Érvénytelen ajánlási kód!";
        } elseif ($code_row['is_used'] && !$code_row['never_expires']) {
            $_SESSION['signup'] = "Ez az ajánlási kód már felhasználva!";
        }
        // Check if passwords dont match
        if ($createpassword !== $confirmpassword) {
            $_SESSION['signup'] = "A jelszavak nem egyeznek!";
        } else {
            // Hash password
            $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

            // Check if username/email already exist
            $user_check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
            $stmt = mysqli_prepare($connection, $user_check_query);
            mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
            mysqli_stmt_execute($stmt);
            $user_check_result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);

            if (mysqli_num_rows($user_check_result) > 0) {
                $_SESSION['signup'] = "A felhasználónév vagy email már létezik!";
            } else {
                // PROFILKÉP OPTIMALIZÁLÁS ÉS WEBP KONVERZIÓ
                if ($avatar['name']) {
                    $avatar_tmp_name = $avatar['tmp_name'];

                    // WebP optimalizálás username alapján
                    $optimized_name = optimizeProfileImage($avatar_tmp_name, $username);

                    if ($optimized_name) {
                        $avatar_destination_path = 'images/' . $optimized_name;

                        if (move_uploaded_file($avatar_tmp_name, $avatar_destination_path)) {
                            $avatar_name = $optimized_name;
                        } else {
                            $_SESSION['signup'] = 'Profilkép mentése sikertelen.';
                        }
                    } else {
                        $_SESSION['signup'] = 'A kép optimalizálása sikertelen. Próbáld újra érvényes képformátummal (JPG, PNG).';
                    }
                } else {
                    $avatar_name = 'default-avatar.png';
                }
            }
        }
    }

    // Return if validation fails
    if (isset($_SESSION['signup'])) {
        // Ha hibás a validáció, vissza az űrlaphoz
        $_SESSION['signup-data'] = $_POST;
        header('location: ' . ROOT_URL . 'blog/signup.php');
        die();
    } else {
        // Felhasználó hozzáadása az adatbázishoz
        $insert_user_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = mysqli_prepare($connection, $insert_user_query);
        mysqli_stmt_bind_param($stmt, 'ssssss', $firstname, $lastname, $username, $email, $hashed_password, $avatar_name);
        $insert_user_result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($insert_user_result) {
            // Ha nem örökös kód, állítsuk felhasználtra
            if (!$code_row['never_expires']) {
                $update_code_query = "UPDATE referral_codes SET is_used=1 WHERE id = ?";
                $stmt = mysqli_prepare($connection, $update_code_query);
                mysqli_stmt_bind_param($stmt, 'i', $code_row['id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // Automatikus bejelentkezés - lekérjük az új felhasználó ID-ját
            $new_user_id = mysqli_insert_id($connection);
            $_SESSION['user-id'] = $new_user_id;

            // Sikeres regisztráció és automatikus bejelentkezés
            $_SESSION['signup-success'] = "Sikeres regisztráció és bejelentkezés! Üdvözöljük!";
            header('location:' . ROOT_URL . 'blog/admin/');
            die();
        }
    }
} else {
    // Ha nem kattintottak a gombra, vissza az űrlaphoz
    header('location: ' . ROOT_URL . 'blog/signup.php');
    die();
}
