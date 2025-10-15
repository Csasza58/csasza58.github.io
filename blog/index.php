<?php
// Markdown-like formatting: *italic*, **bold**
function simple_markdown($text)
{
    // HTML entity dekódolás
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    // **félkövér**, *dőlt* szöveg
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
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

// Biztonságos előnézet készítő - levágja a szöveget, de megtartja a HTML struktúrát
function safe_excerpt($text, $length = 300) {
    // Először csak a szöveget vágjuk le, formázás nélkül
    $plain_excerpt = substr($text, 0, $length);
    // Majd alkalmazzuk a markdown formázást
    return simple_markdown($plain_excerpt);
}

// Date formatting function
function format_date($date_time)
{
    date_default_timezone_set('Europe/Budapest');
    $months = [1 => 'jan.', 2 => 'febr.', 3 => 'márc.', 4 => 'ápr.', 5 => 'máj.', 6 => 'jún.', 7 => 'júl.', 8 => 'aug.', 9 => 'szept.', 10 => 'okt.', 11 => 'nov.', 12 => 'dec.'];
    $dt = strtotime($date_time);
    $year = date('Y', $dt);
    $month = $months[(int) date('n', $dt)];
    $day = date('d', $dt);
    $time = date('H:i', $dt);
    $result = "$year. $month $day. - $time";
    $hoursAgo = floor((time() - $dt) / 3600);
    if ($hoursAgo <= 24) {
        $result .= " ($hoursAgo órája)";
    }
    return $result;
}
require 'config/database.php';
$page_title = "Főoldal";
include 'partials/header.php';

// Fetch featured posts from database (maximum 3)
$featured_query = " SELECT 
                    posts.id,
                    posts.title,
                    posts.body,
                    posts.date_time,
                    posts.thumbnail,
                    posts.is_featured,
                    categories.title AS category_title,
                    categories.id AS category_id,
                    users.avatar,
                    users.firstname,
                    users.lastname
                    FROM posts
                    INNER JOIN categories ON posts.category_id = categories.id
                    INNER JOIN users ON posts.author_id = users.id
                    WHERE posts.is_featured IN (1, 2, 3)
                    ORDER BY posts.is_featured ASC";
$featured_result = mysqli_query($connection, $featured_query);
$featured_posts = [];
while ($post = mysqli_fetch_assoc($featured_result)) {
    $featured_posts[] = $post;
}

// Fetch latest 6 post from database
$posts_query = "  SELECT 
            posts.id,
            posts.title,
            posts.body,
            posts.date_time,
            posts.thumbnail,
            categories.title AS category_title,
            categories.id AS category_id,
            users.avatar,
            users.firstname,
            users.lastname
            FROM posts
            INNER JOIN categories ON posts.category_id = categories.id
            INNER JOIN users ON posts.author_id = users.id
            ORDER BY date_time DESC LIMIT 6";
$posts = mysqli_query($connection, $posts_query);

// Fetch categories from database
$categories_query = "SELECT * FROM categories ORDER BY title";
$categories = mysqli_query($connection, $categories_query);
?>

<?php if (count($featured_posts) > 0): ?>
    <section class="featured">
        <div class="container feature__container">
            <div class="featured-carousel" data-carousel-count="<?= count($featured_posts) ?>">
                <?php foreach ($featured_posts as $index => $featured): ?>
                    <div class="featured-slide <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>">
                        <div class="post__thumbnail">
                            <img src="<?= ROOT_URL ?>blog/images/<?= $featured['thumbnail'] ?>">
                        </div>
                        <div class="post__info">
                            <a href="<?= ROOT_URL ?>blog/category-posts.php?id=<?= $featured['category_id'] ?>"
                                class="category__button"><?= htmlspecialchars($featured['category_title']) ?></a>
                            <h2 class="post__title">
                                <a href="<?= ROOT_URL ?>blog/post.php?id=<?= $featured['id'] ?>"><?= htmlspecialchars($featured['title']) ?></a>
                            </h2>
                            <p class="post__body">
                                <?= safe_excerpt($featured['body'], 300) ?>...
                            </p>
                            <div class="post__author">
                                <div class="post__author-avatar">
                                    <img src="<?= ROOT_URL ?>blog/images/<?= !empty($featured['avatar']) ? $featured['avatar'] : 'default-avatar.png' ?>">
                                </div>
                                <div class="post__author-info">
                                    <h5>Írta: <?= htmlspecialchars($featured['firstname']) ?> <?= htmlspecialchars($featured['lastname']) ?></h5>
                                    <small><?= format_date($featured['date_time']) ?></small>
                                    <span class="featured-badge">Kiemelt #<?= $featured['is_featured'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            
            <?php if (count($featured_posts) > 1): ?>
                <div class="carousel-controls">
                    <?php for ($i = 0; $i < count($featured_posts); $i++): ?>
                        <button class="carousel-dot <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"></button>
                    <?php endfor ?>
                </div>
            <?php endif ?>
        </div>
    </section>
    <!-- END OF FEATURED -->
<?php endif ?>

<section class="posts">
    <div class="container posts__container">
        <?php while ($post = mysqli_fetch_assoc($posts)): ?>
            <article class="post">
                <a href="<?= ROOT_URL ?>blog/post.php?id=<?= $post['id'] ?>"
                    style="display:block;text-decoration:none;color:inherit;">
                    <div class="post__thumbnail">
                        <img src="<?= ROOT_URL ?>blog/images/<?= $post['thumbnail'] ?>">
                    </div>
                    <div class="post__info">
                        <div class="post__main">
                            <a href="<?= ROOT_URL ?>blog/category-posts.php?id=<?= $post['category_id'] ?>" class="category__button"
                                onclick="event.stopPropagation();"><?= htmlspecialchars($post['category_title']) ?></a>
                            <h3 class="post__title">
                                <?= htmlspecialchars($post['title']) ?>
                            </h3>
                            <p class="post__body">
                                <?= safe_excerpt($post['body'], 300) ?>...
                            </p>
                            <div class="post__author">
                                <div class="post__author-avatar">
                                    <img src="<?= ROOT_URL ?>blog/images/<?= !empty($post['avatar']) ? $post['avatar'] : 'default-avatar.png' ?>">
                                </div>
                                <div class="post__author-info">
                                    <h5>Írta: <?= htmlspecialchars($post['firstname']) ?> <?= htmlspecialchars($post['lastname']) ?></h5>
                                    <small><?= format_date($post['date_time']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </article>
        <?php endwhile ?>
    </div>
</section>
<!-- END OF POSTS -->

<section class="category__buttons">
    <div class="container category__buttons-container">
        <?php if (mysqli_num_rows($categories) > 0): ?>
            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <a href="<?= ROOT_URL ?>blog/category-posts.php?id=<?= $category['id'] ?>"
                    class="category__button"><?= htmlspecialchars($category['title']) ?></a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert__message error">
                <p>Nincsenek kategóriák</p>
            </div>
        <?php endif ?>
    </div>
</section>
<!-- END OF CATEGORY BUTTONS-->

<?php
include 'partials/footer.php'
    ?>