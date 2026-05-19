<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /ProyectoIglesia/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado</title>
    <!-- Incluir CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .denied-card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }

        .denied-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .btn-home {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="denied-card">
        <i class="fas fa-lock denied-icon"></i>
        <h2 class="mb-3">Acceso Denegado</h2>
        <p class="text-muted">No tienes los permisos necesarios para acceder a este m&oacute;dulo. Si crees que esto es
            un error, contacta al administrador del sistema.</p>
        <a href="/ProyectoIglesia/inicio" class="btn btn-primary btn-home"><i class="fas fa-home me-2"></i> Volver al Inicio</a>
    </div>
</body>

</html>