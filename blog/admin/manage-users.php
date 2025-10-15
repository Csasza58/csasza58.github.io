<?php
$page_title = "Felhasználók kezelése";
include 'partials/header.php';

// Fetch users from database except user
$current_admin_id = $_SESSION['user-id'];

$query = "SELECT * FROM users WHERE NOT id=$current_admin_id";
$users = mysqli_query($connection, $query);
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
        <?php if(isset($_SESSION['add-user-success'])) : ?>
            <div class="alert__message success container">
                <p><?= $_SESSION['add-user-success']; unset($_SESSION['add-user-success']);?></p>
            </div>
        <?php elseif(isset($_SESSION['edit-user-success'])) : ?>
            <div class="alert__message success container">
                <p><?= $_SESSION['edit-user-success']; unset($_SESSION['edit-user-success']);?></p>
            </div>
        <?php elseif(isset($_SESSION['edit-user'])) : ?>
            <div class="alert__message error container">
                <p><?= $_SESSION['edit-user']; unset($_SESSION['edit-user']);?></p>
            </div>
        <?php elseif(isset($_SESSION['delete-user'])) : ?>
            <div class="alert__message error container">
                <p><?= $_SESSION['delete-user']; unset($_SESSION['delete-user']);?></p>
            </div>
        <?php elseif(isset($_SESSION['delete-user-success'])) : ?>
            <div class="alert__message success container">
                <p><?= $_SESSION['delete-user-success']; unset($_SESSION['delete-user-success']);?></p>
            </div>
        <?php endif?>
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
                        <a href="manage-users.php" class="active">
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
            <h2>Felhasználók kezelése</h2>
            <?php if(mysqli_num_rows($users) > 0) :?>
            <!-- Desktop táblázat -->
            <div class="table-wrapper">
            <table class="desktop-table">
                <thead>
                    <tr>
                        <th>Név</th>
                        <th>Felhasználónév</th>
                        <th>Szerkesztés</th>
                        <th>Törlés</th>
                        <th>Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($users)) : ?>
                    <tr>
                        <td><?= "{$user['firstname']} {$user['lastname']}" ?></td>
                        <td><?= "{$user['username']}" ?></td>
                        <td><a href="<?= ROOT_URL ?>blog/admin/edit-user.php?id=<?= $user['id']?>" class="btn sm">Szerkesztés</a></td>
                        <td><a href="<?= ROOT_URL ?>blog/admin/delete-user.php?id=<?= $user['id']?>" class="btn sm danger">Törlés</a></td>
                        <td><?= $user['is_admin'] ? 'Igen': 'Nem' ?></td>
                    </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
            </div>
            
            <!-- Mobil kártyás layout -->
            <div class="mobile-cards">
                <?php 
                // Reset a query eredményét
                mysqli_data_seek($users, 0);
                while ($user = mysqli_fetch_assoc($users)): ?>
                    <div class="user-card">
                        <div class="card-header">
                            <h3><?= "{$user['firstname']} {$user['lastname']}" ?></h3>
                            <?php if ($user['is_admin']): ?>
                                <span class="admin-badge">👑 Admin</span>
                            <?php endif ?>
                        </div>
                        <div class="card-info">
                            <span class="username">👤 <?= $user['username'] ?></span>
                            <span class="status">
                                <?php if ($user['is_admin']): ?>
                                    <span style="color:#d4a574;font-weight:600;">👑 Admin</span>
                                <?php else: ?>
                                    <span style="color:#4a6b3a;font-weight:500;">👤 Felhasználó</span>
                                <?php endif ?>
                            </span>
                        </div>
                        <div class="card-actions">
                            <a href="<?= ROOT_URL ?>blog/admin/edit-user.php?id=<?= $user['id']?>" class="btn sm">Szerkesztés</a>
                            <a href="<?= ROOT_URL ?>blog/admin/delete-user.php?id=<?= $user['id']?>" class="btn sm danger">Törlés</a>
                        </div>
                    </div>
                <?php endwhile ?>
            </div>
            <?php else :?>
            <div class="alert__message error">
                <p><?= "Nincsenek felhasználók"?></p>
            </div>
            <?php endif?>
        </main>
    </div>
</section>

<?php
include '../partials/footer.php';
?>
