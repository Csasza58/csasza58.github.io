// Admin Sidebar Toggle - EGYETLEN GOMB
console.log('Mobile admin JS betöltve!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready!');
    
    const sidebar = document.querySelector('.dashboard__container aside');
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    
    if (!sidebar || !toggleBtn) {
        console.error('Elemek nem találhatók!');
        return;
    }
    
    console.log('Minden elem megtalálva!');
    
    // Overlay létrehozása
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }
    
    // Toggle funkció - nyit/zár
    function toggleSidebar() {
        const isOpen = sidebar.classList.contains('open');
        
        if (isOpen) {
            // Bezárás
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
            console.log('Sidebar bezárva');
        } else {
            // Megnyitás
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.classList.add('sidebar-open');
            console.log('Sidebar megnyitva');
        }
    }
    
    // Toggle button click
    toggleBtn.addEventListener('click', function(e) {
        console.log('TOGGLE GOMB KATTINTVA!');
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
    });
    
    // Overlay click - bezár
    overlay.addEventListener('click', function() {
        console.log('OVERLAY KATTINTVA!');
        if (sidebar.classList.contains('open')) {
            toggleSidebar();
        }
    });
    
    // ESC gomb - bezár
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            console.log('ESC megnyomva!');
            toggleSidebar();
        }
    });
    
    console.log('Event listeners felállítva!');
});
