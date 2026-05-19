<?php
require_once __DIR__ . '/../../Middleware/Permisos.php';
Permisos::verificar('miembros');
require_once 'header.php';
?>
<!-- Select2 Assets -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>
    <main class="main-content">
        <div class="container-fluid p-3 p-md-4 mb-5">
            <?php require_once __DIR__ . '/components/miembros_ui.php'; ?>
        </div>
        <?php require_once 'footer.php'; ?>


<!-- Select2 Script -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>