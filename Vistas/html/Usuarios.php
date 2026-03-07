<?php
include 'header.php';
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

$isComponent = false;
include __DIR__ . '/components/usuarios_logica.php';
?>

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <?php include __DIR__ . '/components/usuarios_ui.php'; ?>
        </div>
    </main>
</div>

</body>

</html>
<?php
$conn->close();
include 'footer.php';
?>