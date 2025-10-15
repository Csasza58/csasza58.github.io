<?php
// production: errors not forcibly displayed

$page_title = "Profil szerkesztése";
include 'partials/header.php';
require 'config/database.php';
require_once '../config/optimize-image.php';

// A fejléc már tartalmazza a fontokat és a style.css-t, így a betűtípus egységes lesz minden admin oldalon.

// Aktuális felhasználó adatai
$user_id = $_SESSION['user-id'];
$query = "SELECT * FROM users WHERE id=$user_id";
$result = mysqli_query($connection, $query);
$user = mysqli_fetch_assoc($result);

// Hibák és sikeres üzenet
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $avatar = $_FILES['avatar'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validáció
    if (!$firstname || !$lastname || !$username || !$email) {
        $error = 'Minden mező kitöltése kötelező!';
    }

    // Jelszó módosítás validáció
    if (!$error && ($new_password || $confirm_password)) {
        if (!$current_password) {
            $error = 'A jelszó módosításához add meg a jelenlegi jelszavad!';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Az új jelszavak nem egyeznek!';
        } elseif (strlen($new_password) < 8) {
            $error = 'Az új jelszónak legalább 8 karakter hosszúnak kell lennie!';
        } else {
            // Ellenőrizzük a jelenlegi jelszót
            $user_query = "SELECT password FROM users WHERE id=$user_id";
            $user_result = mysqli_query($connection, $user_query);
            $user_row = mysqli_fetch_assoc($user_result);
            if (!password_verify($current_password, $user_row['password'])) {
                $error = 'A jelenlegi jelszó hibás!';
            }
        }
    }

    // Avatar kezelése
    $avatar_name = $user['avatar']; // alapértelmezetten a meglévő avatar
    if (!$error && $avatar['name']) {
        $allowed_files = ['png', 'jpg', 'jpeg', 'webp'];
        $extension = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_files)) {
            $error = 'A fájl típusa csak png, jpg, jpeg vagy webp lehet!';
        } else {
            // OPTIMALIZÁLÁS ÉS WEBP KONVERZIÓ
            $avatar_tmp_name = $avatar['tmp_name'];
            $new_avatar_name = optimizeProfileImage($avatar_tmp_name, $username);

            if ($new_avatar_name) {
                $avatar_destination_path = '../images/' . $new_avatar_name;

                // FÁJL MOZGATÁSA (az optimalizált WebP a tmp_name helyen van)
                if (move_uploaded_file($avatar_tmp_name, $avatar_destination_path)) {
                    // Sikeres mentés
                    $avatar_name = $new_avatar_name;

                    // Töröljük a régi avatart
                    if ($user['avatar'] && $user['avatar'] !== 'default-avatar.png') {
                        $old_avatar_path = '../images/' . $user['avatar'];
                        if (file_exists($old_avatar_path)) {
                            unlink($old_avatar_path);
                        }
                    }
                } else {
                    $error = 'A kép mentése sikertelen! Jogosultság hiba.';
                }
            } else {
                $error = 'Kép optimalizálás sikertelen!';
            }
        }
    }

    // Mentés az adatbázisba
    if (!$error) {
        if ($new_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET firstname='$firstname', lastname='$lastname', username='$username', email='$email', avatar='$avatar_name', password='$hashed' WHERE id=$user_id";
        } else {
            $update_query = "UPDATE users SET firstname='$firstname', lastname='$lastname', username='$username', email='$email', avatar='$avatar_name' WHERE id=$user_id";
        }

        if (mysqli_query($connection, $update_query)) {
            $success = 'Profil sikeresen frissítve!';
            if ($new_password) $success .= ' Jelszó módosítva.';
            $_SESSION['avatar'] = $avatar_name;
            $user = array_merge($user, [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $username,
                'email' => $email,
                'avatar' => $avatar_name
            ]);
        } else {
            $error = 'Hiba történt a frissítés során.';
        }
    }
}
?>

<?php
// debug removed

?>

<section class="dashboard">
    <div>
        <?php
        // Greeting card mint az index.php-n
        if (isset($_SESSION['user-id'])) {
            $user_id = $_SESSION['user-id'];
            $user_query = "SELECT username FROM users WHERE id=$user_id";
            $user_result = mysqli_query($connection, $user_query);
            $user_greet = mysqli_fetch_assoc($user_result);
            if ($user_greet && isset($user_greet['username'])) {
                echo '<div class="admin-greeting-card" style="max-width:400px;margin:2rem auto 2.5rem auto;padding:1.2rem 2.2rem;background:linear-gradient(135deg,#e2e8f0 0%,#b7c2a7 60%,#d4a574 100%);border-radius:1.2rem;box-shadow:0 2px 12px rgba(45,74,34,0.08);text-align:center;font-size:1.35rem;font-weight:600;color:#3a4a35;letter-spacing:0.5px;">Szia, <span style="color:#4a6b3a;">' . htmlspecialchars($user_greet['username']) . '</span>!</div>';
            }
        }
        ?>
        <?php if ($error): ?>
            <div class="alert__message error container">
                <p><?= $error ?></p>
            </div>
        <?php elseif ($success): ?>
            <div class="alert__message success container">
                <p><?= $success ?></p>
            </div>
        <?php endif; ?>
    </div>
    <button id="sidebar-toggle-btn" class="sidebar__toggle">
        <i class="uil uil-arrow-right"></i>
    </button>
    <div class="container dashboard__container">
        <aside>
            <ul>
                <li>
                    <a href="add-post.php">
                        <i class="uil uil-pen"></i>
                        <h5>Új poszt</h5>
                    </a>
                </li>
                <li>
                    <a href="index.php">
                        <i class="uil uil-postcard"></i>
                        <h5>Posztok kezelése</h5>
                    </a>
                </li>
                <?php if (isset($_SESSION['user-id'])): ?>
                    <li>
                        <a href="profile-edit.php" class="active">
                            <i class="uil uil-user"></i>
                            <h5>Profil szerkesztése</h5>
                        </a>
                    </li>
                <?php endif ?>
                <?php if (isset($_SESSION['user_is_admin'])): ?>
                    <li>
                        <a href="manage-users.php">
                            <i class="uil uil-users-alt"></i>
                            <h5>Felhasználók kezelése</h5>
                        </a>
                    </li>
                    <li>
                        <a href="add-user.php">
                            <i class="uil uil-user-plus"></i>
                            <h5>Felhasználó hozzáadása</h5>
                        </a>
                    </li>
                <?php endif ?>
                <?php if (isset($_SESSION['user-id'])): ?>
                    <li>
                        <a href="manage-categories.php">
                            <i class="uil uil-list-ul"></i>
                            <h5>Kategóriák kezelése</h5>
                        </a>
                    </li>
                    <li>
                        <a href="add-category.php">
                            <i class="uil uil-edit"></i>
                            <h5>Kategória hozzáadása</h5>
                        </a>
                    </li>
                <?php endif ?>
                <?php if (isset($_SESSION['user_is_admin'])): ?>
                    <li>
                        <a href="manage-referral-codes.php">
                            <i class="uil uil-key-skeleton"></i>
                            <h5>Ajánlási kódok</h5>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </aside>
        <main>
            <h2>Profil szerkesztése</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="firstname">Keresztnév</label>
                <input type="text" name="firstname" id="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                <label for="lastname">Vezetéknév</label>
                <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>
                <label for="username">Felhasználónév</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                <label for="avatar">Profilkép (opcionális)</label>
                <input type="file" name="avatar" id="avatar" accept="image/png, image/jpg, image/jpeg, image/webp">
                <?php if ($user['avatar']): ?>
                    <div style="margin:1rem 0;"><img src="<?= ROOT_URL ?>blog/images/<?= htmlspecialchars($user['avatar']) ?>" alt="Profilkép" style="max-width:120px;border-radius:8px;"></div>
                <?php endif; ?>
                <hr style="margin:2rem 0;">
                <h3>Jelszó módosítása</h3>
                <label for="current_password">Jelenlegi jelszó</label>
                <input type="password" name="current_password" id="current_password" autocomplete="current-password">
                <label for="new_password">Új jelszó</label>
                <input type="password" name="new_password" id="new_password" autocomplete="new-password">
                <label for="confirm_password">Új jelszó megerősítése</label>
                <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password">
                <button type="submit" class="btn">Mentés</button>
            </form>
        </main>
    </div>
</section>

<?php include '../partials/footer.php'; ?>