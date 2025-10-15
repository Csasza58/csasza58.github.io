// Egyedi context menü a post-body textarea-hoz
// chat-context-menu.js

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('post-body');
    if (!textarea) return;

    // Context menü létrehozása
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

    textarea.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        contextMenu.style.top = (e.clientY) + 'px';
        contextMenu.style.left = (e.clientX) + 'px';
        contextMenu.style.display = 'block';
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('#custom-context-menu')) return;
        contextMenu.style.display = 'none';
    });

    contextMenu.addEventListener('mousedown', function(e) {
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
        // Ha van előnézet, frissítsük
        const preview = document.getElementById('post-preview');
        if (preview) {
            preview.innerHTML = window.simpleMarkdown ? window.simpleMarkdown(textarea.value) : textarea.value;
        }
        contextMenu.style.display = 'none';
    });
});
