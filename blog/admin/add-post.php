<?php
$page_title = "Új poszt";
include 'partials/header.php';
include '../config/featured_posts.php';

// Ha a poszt hozzáadása sikertelen, visszaadjuk az adatokat
$title = (isset($_SESSION['add-post-data']) && isset($_SESSION['add-post-data']['title'])) ? $_SESSION['add-post-data']['title'] : '';
$body = (isset($_SESSION['add-post-data']) && isset($_SESSION['add-post-data']['body'])) ? $_SESSION['add-post-data']['body'] : '';
$selected_category = (isset($_SESSION['add-post-data']) && isset($_SESSION['add-post-data']['category'])) ? $_SESSION['add-post-data']['category'] : '';
$is_featured = (isset($_SESSION['add-post-data']) && isset($_SESSION['add-post-data']['is_featured'])) ? $_SESSION['add-post-data']['is_featured'] : 0;

// Session adatok törlése a megjelenítés után
if (isset($_SESSION['add-post-data'])) {
    unset($_SESSION['add-post-data']);
}
// Fetch categories from database
// Kategóriák lekérdezése
$query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $query);

// Kategóriák array-be mentése a újrafelhasználáshoz
$categories_array = [];
while ($category = mysqli_fetch_assoc($categories)) {
    $categories_array[] = $category;
}
?>

<script src="<?= ROOT_URL ?>blog/js/main.js"></script>
<script src="<?= ROOT_URL ?>blog/js/chat-context-menu.js"></script>

<section class="dashboard">
    <button id="sidebar-toggle-btn" class="sidebar__toggle">
        <i class="uil uil-arrow-right"></i>
    </button>

<div class="container dashboard__container">
    <aside>
        <ul>
            <li>
                <a href="add-post.php" class="active">
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
        <h2>Új poszt hozzáadása</h2>
        <?php if (isset($_SESSION['add-post'])): ?>
            <div class="alert__message error">
                <p>
                    <?= $_SESSION['add-post'];
                    unset($_SESSION['add-post']); ?>
                </p>
            </div>
        <?php endif ?>
        <?php if (count($categories_array) > 0): ?>
            <form action="<?= ROOT_URL ?>blog/admin/add-post-logic.php" enctype="multipart/form-data" method="POST">
                <div class="add-post-grid"
                    style="display:grid;grid-template-columns:1fr 1fr;gap:2.5rem;margin-bottom:1rem;align-items:flex-start;max-width:1200px;margin-left:auto;margin-right:auto;">
                    <div>
                        <input value="<?= htmlspecialchars($title) ?>" type="text" name="title" placeholder="Cím"
                            style="margin-bottom:1rem;width:100%;max-width:100%;">
                        <select name="category" id="" style="margin-bottom:1rem;width:100%;max-width:100%;">
                            <?php foreach ($categories_array as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= ($selected_category == $category['id']) ? 'selected' : '' ?>><?= $category['title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <textarea name="body" id="post-body" rows="6" placeholder="Leírás, tartalom..."
                            style="margin-bottom:1rem;width:100%;max-width:100%;resize:none;overflow:hidden;min-height:150px;"><?= htmlspecialchars($body) ?></textarea>
                    </div>
                    <div>
                        <div
                            style="background:#f7f7f7;border-radius:0.5rem;padding:1rem 1.2rem;color:#3a4a35;box-shadow:0 2px 8px rgba(45,74,34,0.07);font-size:0.98rem;margin-bottom:1.2rem;">
                            <strong>Formázási lehetőségek:</strong>
                            <ul style="margin:0.7rem 0 0 1.2rem;padding:0;list-style:disc;">
                                <li><span style="font-style:italic;">Dőlt:</span> <code>*dőlt szöveg*</code></li>
                                <li><span style="font-weight:bold;">Félkövér:</span> <code>**félkövér szöveg**</code></li>
                                <li><span style="color:#ff0000;">Színes 1 (piros):</span> <code>&1szöveg&</code></li>
                                <li><span style="color:#4a6b3a;">Színes 2 (zöld):</span> <code>&2szöveg&</code></li>
                                <li><span style="color:#b7c2a7;">Színes 3 (világoszöld):</span> <code>&3szöveg&</code></li>
                                <li><span style="color:#d16666;">Színes 4 (világos piros):</span> <code>&4szöveg&</code>
                                </li>
                                <li><span style="color:#6a7fd1;">Színes 5 (kék):</span> <code>&5szöveg&</code></li>
                            </ul>
                        </div>
                        <label for="preview" style="font-weight:600;color:#3a4a35;">Előnézet:</label>
                        <div id="post-preview"
                            style="background:#f7f7f7;border-radius:0.3rem;padding:1rem 1.2rem;color:#3a4a35;min-height:2.5rem;">
                        </div>
                    </div>
                </div>
                <script>
                    // Egyszerű Markdown + színezés + sortörés előnézet
                    function simpleMarkdown(text) {
                        // **félkövér**, *dőlt* szöveg
                        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                        text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
                        // Színőzés: &1-&5
                        text = text.replace(/&1(.*?)&/g, '<span style="color:#ff0000">$1</span>');
                        text = text.replace(/&2(.*?)&/g, '<span style="color:#4a6b3a">$1</span>');
                        text = text.replace(/&3(.*?)&/g, '<span style="color:#b7c2a7">$1</span>');
                        text = text.replace(/&4(.*?)&/g, '<span style="color:#d16666">$1</span>');
                        text = text.replace(/&5(.*?)&/g, '<span style="color:#6a7fd1">$1</span>');
                        // Sortörések HTML-re konvertálása
                        text = text.replace(/\n/g, '<br>');
                        return text;
                    }
                    const textarea = document.getElementById('post-body');
                    const preview = document.getElementById('post-preview');
                    
                    // Auto-resize textarea functionality
                    function autoResize(textarea) {
                        textarea.style.height = 'auto';
                        textarea.style.height = Math.max(textarea.scrollHeight, 150) + 'px';
                    }
                    
                    function updatePreview() {
                        preview.innerHTML = simpleMarkdown(textarea.value);
                        autoResize(textarea);
                    }
                    
                    textarea.addEventListener('input', updatePreview);
                    window.addEventListener('DOMContentLoaded', function() {
                        updatePreview();
                        autoResize(textarea);
                    });

                    // Egyedi context menü kijelölt szöveg formázásához
                    const contextMenu = document.createElement('div');
                    contextMenu.id = 'custom-context-menu';
                    contextMenu.style.position = 'absolute';
                    contextMenu.style.display = 'none';
                    contextMenu.style.background = '#fff';
                    contextMenu.style.border = '1px solid #ccc';
                    contextMenu.style.boxShadow = '0 2px 8px rgba(45,74,34,0.07)';
                    contextMenu.style.zIndex = '9999';
                    contextMenu.style.padding = '0.5rem 0';
                    contextMenu.style.borderRadius = '0.4rem';
                    contextMenu.style.minWidth = '160px';
                    contextMenu.innerHTML = `
                        <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="italic" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Dőlt</div>
                        <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="bold" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Félkövér</div>
                        <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color1" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 1 (piros)</div>
                        <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color2" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 2 (zöld)</div>
                        <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color3" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 3 (világoszöld)</div>
                        <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color4" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 4 (világos piros)</div>
                        <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color5" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 5 (kék)</div>
                    `;
                    document.body.appendChild(contextMenu);

                    textarea.addEventListener('contextmenu', function (e) {
                        e.preventDefault();
                        const rect = textarea.getBoundingClientRect();
                        contextMenu.style.top = (e.clientY) + 'px';
                        contextMenu.style.left = (e.clientX) + 'px';
                        contextMenu.style.display = 'block';
                    });

                    document.addEventListener('click', function (e) {
                        if (e.target.closest('#custom-context-menu')) return;
                        contextMenu.style.display = 'none';
                    });

                    contextMenu.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        if (!e.target.dataset.action) return;
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        const value = textarea.value;
                        const selected = value.substring(start, end);
                        let formatted = selected;
                        switch (e.target.dataset.action) {
                            case 'italic':
                                formatted = `*${selected}*`;
                                break;
                            case 'bold':
                                formatted = `**${selected}**`;
                                break;
                            case 'color1':
                                formatted = `&1${selected}&`;
                                break;
                            case 'color2':
                                formatted = `&2${selected}&`;
                                break;
                            case 'color3':
                                formatted = `&3${selected}&`;
                                break;
                            case 'color4':
                                formatted = `&4${selected}&`;
                                break;
                            case 'color5':
                                formatted = `&5${selected}&`;
                                break;
                        }
                        // Replace selected text
                        textarea.value = value.substring(0, start) + formatted + value.substring(end);
                        textarea.focus();
                        textarea.setSelectionRange(start, start + formatted.length);
                        updatePreview();
                        contextMenu.style.display = 'none';
                    });
                </script>
                <?php if (isset($_SESSION['user_is_admin'])): ?>
                    <div class="form__control featured-radio-container" style="margin-bottom:3rem;">
                        <label style="font-size:1.08rem;font-weight:600;color:#3a4a35;margin-bottom:0.8rem;display:block;">Kiemelt poszt státusz:</label>
                        <div class="featured-radio-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                            <label style="display:flex;align-items:center;gap:0.8rem;cursor:pointer;padding:1rem;border:2px solid #4a6b3a;border-radius:0.6rem;background:#ffffff;color:#2d4a22;font-size:1.1rem;font-weight:500;box-shadow:0 3px 8px rgba(0,0,0,0.1);transition:all 0.2s ease;">
                                <input type="radio" name="is_featured" value="0" <?= (!isset($is_featured) || $is_featured == 0) ? 'checked' : '' ?> style="accent-color:#4a6b3a;width:20px;height:20px;">
                                <span>📄 Nem kiemelt</span>
                            </label>
                            <label style="display:flex;align-items:center;gap:0.8rem;cursor:pointer;padding:1rem;border:2px solid #d4a574;border-radius:0.6rem;background:#ffffff;color:#8b5a2b;font-size:1.1rem;font-weight:600;box-shadow:0 3px 8px rgba(212,165,116,0.2);transition:all 0.2s ease;">
                                <input type="radio" name="is_featured" value="1" <?= (isset($is_featured) && $is_featured > 0) ? 'checked' : '' ?> style="accent-color:#d4a574;width:20px;height:20px;">
                                <span>⭐ Kiemelt</span>
                            </label>
                        </div>
                    </div>
                <?php endif ?>
                <div class="form__control file-upload-container" style="margin-bottom:2rem;">
                    <label for="thumbnail">Borítókép hozzáadása</label>
                    <input type="file" name="thumbnail" id="thumbnail">
                </div>
                <button class="btn submit-btn" name="submit" type="submit" style="margin-bottom:4rem;">Poszt hozzáadása</button>
            </form>
        <?php endif; ?>
    </main>
</div>

<?php
include '../partials/footer.php';