<?php
$page_title = "Kategóriák kezelése";
include 'partials/header.php';

// Fetch categories from database
// Kategóriák lekérése az adatbázisból
$query = "SELECT * FROM categories ORDER BY title";
$categories = mysqli_query($connection, $query);
?>

<section class="dashboard">
    <div>
        <?php
        // Greeting card
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
        <?php if(isset($_SESSION['add-category-success'])) : ?>
            <div class="alert__message success container">
                <p>
                    <?= $_SESSION['add-category-success'];
                    unset($_SESSION['add-category-success']);?>
                </p>
            </div>
        <?php elseif(isset($_SESSION['edit-category-success'])) : ?>
            <div class="alert__message success container">
                <p>
                    <?= $_SESSION['edit-category-success'];
                    unset($_SESSION['edit-category-success']);?>
                </p>
            </div>
        <?php elseif(isset($_SESSION['edit-category'])) : ?>
            <div class="alert__message error container">
                <p>
                    <?= $_SESSION['edit-category'];
                    unset($_SESSION['edit-category']);?>
                </p>
            </div>
        <?php elseif(isset($_SESSION['delete-category'])) : ?>
            <div class="alert__message error container">
                <p>
                    <?= $_SESSION['delete-category'];
                    unset($_SESSION['delete-category']);?>
                </p>
            </div>
        <?php elseif(isset($_SESSION['delete-category-success'])) : ?>
            <div class="alert__message success container">
                <p>
                    <?= $_SESSION['delete-category-success'];
                    unset($_SESSION['delete-category-success']);?>
                </p>
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
                        <a href="manage-categories.php" class="active">
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
            <h2>Kategóriák kezelése</h2>
            <?php if(mysqli_num_rows($categories) > 0) :?>
            <!-- Desktop táblázat -->
            <div class="table-wrapper">
            <table class="desktop-table">
                <thead>
                    <tr>
                        <th>Név</th>
                        <th>Szerkesztés</th>
                        <th>Törlés</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($category = mysqli_fetch_assoc($categories)) : ?>
                    <tr>
                        <td><?=$category['title']?></td>
                        <td><a href="edit-category.php?id=<?=$category['id']?>" class="btn sm">Szerkesztés</a></td>
                        <?php if(isset($_SESSION['user_is_admin'])): ?>
                            <td><a href="delete-category.php?id=<?=$category['id']?>" class="btn sm danger">Törlés</a></td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
            
            <!-- Mobil kártyás layout -->
            <div class="mobile-cards">
                <?php 
                // Reset a query eredményét
                mysqli_data_seek($categories, 0);
                while ($category = mysqli_fetch_assoc($categories)): ?>
                    <div class="category-card">
                        <div class="card-header">
                            <h3><?= $category['title'] ?></h3>
                        </div>
                        <div class="card-actions">
                            <a href="edit-category.php?id=<?=$category['id']?>" class="btn sm">Szerkesztés</a>
                            <?php if(isset($_SESSION['user_is_admin'])): ?>
                                <a href="delete-category.php?id=<?=$category['id']?>" class="btn sm danger">Törlés</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php else :?>
            <div class="alert__message error">
                <p><?= "Nincsenek kategóriák"?></p>
            </div>
            <?php endif?>
        </main>
    </div>
</section>

<?php
include '../partials/footer.php';
?>
    
