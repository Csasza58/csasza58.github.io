<?php
require __DIR__ . '/../config/database.php';

// Aktuális felhasználó lekérése az adatbázisból
if (isset($_SESSION['user-id'])) {
    $id = filter_var($_SESSION['user-id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT avatar FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $avatar = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= isset($page_title) ? htmlspecialchars("1507. | " . $page_title) : "1507. | ÁSzMCsCs" ?></title>
    <link rel="icon" type="image/x-icon" href="<?= ROOT_URL ?>images/favicon.ico">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>blog/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ROOT_URL ?>blog/css/header.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ROOT_URL ?>blog/css/footer.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Pacifico&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- Iconscout CDN -->
    <!-- Google Fonts already included above -->
    <script src="<?= ROOT_URL ?>blog/js/main.js" defer></script>
    <script>
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            const hamburger = document.querySelector('.hamburger');

            if (nav.classList.contains('open')) {
                nav.classList.remove('open');
                hamburger.classList.remove('active');
            } else {
                nav.classList.add('open');
                hamburger.classList.add('active');
            }
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('main-nav');
            const hamburger = document.querySelector('.hamburger');

            if (nav && nav.classList.contains('open') &&
                !nav.contains(event.target) &&
                !hamburger.contains(event.target)) {
                nav.classList.remove('open');
                hamburger.classList.remove('active');
            }
        });
    </script>
</head>

<body>
    <header>
        <div class="logo">
            <a href="<?= ROOT_URL ?>">
                <img src="<?= ROOT_URL ?>blog/images/liliom.svg" alt="Cserkészliliom logó" class="svg-logo" />
                <span class="logo-text">
                    1507. Árpádházi<br>Szent Margit <br>Cserkészcsapat
                </span>
                <span class="logo-short-text" style="display:none;">
                    ÁSzMCsCs
                </span>
            </a>
        </div>
        <button type="button" class="hamburger" aria-label="Menü megnyitása" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <nav class="main-nav" id="main-nav">
            <a href="<?= ROOT_URL ?>" class="nav-link">Főoldal</a>
            <a href="<?= ROOT_URL ?>#about" class="nav-link">Rólunk</a>
            <a href="<?= ROOT_URL ?>kapcsolat/kapcsolat.html" class="nav-link">Kapcsolat</a>
            <a href="<?= ROOT_URL ?>blog/" class="nav-link">Blog</a>
            <?php if (isset($_SESSION['user-id'])): ?>
                <a href="<?= ROOT_URL ?>blog/admin/" class="nav-link">Dashboard</a>
                <a href="<?= ROOT_URL ?>blog/logout.php" class="nav-link">Kilépés</a>
            <?php else: ?>
                <a href="<?= ROOT_URL ?>blog/signin.php" class="nav-link">Belépés</a>
            <?php endif; ?>
        </nav>
    </header>
    <!-- END OF NAV -->