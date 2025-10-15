<?php
// Markdown-like formatting: *italic*, **bold**
function simple_markdown($text)
{
    // HTML entity dekódolás
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
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

$page_title = "Kategória";
include 'partials/header.php';

// Posztok lekérése az adatbázisból, ha van kategória id
if(isset($_GET['id'])){
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    
    // Posts query with prepared statement
    $posts_query = " SELECT 
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
                    WHERE posts.category_id = ?
                    ORDER BY date_time DESC";
    $posts_stmt = mysqli_prepare($connection, $posts_query);
    mysqli_stmt_bind_param($posts_stmt, "i", $id);
    mysqli_stmt_execute($posts_stmt);
    $posts_result = mysqli_stmt_get_result($posts_stmt);
    
    // Category query with prepared statement
    $category_query = "SELECT title FROM categories WHERE id = ?";
    $category_stmt = mysqli_prepare($connection, $category_query);
    mysqli_stmt_bind_param($category_stmt, "i", $id);
    mysqli_stmt_execute($category_stmt);
    $category_result = mysqli_stmt_get_result($category_stmt);
} else {
    header('location: '. ROOT_URL .'index.php');
    die();
}
// Kategórianév lekérése
$category = mysqli_fetch_assoc($category_result);

// Kategóriák lekérése
$categories_query = "SELECT * FROM categories ORDER BY title";
$categories = mysqli_query($connection, $categories_query);
?>

<header class="category__title">
    <h2><?=$category['title']?></h2>
</header>
<!-- KATEGÓRIA CÍM VÉGE -->

<?php if(mysqli_num_rows($posts_result) > 0 )  : ?>
<section class="posts">
    <div class="container posts__container">
        <?php while($post = mysqli_fetch_assoc($posts_result)) : ?>
        <article class="post">
            <div class="post__thumbnail">
                <img src="<?= ROOT_URL ?>blog/images/<?=$post['thumbnail']?>">
            </div>
            <div class="post__info">
                <div class="post__main">
                    <a href="<?= ROOT_URL ?>blog/category-posts.php?id=<?=$post['category_id']?>" class="category__button"><?=$post['category_title']?></a>
                    <h3 class="post__title">
                        <a href="<?= ROOT_URL ?>blog/post.php?id=<?=$post['id']?>"><?=$post['title']?></a>
                    </h3>
                    <p class="post__body">
                        <?= safe_excerpt($post['body'], 300)?>...
                    </p>
                    <div class="post__author">
                        <div class="post__author-avatar">
                            <img src="<?= ROOT_URL ?>blog/images/<?=$post['avatar']?>">
                        </div>
                        <div class="post__author-info">
                            <h5>Szerző: <?=$post['firstname']?> <?=$post['lastname']?></h5>
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
            </div>
        </article>
        <?php endwhile ?>
    </div>
</section>
<!-- END OF POSTS -->
<?php else : ?>
<div class="alert__message error lg">
    <p>Nincsenek posztok</p>
</div>
<?php endif ?>

<section class="category__buttons">
    <div class="container category__buttons-container">
    <?php if(mysqli_num_rows($categories) > 0) :?>
        <?php while($category = mysqli_fetch_assoc($categories)) : ?>
        <a href="<?= ROOT_URL ?>blog/category-posts.php?id=<?=$category['id']?>" class="category__button"><?=$category['title']?></a>
        <?php endwhile; ?>
    <?php else :?>
    <div class="alert__message error">
        <p>Nincsenek kategóriák</p>
    </div>
    <?php endif?>
    </div>
</section>
<!-- KATEGÓRIA GOMBOK VÉGE -->

<?php
include 'partials/footer.php'
?>

