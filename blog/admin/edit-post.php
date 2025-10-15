<?php
$page_title = "Poszt szerkesztése";
include 'partials/header.php';
include '../config/featured_posts.php';
?>
<script src="<?= ROOT_URL ?>blog/js/chat-context-menu.js"></script>
<?php
// Poszt szerkesztése, ha van id
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT *, featured_date FROM posts WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $post = mysqli_fetch_assoc($result);
} else {
    header('location: ' . ROOT_URL . 'blog/admin/index.php');
    die();
}

// Kategóriák lekérése
$query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $query);
?>

<section class="form__section">
    <div class="container form__section-container mobile-optimized">
        <h2>Poszt szerkesztése</h2>
        <?php if (isset($_SESSION['edit-post'])): ?>
            <div class="alert__message error">
                <p>
                    <?= $_SESSION['edit-post'];
                    unset($_SESSION['edit-post']); ?>
                </p>
            </div>
        <?php endif ?>
        <form action="<?= ROOT_URL ?>blog/admin/edit-post-logic.php" enctype="multipart/form-data" method="POST">
            <input type="hidden" value="<?= $post['id'] ?>" name="id">
            <input type="hidden" value="<?= $post['thumbnail'] ?>" name="previous_thumbnail_name">
            <div class="add-post-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:2.5rem;margin-bottom:1rem;align-items:flex-start;max-width:1200px;margin-left:auto;margin-right:auto;">
                <div>
                    <input name="title" value="<?= htmlspecialchars($post['title']) ?>" type="text" placeholder="Cím" style="margin-bottom:1rem;width:100%;max-width:100%;">
                    <select name="category" style="margin-bottom:1rem;width:100%;max-width:100%;">
                        <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $category['id'] ?>" <?= $category['id'] == $post['category_id'] ? 'selected' : '' ?>><?= $category['title'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <textarea name="body" id="edit-post-body" rows="6" placeholder="Leírás, tartalom..." style="margin-bottom:1rem;width:100%;max-width:100%;resize:none;overflow:hidden;min-height:150px;"><?= htmlspecialchars($post['body']) ?></textarea>
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
                            <li><span style="color:#d16666;">Színes 4 (világos piros):</span> <code>&4szöveg&</code></li>
                            <li><span style="color:#6a7fd1;">Színes 5 (kék):</span> <code>&5szöveg&</code></li>
                        </ul>
                    </div>
                    <label for="preview" style="font-weight:600;color:#3a4a35;">Előnézet:</label>
                    <div id="edit-post-preview"
                        style="background:#f7f7f7;border-radius:0.3rem;padding:1rem 1.2rem;color:#3a4a35;min-height:2.5rem;">
                    </div>
                </div>
            </div>
            <script>
                // Egyszerű Markdown + színezés + sortörés előnézet
                function simpleMarkdownEdit(text) {
                    // **félkövér**, *dőlt* szöveg
                    text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
                    // Színezés: &1-&5
                    text = text.replace(/&1"(.*?)"&/g, '<span style="color:#d4a574">$1</span>');
                    text = text.replace(/&2"(.*?)"&/g, '<span style="color:#4a6b3a">$1</span>');
                    text = text.replace(/&3"(.*?)"&/g, '<span style="color:#b7c2a7">$1</span>');
                    text = text.replace(/&4"(.*?)"&/g, '<span style="color:#d16666">$1</span>');
                    text = text.replace(/&5"(.*?)"&/g, '<span style="color:#6a7fd1">$1</span>');
                    // Sortörések HTML-re konvertálása
                    text = text.replace(/\n/g, '<br>');
                    return text;
                }
                const editTextarea = document.getElementById('edit-post-body');
                const editPreview = document.getElementById('edit-post-preview');
                
                // Auto-resize textarea functionality
                function autoResize(textarea) {
                    textarea.style.height = 'auto';
                    textarea.style.height = Math.max(textarea.scrollHeight, 150) + 'px';
                }
                
                function updateEditPreview() {
                    editPreview.innerHTML = simpleMarkdownEdit(editTextarea.value);
                    autoResize(editTextarea);
                }
                
                editTextarea.addEventListener('input', updateEditPreview);
                window.addEventListener('DOMContentLoaded', function() {
                    updateEditPreview();
                    autoResize(editTextarea);
                });

                // Egyedi context menü kijelölt szöveg formázásához
                const contextMenu = document.createElement('div');
                contextMenu.id = 'custom-context-menu-edit';
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
                    <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color1" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 1 (aranysárga)</div>
                    <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color2" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 2 (zöld)</div>
                    <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color3" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 3 (világoszöld)</div>
                    <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color4" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 4 (piros)</div>
                    <div style="padding:0.5rem 1.2rem;cursor:pointer;color:#2a3a2a;font-weight:500;transition:all 0.2s;" data-action="color5" onmouseover="this.style.backgroundColor='#e8f0e8'; this.style.color='#1a2a1a'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2a3a2a'">Színes 5 (kék)</div>
                `;
                document.body.appendChild(contextMenu);

                editTextarea.addEventListener('contextmenu', function (e) {
                    e.preventDefault();
                    contextMenu.style.top = (e.clientY) + 'px';
                    contextMenu.style.left = (e.clientX) + 'px';
                    contextMenu.style.display = 'block';
                });

                document.addEventListener('click', function (e) {
                    if (e.target.closest('#custom-context-menu-edit')) return;
                    contextMenu.style.display = 'none';
                });

                contextMenu.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    if (!e.target.dataset.action) return;
                    const start = editTextarea.selectionStart;
                    const end = editTextarea.selectionEnd;
                    const value = editTextarea.value;
                    const selectedText = value.substring(start, end);

                    let newText = '';
                    switch (e.target.dataset.action) {
                        case 'italic':
                            newText = value.substring(0, start) + '*' + selectedText + '*' + value.substring(end);
                            break;
                        case 'bold':
                            newText = value.substring(0, start) + '**' + selectedText + '**' + value.substring(end);
                            break;
                        case 'color1':
                            newText = value.substring(0, start) + '&1"' + selectedText + '"&' + value.substring(end);
                            break;
                        case 'color2':
                            newText = value.substring(0, start) + '&2"' + selectedText + '"&' + value.substring(end);
                            break;
                        case 'color3':
                            newText = value.substring(0, start) + '&3"' + selectedText + '"&' + value.substring(end);
                            break;
                        case 'color4':
                            newText = value.substring(0, start) + '&4"' + selectedText + '"&' + value.substring(end);
                            break;
                        case 'color5':
                            newText = value.substring(0, start) + '&5"' + selectedText + '"&' + value.substring(end);
                            break;
                    }

                    editTextarea.value = newText;
                    editTextarea.setSelectionRange(start, end + (newText.length - value.length));
                    editTextarea.focus();
                    updateEditPreview();
                    contextMenu.style.display = 'none';
                });
            </script>
                    <?php if (isset($_SESSION['user_is_admin'])): ?>
                        <div class="form__control featured-radio-container" style="margin-bottom:3rem;">
                            <label style="font-size:1.08rem;font-weight:600;color:#3a4a35;margin-bottom:0.8rem;display:block;">Kiemelt poszt státusz:</label>
                            <div class="featured-radio-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                                <?php 
                                    // Ellenőrizzük hogy ez a poszt kiemelt-e az új rendszerben
                                    $is_currently_featured = isPostFeatured($post['id'], $connection);
                                    $featured_date = $post['featured_date'] ?? null;
                                ?>
                                <label style="display:flex;align-items:center;gap:0.8rem;cursor:pointer;padding:1rem;border:2px solid #4a6b3a;border-radius:0.6rem;background:#ffffff;color:#2d4a22;font-size:1.1rem;font-weight:500;box-shadow:0 3px 8px rgba(0,0,0,0.1);transition:all 0.2s ease;">
                                    <input type="radio" name="is_featured" value="0" <?= (!$featured_date) ? 'checked' : '' ?> style="accent-color:#4a6b3a;width:20px;height:20px;">
                                    <span>📄 Nem manuálisan kiemelt</span>
                                </label>
                                <label style="display:flex;align-items:center;gap:0.8rem;cursor:pointer;padding:1rem;border:2px solid #d4a574;border-radius:0.6rem;background:#ffffff;color:#8b5a2b;font-size:1.1rem;font-weight:600;box-shadow:0 3px 8px rgba(212,165,116,0.2);transition:all 0.2s ease;">
                                    <input type="radio" name="is_featured" value="1" <?= ($featured_date) ? 'checked' : '' ?> style="accent-color:#d4a574;width:20px;height:20px;">
                                    <span>⭐ Manuálisan kiemelt (15 nap)</span>
                                </label>
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="form__control file-upload-container" style="margin-bottom:2rem;">
                        <label for="thumbnail">Borítókép módosítása</label>
                        <input type="file" name="thumbnail" id="thumbnail">
                    </div>
                    <button class="btn submit-btn" name="submit" type="submit" style="margin-bottom:1rem;">Mentés</button>
            </div>
        </form>
    </div>
</section>

<?php
include '../partials/footer.php';
?>