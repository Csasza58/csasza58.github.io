<?php
// Markdown-like formatting: *italic*, **bold**
function simple_markdown($text)
{
    // HTML entity dekódolás
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    // Bold: **text**
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    // Italic: *text*
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
    // Színező kódok: &1-&5
    $text = preg_replace('/&1"(.*?)"&/', '<span style="color:#d4a574">$1</span>', $text);
    $text = preg_replace('/&2"(.*?)"&/', '<span style="color:#4a6b3a">$1</span>', $text);
    $text = preg_replace('/&3"(.*?)"&/', '<span style="color:#b7c2a7">$1</span>', $text);
    $text = preg_replace('/&4"(.*?)"&/', '<span style="color:#d16666">$1</span>', $text);
    $text = preg_replace('/&5"(.*?)"&/', '<span style="color:#6a7fd1">$1</span>', $text);
    // Sortörések konvertálása HTML-re
    $text = nl2br($text);
    return $text;
}
$page_title = "Admin";
include 'partials/header.php';

// Fetch current user post from database
// Aktuális felhasználó posztjainak lekérése
$current_user_id = $_SESSION['user-id'];
$query = "  SELECT posts.id, posts.title, categories.title AS category_title
            , posts.body, posts.is_featured
            FROM posts
            INNER JOIN categories
            ON posts.category_id = categories.id 
            WHERE author_id = $current_user_id 
            ORDER BY posts.id DESC";
$posts = mysqli_query($connection, $query);
?>

<section class="dashboard">
    <button id="sidebar-toggle-btn" class="sidebar__toggle">
        <i class="uil uil-arrow-right"></i>
    </button>
    <div>
        <?php
        // Fetch nickname/username
        if (isset($_SESSION['user-id'])) {
            $user_id = $_SESSION['user-id'];
            $user_query = "SELECT username FROM users WHERE id=$user_id";
            $user_result = mysqli_query($connection, $user_query);
            $user = mysqli_fetch_assoc($user_result);
            if ($user && isset($user['username'])) {
                echo '<div class="admin-greeting-card" style="max-width:400px;margin:4rem auto 2.5rem auto;padding:1.2rem 2.2rem;background:linear-gradient(135deg,#e2e8f0 0%,#b7c2a7 60%,#d4a574 100%);border-radius:1.2rem;box-shadow:0 2px 12px rgba(45,74,34,0.08);text-align:center;font-size:1.35rem;font-weight:600;color:#3a4a35;letter-spacing:0.5px;">Szia, <span style="color:#4a6b3a;">' . htmlspecialchars($user['username']) . '</span>!</div>';
            }
        }
        ?>
        <?php if (isset($_SESSION['add-post-success'])): ?>
            <div class="alert__message success container">
                <p>
                    <?= $_SESSION['add-post-success'];
                    unset($_SESSION['add-post-success']); ?>
                </p>
            </div>
        <?php elseif (isset($_SESSION['edit-post-success'])): ?>
            <div class="alert__message success container">
                <p>
                    <?= $_SESSION['edit-post-success'];
                    unset($_SESSION['edit-post-success']); ?>
                </p>
            </div>
        <?php elseif (isset($_SESSION['edit-post'])): ?>
            <div class="alert__message error container">
                <p>
                    <?= $_SESSION['edit-post'];
                    unset($_SESSION['edit-post']); ?>
                </p>
            </div>
        <?php elseif (isset($_SESSION['delete-post'])): ?>
            <div class="alert__message error container">
                <p>
                    <?= $_SESSION['delete-post'];
                    unset($_SESSION['delete-post']); ?>
                </p>
            </div>
        <?php elseif (isset($_SESSION['delete-post-success'])): ?>
            <div class="alert__message success container">
                <p>
                    <?= $_SESSION['delete-post-success'];
                    unset($_SESSION['delete-post-success']); ?>
                </p>
            </div>
        <?php endif ?>
    </div>
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
                    <a href="index.php" class="active">
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
                        <a href="manage-referral-codes.php">
                            <i class="uil uil-key-skeleton"></i>
                            <h5>Ajánlási kódok</h5>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </aside>
        <main>
            <h2>Posztok kezelése</h2>
            <?php if (mysqli_num_rows($posts) > 0): ?>
                <!-- Desktop táblázat -->
                <div class="table-wrapper">
                    <table class="desktop-table">
                        <thead>
                            <tr>
                                <th>Cím</th>
                                <th>Kategória</th>
                                <th>Státusz</th>
                                <th>Szerkesztés</th>
                                <th>Törlés</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                                <tr>
                                    <td>
                                        <?= $post['title'] ?>
                                        <?php if ($post['is_featured'] > 0): ?>
                                            <span style="display:inline-block;background:linear-gradient(45deg,#d4a574,#b8935f);color:white;padding:3px 8px;border-radius:10px;font-size:0.75rem;font-weight:600;margin-left:0.5rem;">⭐ KIEMELT</span>
                                        <?php endif ?>
                                    </td>
                                    <td><?= $post['category_title'] ?></td>
                                    <td>
                                        <?php if ($post['is_featured'] > 0): ?>
                                            <span style="color:#8b5a2b;font-weight:700;font-size:0.95rem;">⭐ Kiemelt</span>
                                        <?php else: ?>
                                            <span style="color:#2d4a22;font-weight:500;font-size:0.95rem;">📄 Normál</span>
                                        <?php endif ?>
                                    </td>
                                    <td><a href="<?= ROOT_URL ?>blog/admin/edit-post.php?id=<?= $post['id'] ?>" class="btn sm">Szerkesztés</a>
                                    </td>
                                    <td><a href="<?= ROOT_URL ?>blog/admin/delete-post.php?id=<?= $post['id'] ?>"
                                            class="btn sm danger">Törlés</a></td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobil kártyás layout -->
                <div class="mobile-cards">
                    <?php
                    // Reset a query eredményét
                    mysqli_data_seek($posts, 0);
                    while ($post = mysqli_fetch_assoc($posts)): ?>
                        <div class="post-card">
                            <div class="card-header">
                                <h3><?= $post['title'] ?></h3>
                                <?php if ($post['is_featured'] > 0): ?>
                                    <span class="featured-badge">⭐ KIEMELT</span>
                                <?php endif ?>
                            </div>
                            <div class="card-info">
                                <span class="category">📂 <?= $post['category_title'] ?></span>
                                <span class="status">
                                    <?php if ($post['is_featured'] > 0): ?>
                                        <span style="color:#8b5a2b;">⭐ Kiemelt</span>
                                    <?php else: ?>
                                        <span style="color:#2d4a22;">📄 Normál</span>
                                    <?php endif ?>
                                </span>
                            </div>
                            <div class="card-actions">
                                <a href="<?= ROOT_URL ?>blog/admin/edit-post.php?id=<?= $post['id'] ?>" class="btn sm">Szerkesztés</a>
                                <a href="<?= ROOT_URL ?>blog/admin/delete-post.php?id=<?= $post['id'] ?>" class="btn sm danger">Törlés</a>
                            </div>
                        </div>
                    <?php endwhile ?>
                </div>
            <?php else: ?>
                <div class="alert__message error">
                    <p><?= "Nincsenek posztok" ?></p>
                </div>
            <?php endif ?>
        </main>
    </div>
</section>

<?php
include '../partials/footer.php';
?>