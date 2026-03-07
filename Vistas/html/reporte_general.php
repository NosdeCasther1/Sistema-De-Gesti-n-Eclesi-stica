<?php
include 'header.php'; 

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página en Construcción - Sistema de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
    }

    .wrapper {
        display: flex;
        width: 100%;
        align-items: stretch;
    }

    #header {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        background: white;
        border-bottom: 1px solid #dee2e6;
    }

    .main-container {
        display: flex;
        margin-top: 60px;
        min-height: calc(100vh - 120px);
    }

    #sidebar {
        min-width: 280px;
        max-width: 280px;
        min-height: 100vh;
    }

    #content {
        width: 100%;
        padding: 20px;
        min-height: 100vh;
        transition: all 0.3s;
    }

    .construction-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 200px);
        text-align: center;
        padding: 2rem;
    }

    .construction-icon {
        font-size: 5rem;
        color: #ffc107;
        margin-bottom: 2rem;
        animation: bounce 2s infinite;
    }

    .progress {
        height: 25px;
        width: 80%;
        max-width: 500px;
        margin: 2rem auto;
        background-color: #e9ecef;
    }

    .progress-bar {
        width: 75%;
        background-color: #ffc107;
        animation: progress-animation 2s;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes progress-animation {
        0% {
            width: 0%;
        }

        100% {
            width: 75%;
        }
    }

    .construction-text {
        max-width: 600px;
        margin: 0 auto;
    }

    .estimated-date {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        margin-top: 2rem;
        display: inline-block;
    }
    </style>
</head>

<body>
    <!-- Estructura principal de la página -->
    <div class="wrapper">
        <!-- Barra lateral (menú) -->
        <nav id="sidebar">
            <div class="position-sticky pt-3">
                <?php include 'menu.php'; ?>
            </div>
        </nav>

        <!-- Contenido Principal -->
        <main class="col-md-8 ms-sm-auto col-lg-10 px-md-4">
            <div class="construction-container">
                <i class="fas fa-hard-hat construction-icon"></i>
                <h1 class="mb-4">Página en Construcción</h1>

                <div class="construction-text">
                    <p class="lead mb-4">
                        Estamos trabajando arduamente para mejorar esta sección y brindarle una mejor experiencia.
                    </p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            75%
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-clock mb-3" style="font-size: 2rem; color: #6c757d;"></i>
                                <h5 class="card-title">Tiempo Estimado</h5>
                                <p class="card-text">2 semanas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-tasks mb-3" style="font-size: 2rem; color: #6c757d;"></i>
                                <h5 class="card-title">Progreso</h5>
                                <p class="card-text">75% Completado</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt mb-3" style="font-size: 2rem; color: #6c757d;"></i>
                                <h5 class="card-title">Fecha Estimada</h5>
                                <p class="card-text">15 de Noviembre, 2024</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="estimated-date mt-5">
                    <p class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Para más información, contacte al administrador
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>

<?php include 'footer.php'; ?>