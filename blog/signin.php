<?php
require 'config/constants.php';

// Ha a bejelentkezés sikertelen, visszaadjuk az adatokat
$username_email = $_SESSION['signin-data']['username_email'] ?? '';
$password = ''; // Biztonsági okokból a jelszót soha nem töltjük vissza

?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1507. | Bejelentkezés</title>
    <link rel="icon" type="image/x-icon" href="<?= ROOT_URL ?>images/favicon.ico">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>blog/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ROOT_URL ?>blog/css/form.css?v=<?= time() ?>">
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
            <h2>Bejelentkezés</h2>
            <?php if (isset($_SESSION['signup-success'])): ?>
                <div class="alert__message success">
                    <p>
                        <?= $_SESSION['signup-success']; unset($_SESSION['signup-success']); ?>
                    </p>
                </div>
            <?php elseif (isset($_SESSION['signin'])): ?>
                <div class="alert__message error">
                    <p>
                        <?= $_SESSION['signin']; unset($_SESSION['signin']); ?>
                    </p>
                </div>
            <?php endif ?>
            <form action="<?= ROOT_URL ?>blog/signin-logic.php" method="POST">
                <?= CSRFProtection::getTokenField() ?>
                <input value="<?= htmlspecialchars($username_email, ENT_QUOTES, 'UTF-8') ?>" name="username_email" type="text" placeholder="Felhasználónév vagy Email">
                <input value="<?= htmlspecialchars($password, ENT_QUOTES, 'UTF-8') ?>" name="password" type="password" placeholder="Jelszó">
                <button name="submit" class="btn" type="submit">Bejelentkezés</button>
                <small>Nincs még fiókod? <a href="signup.php">Regisztráció</a></small>
            </form>
            <?php 
            // Bejelentkezési adatok törlése az űrlap megjelenítése után
            if (isset($_SESSION['signin-data'])) {
                unset($_SESSION['signin-data']);
            }
            ?>
        </div>
    </section>
</body>

</html>