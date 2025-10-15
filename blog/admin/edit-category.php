<?php
$page_title = "Kategória szerkesztése";
include 'partials/header.php';

if(isset($_GET['id'])){
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM categories WHERE id=$id";
    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows($result) == 1){
        $category = mysqli_fetch_assoc($result);
    }
} else {
    header('location: '. ROOT_URL .'blog/admin/manage-categories.php');
    die();
}
?>

<section class="form__section">
    <div class="container form__section-container mobile-optimized">
        <h2>Edit Category</h2>
        <form action="<?= ROOT_URL?>blog/admin/edit-category-logic.php" method="POST">
            <input type="hidden" value="<?= $category['id']?>" name="id">
            <input type="text" name="title" value="<?= $category['title']?>" placeholder="Title">
            <textarea name="description" id="" rows="4" placeholder="Description"><?= $category['description']?></textarea>
            <button class="btn" name="submit" type="submit">Update Category</button>
        </form>
    </div>
</section>

<?php
include '../partials/footer.php';
?>

