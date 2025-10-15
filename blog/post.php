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

require 'config/database.php';

// Fetch post from database if set
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $post_query = "SELECT 
                    posts.id,
                    posts.title,
                    posts.body,
                    posts.date_time,
                    posts.thumbnail,
                    users.avatar,
                    users.firstname,
                    users.lastname
                    FROM posts
                    INNER JOIN users ON posts.author_id = users.id
                    WHERE posts.id = ?";
    $stmt = mysqli_prepare($connection, $post_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $post_result = mysqli_stmt_get_result($stmt);
    $post = mysqli_fetch_assoc($post_result);
    $page_title = $post ? $post['title'] : 'Poszt nem található';
} else {
    header('location: ' . ROOT_URL . 'index.php');
    die();
}

include 'partials/header.php';
?>

<section class="singlepost">
    <div class="container singlepost__container">
        <h2><?= $post['title'] ?></h2>
        <div class="post__author">
            <div class="post__author-avatar">
                <img src="<?= ROOT_URL ?>blog/images/<?= $post['avatar'] ?>">
            </div>
            <div class="post__author-info">
                <h5>Szerző: <?= $post['firstname'] ?> <?= $post['lastname'] ?></h5>
                <small>
                    <?php
                        date_default_timezone_set('Europe/Budapest');
                        // Egyszerűbb dátumformázás IntlDateFormatter nélkül
                        $timestamp = strtotime($post['date_time']);
                        
                        // Magyar hónapnevek
                        $magyar_honapok = [
                            1 => 'jan.', 2 => 'febr.', 3 => 'márc.', 4 => 'ápr.',
                            5 => 'máj.', 6 => 'jún.', 7 => 'júl.', 8 => 'aug.',
                            9 => 'szept.', 10 => 'okt.', 11 => 'nov.', 12 => 'dec.'
                        ];
                        
                        $ev = date('Y', $timestamp);
                        $honap = $magyar_honapok[(int)date('n', $timestamp)];
                        $nap = date('d', $timestamp);
                        $ora = date('H:i', $timestamp);
                        
                        echo "$ev. $honap $nap. - $ora";
                        
                        $hoursAgo = floor((time() - $timestamp) / 3600);
                        if ($hoursAgo <= 24) {
                            echo " ($hoursAgo órája)";
                        }
                    ?>
                </small>
            </div>
        </div>
        <?php if (!empty($post['thumbnail'])): ?>
        <div class="singlepost__thumbnail">
            <img src="<?= ROOT_URL ?>blog/images/<?= $post['thumbnail'] ?>" alt="<?= htmlspecialchars($post['title']) ?>">
        </div>
        <?php endif; ?>
        <p><?= simple_markdown($post['body']) ?></p>
    </div>
</section>

<?php
include 'partials/footer.php';
?>
