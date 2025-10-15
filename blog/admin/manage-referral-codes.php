
<?php
$page_title = "Meghívókódok kezelése";
include 'partials/header.php';
require 'config/database.php';

// Kód törlése
if (isset($_POST['delete_code']) && isset($_POST['code_id'])) {
    $code_id = intval($_POST['code_id']);
    mysqli_query($connection, "DELETE FROM referral_codes WHERE id=$code_id");
    $_SESSION['referral_code_success'] = "Kód sikeresen eltávolítva.";
    header('Location: manage-referral-codes.php');
    die();
}

// Kód generálása
if (isset($_POST['generate_code'])) {
    $never_expires = isset($_POST['never_expires']) ? 1 : 0;
    $code = bin2hex(random_bytes(8));
    $creator = $_SESSION['user-id'] ?? 0;
    $insert = "INSERT INTO referral_codes (code, is_used, never_expires, created_at, creator_id) VALUES ('$code', 0, $never_expires, NOW(), $creator)";
    mysqli_query($connection, $insert);
    $_SESSION['referral_code_success'] = "Új kód sikeresen generálva: $code";
    header('Location: manage-referral-codes.php');
    die();
}

// Kódok lekérése
$codes = mysqli_query($connection, "SELECT rc.*, u.username AS creator_name FROM referral_codes rc LEFT JOIN users u ON rc.creator_id = u.id ORDER BY rc.created_at DESC");
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
                echo '<div class="admin-greeting-card" style="max-width:400px;margin:4rem auto 2.5rem auto;padding:1.2rem 2.2rem;background:linear-gradient(135deg,#e2e8f0 0%,#b7c2a7 60%,#d4a574 100%);border-radius:1.2rem;box-shadow:0 2px 12px rgba(45,74,34,0.08);text-align:center;font-size:1.35rem;font-weight:600;color:#3a4a35;letter-spacing:0.5px;">Szia, <span style="color:#4a6b3a;">' . htmlspecialchars($user_greet['username']) . '</span>!</div>';
            }
        }
        ?>
        <?php if (isset($_SESSION['referral_code_success'])): ?>
            <div class="alert__message success container">
                <p><?= $_SESSION['referral_code_success']; unset($_SESSION['referral_code_success']); ?></p>
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
                        <a href="profile-edit.php">
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
                        <a href="manage-referral-codes.php" class="active">
                            <i class="uil uil-key-skeleton"></i>
                            <h5>Ajánlási kódok</h5>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </aside>
        <main>
            <h2>Ajánlási kódok kezelése</h2>
            
            <!-- Desktop táblázat -->
            <table class="referral-codes-table">
                <thead>
                    <tr>
                        <th>Kód</th>
                        <th>Felhasználva</th>
                        <th>Végtelen?</th>
                        <th>Létrehozó</th>
                        <th>Létrehozva</th>
                        <th>Törlés</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($codes)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['code']) ?></td>
                            <td><?php if ($row['never_expires']) { echo '—'; } else { echo $row['is_used'] ? 'Igen' : 'Nem'; } ?></td>
                            <td><?= $row['never_expires'] ? 'Igen' : 'Nem' ?></td>
                            <td><?= $row['creator_name'] ?? '-' ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Biztosan törölni szeretnéd ezt a kódot?');" style="display:inline;">
                                    <input type="hidden" name="code_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="delete_code" style="background:#c0392b;color:#fff;border:none;padding:0.3rem 0.7rem;border-radius:0.4rem;cursor:pointer;font-size:0.95rem;">Törlés</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Mobil kártyák -->
            <div class="referral-cards">
                <?php
                // Újra lekérjük az adatokat a kártyákhoz
                mysqli_data_seek($codes, 0);
                while ($row = mysqli_fetch_assoc($codes)):
                ?>
                    <div class="referral-card">
                        <div class="referral-card-header">
                            <div class="referral-card-code"><?= htmlspecialchars($row['code']) ?></div>
                        </div>
                        <div class="referral-card-info">
                            <div class="referral-card-info-item">
                                <span class="referral-card-info-label">Felhasználva</span>
                                <span class="referral-card-info-value"><?php if ($row['never_expires']) { echo '—'; } else { echo $row['is_used'] ? 'Igen' : 'Nem'; } ?></span>
                            </div>
                            <div class="referral-card-info-item">
                                <span class="referral-card-info-label">Végtelen?</span>
                                <span class="referral-card-info-value"><?= $row['never_expires'] ? 'Igen' : 'Nem' ?></span>
                            </div>
                            <div class="referral-card-info-item">
                                <span class="referral-card-info-label">Létrehozó</span>
                                <span class="referral-card-info-value"><?= $row['creator_name'] ?? '-' ?></span>
                            </div>
                            <div class="referral-card-info-item">
                                <span class="referral-card-info-label">Létrehozva</span>
                                <span class="referral-card-info-value"><?= $row['created_at'] ?></span>
                            </div>
                        </div>
                        <div class="referral-card-actions">
                            <form method="POST" onsubmit="return confirm('Biztosan törölni szeretnéd ezt a kódot?');">
                                <input type="hidden" name="code_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_code" class="referral-card-delete-btn">Törlés</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <hr style="margin:2rem 0;">
            
            <!-- Desktop generálás -->
            <form method="POST" style="margin-bottom:2rem;" class="desktop-generate-form">
                <div style="display:flex;align-items:center;gap:0.7rem;margin-bottom:1rem;">
                    <input type="checkbox" name="never_expires" id="never_expires" style="width:1.2rem;height:1.2rem;">
                    <label for="never_expires" style="font-size:1rem;cursor:pointer;">Végtelen felhasználású legyen?</label>
                </div>
                <button type="submit" name="generate_code" class="btn">Kód generálása</button>
            </form>
            
            <!-- Mobil generálás kártya -->
            <div class="generate-card">
                <h3>Új kód generálása</h3>
                <form method="POST">
                    <div class="generate-card-checkbox">
                        <input type="checkbox" name="never_expires" id="never_expires_mobile">
                        <label for="never_expires_mobile">Végtelen felhasználású legyen?</label>
                    </div>
                    <button type="submit" name="generate_code" class="btn">Kód generálása</button>
                </form>
            </div>
            </form>
        </main>
    </div>
</section>

<?php include '../partials/footer.php'; ?>
