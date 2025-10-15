<?php
$page_title = "Keresés";
include 'partials/header.php';

// Kategóriák lekérése
$categories_query = "SELECT * FROM categories ORDER BY title";
$categories = mysqli_query($connection, $categories_query);

if (isset($_GET['search']) && isset($_GET['submit'])) {
    $search = filter_var($_GET['search'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $query = "  SELECT
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
                WHERE posts.title LIKE '%$search%'
                ORDER BY date_time DESC";
    $posts = mysqli_query($connection, $query);
} else {
    header('location: ' . ROOT_URL . 'blog.php');
    die();
}
?>

<?php if (mysqli_num_rows($posts) > 0): ?>
    <section class="posts section__extra-margin">
        <div class="container posts__container">
            <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                <article class="post">
                    <div class="post__thumbnail">
                        <img src="<?= ROOT_URL ?>blog/images/<?= $post['thumbnail'] ?>">
                    </div>
                    <div class="post__info">
                        <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $post['category_id'] ?>"
                            class="category__button"><?= $post['category_title'] ?></a>
                        <h3 class="post__title">
                            <a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a>
                        </h3>
                        <p class="post__body">
                            <?= substr($post['body'], 0, 300) ?>...
                        </p>
                        <div class="post__author">
                            <div class="post__author-avatar">
                                <img src="images/<?= $post['avatar'] ?>">
                            </div>
                            <div class="post__author-info">
                                <h5>Szerző: <?= $post['firstname'] ?> <?= $post['lastname'] ?></h5>
                                <small>
                                    <?php
                                        // Magyar dátum formátum: 2025. okt. 4. - 16:43
                                        $dateTime = new DateTime($post['date_time']);
                                        $formatter = new IntlDateFormatter('hu_HU', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
                                        $formatter->setPattern('yyyy. MMM. d. - HH:mm');
                                        echo $formatter->format($dateTime);
                                        $featuredDateTime = strtotime($post['date_time']);
                                        $currentDateTime = time();
                                        $timeDiffInSeconds = $currentDateTime - $featuredDateTime;
                                        $hoursAgo = floor($timeDiffInSeconds / 3600);
                                        if ($hoursAgo <= 24) {
                                            echo " ($hoursAgo órája)";
                                        }
                                    ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endwhile ?>
        </div>
    </section>
<?php else: ?>
    <div class="alert__message error lg section__extra-margin">
        <p>Nincsenek találatok</p>
    </div>
<?php endif ?>
<!-- END OF POSTS -->

<section class="category__buttons">
    <div class="container category__buttons-container">
        <?php if (mysqli_num_rows($categories) > 0): ?>
            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $category['id'] ?>"
                    class="category__button"><?= $category['title'] ?></a>
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