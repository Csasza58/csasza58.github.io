<?php
require '../partials/header.php';

// Bejelentkezés ellenőrzése
if(!isset($_SESSION['user-id'])){
    header('location:' . ROOT_URL . 'blog/signin.php');
    die();
}

// ADMIN MOBILE CSS BETÖLTÉSE
echo '<link rel="stylesheet" href="' . ROOT_URL . 'blog/css/admin-mobile.css?v=' . time() . '">';

// Mobil gombok FORCE display - KRITIKUS!
echo '<style>
@media (max-width: 768px) {
    #show__sidebar-btn, #hide__sidebar-btn {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    
    .sidebar__toggle {
        display: flex !important;
    }
}
</style>';

// Mobil admin menü JavaScript betöltése - DEFER!
echo '<script defer src="' . ROOT_URL . 'blog/admin/js/mobile-admin.js?v=' . time() . '"></script>';
