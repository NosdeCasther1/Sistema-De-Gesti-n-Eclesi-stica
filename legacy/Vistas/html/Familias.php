<?php
require_once __DIR__ . '/../../Middleware/Permisos.php';
Permisos::verificar('miembros');
require_once 'header.php';
?>

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>
    <main class="main-content">
        <div class="container-fluid p-3 p-md-4 mb-5">
            <?php require_once __DIR__ . '/components/familias_ui.php'; ?>
        </div>
        <?php require_once 'footer.php'; ?>