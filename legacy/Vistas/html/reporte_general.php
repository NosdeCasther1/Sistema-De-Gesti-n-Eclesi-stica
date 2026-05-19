<?php
require_once __DIR__ . '/../../Middleware/Permisos.php';
Permisos::verificar('reportes');
require_once 'header.php'; 

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}
?>

<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido Principal -->
    <main class="main-content">
        <div class="container-fluid p-3 p-md-4 mb-5">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Reporte General de Membresía</h1>
                    <p class="text-muted">Consolidado estadístico y demográfico de la congregación.</p>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-tools text-warning" style="font-size: 5rem; animation: bounce 2s infinite;"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-3">Módulo en Desarrollo</h2>
                    <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
                        Estamos trabajando arduamente para integrar las estadísticas avanzadas, gráficos demográficos y reportes de crecimiento ministerial en esta sección.
                    </p>
                    
                    <div class="progress mb-4 mx-auto" style="height: 12px; width: 80%; max-width: 500px; border-radius: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="badge bg-light text-dark border p-2 px-3 mb-5" style="border-radius: 8px;">
                        <i class="fas fa-tasks me-2 text-primary"></i>Progreso actual: 75% completado
                    </span>

                    <div class="row g-4 mt-2">
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <i class="fas fa-clock mb-2 text-muted" style="font-size: 1.5rem;"></i>
                                <h6 class="fw-bold mb-1">Tiempo Estimado</h6>
                                <p class="small text-muted mb-0">Próximas semanas</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <i class="fas fa-calendar-check mb-2 text-muted" style="font-size: 1.5rem;"></i>
                                <h6 class="fw-bold mb-1">Próxima Versión</h6>
                                <p class="small text-muted mb-0">v1.3.0 stable</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light">
                                <i class="fas fa-info-circle mb-2 text-muted" style="font-size: 1.5rem;"></i>
                                <h6 class="fw-bold mb-1">Disponibilidad</h6>
                                <p class="small text-muted mb-0">Solo Administradores</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>

<style>
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}
</style>