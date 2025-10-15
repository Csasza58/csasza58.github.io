<?php
require 'config/constants.php';

// Ha a regisztráció sikertelen, visszaadjuk az adatokat
$firstname = $_SESSION['signup-data']['firstname'] ?? '';
$lastname = $_SESSION['signup-data']['lastname'] ?? '';
$username = $_SESSION['signup-data']['username'] ?? '';
$email = $_SESSION['signup-data']['email'] ?? '';
$createpassword = ''; // Biztonsági okokból jelszavakat nem töltjük vissza
$confirmpassword = '';
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1507. | Regisztráció</title>
    <link rel="icon" type="image/x-icon" href="<?= ROOT_URL ?>images/favicon.ico">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>blog/css/style.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>blog/css/form.css">
    <!-- Iconscout CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Pacifico&display=swap"
        rel="stylesheet">
</head>

<body>
    <section class="form__section">
        <div class="container form__section-container">
            <h2>Regisztráció</h2>
            <?php if (isset($_SESSION['signup'])): ?>
                <div class="alert__message error">
                    <p>
                        <?= $_SESSION['signup']; unset($_SESSION['signup']); ?>
                    </p>
                </div>
            <?php endif ?>
            <form action="<?= ROOT_URL ?>blog/signup-logic.php" enctype="multipart/form-data" method="POST">
                <input value="<?= htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8') ?>" type="text" name="firstname" placeholder="Keresztnév">
                <input value="<?= htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8') ?>" type="text" name="lastname" placeholder="Vezetéknév">
                <input value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" type="text" name="username" placeholder="Felhasználónév">
                <input value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" type="email" name="email" placeholder="Email cím">
                <input value="<?= htmlspecialchars($createpassword, ENT_QUOTES, 'UTF-8') ?>" type="password" name="createpassword" placeholder="Jelszó">
                <input value="<?= htmlspecialchars($confirmpassword, ENT_QUOTES, 'UTF-8') ?>" type="password" name="confirmpassword"
                    placeholder="Jelszó megerősítése">
                <input value="<?= htmlspecialchars($_SESSION['signup-data']['referral_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" type="text" name="referral_code" placeholder="Ajánlási kód" required>
                <div class="form__control">
                    <label for="avatar">Profilkép</label>
                    <input type="file" name="avatar" id="avatar">
                </div>
                <button class="btn" name="submit" type="submit">Regisztráció</button>
                <small>Már van fiókod? <a href="signin.php">Bejelentkezés</a></small>
            </form>
            <?php 
            // Regisztrációs adatok törlése az űrlap megjelenítése után
            if (isset($_SESSION['signup-data'])) {
                unset($_SESSION['signup-data']);
            }
            ?>
        </div>
    </section>
</body>

</html>