<?php
$page_title = "Új kategória";
include 'partials/header.php';

// Ha a kategória hozzáadása sikertelen, visszaadjuk az adatokat
$title          = $_SESSION['add-category-data']['title'] ?? null;
$description    = $_SESSION['add-category-data']['description'] ?? null;

// Kategória hozzáadási adatok törlése
unset($_SESSION['add-category-data']);
?>

<section class="form__section">
    <div class="container form__section-container mobile-optimized">
        <h2>Kategória hozzáadása</h2>
        <?php if(isset($_SESSION['add-category'])) : ?>
            <div class="alert__message error">
                <p>
                    <?= $_SESSION['add-category']; unset($_SESSION['add-category']);?>
                </p>
            </div>
        <?php endif?>
        <form action="<?= ROOT_URL?>blog/admin/add-category-logic.php" method="POST">
            <input value="<?=$title?>" name="title" type="text" placeholder="Kategória neve">
            <textarea name="description" id="" rows="4" placeholder="Leírás, magyarázat..."><?=$description?></textarea>
            <button class="btn" name="submit" type="submit">Kategória hozzáadása</button>
        </form>
    </div>
</section>

<?php
include '../partials/footer.php';
?>