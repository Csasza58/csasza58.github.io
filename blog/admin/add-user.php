<?php
$page_title = "Új felhasználó";
include 'partials/header.php';

// Get back form data if registration fails
$firstname = $_SESSION['add-user-data']['firstname'] ?? null;
$lastname = $_SESSION['add-user-data']['lastname'] ?? null;
$username = $_SESSION['add-user-data']['username'] ?? null;
$email = $_SESSION['add-user-data']['email'] ?? null;
$createpassword = $_SESSION['add-user-data']['createpassword'] ?? null;
$confirmpassword = $_SESSION['add-user-data']['confirmpassword'] ?? null;

// Delete signup data session
unset($_SESSION['add-user-data']);
?>

<section class="form__section">
    <div class="container form__section-container mobile-optimized">
        <h2>Felhasználó hozzáadása</h2>
        <?php if (isset($_SESSION['add-user'])): ?>
            <div class="alert__message error">
                <p>
                    <?= $_SESSION['add-user']; unset($_SESSION['add-user']); ?>
                </p>
            </div>
        <?php endif ?>
        <form action="<?= ROOT_URL ?>blog/admin/add-user-logic.php" enctype="multipart/form-data" method="POST">
            <input value="<?= $firstname ?>" name="firstname" type="text" placeholder="Keresztnév">
            <input value="<?= $lastname ?>" name="lastname" type="text" placeholder="Vezetéknév">
            <input value="<?= $username ?>" name="username" type="text" placeholder="Felhasználónév">
            <input value="<?= $email ?>" name="email" type="email" placeholder="Email cím">
            <input value="<?= $createpassword ?>" name="createpassword" type="password" placeholder="Jelszó">
            <input value="<?= $confirmpassword ?>" name="confirmpassword" type="password" placeholder="Jelszó megerősítése">
            <select name="userrole">
                <option value="0">Szerző</option>
                <option value="1">Admin</option>
            </select>
            <div class="form__control">
                <label for="avatar">Profilkép</label>
                <input type="file" name="avatar" id="avatar">
            </div>
            <button class="btn" name="submit" type="submit">Felhasználó hozzáadása</button>
        </form>
    </div>
</section>

<?php
include '../partials/footer.php';
?>