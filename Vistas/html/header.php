<?php
/**
 * header.php
 * Responsabilidades: iniciar sesión + renderizar <head> y barra de navegación.
 * La lógica de negocio (notificaciones, datos de usuario) vive en services/notificaciones.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Liberar el bloqueo de sesión temprano para evitar bloqueos en iframes
session_write_close();

// Cargar notificaciones y datos del usuario desde el servicio dedicado
require_once __DIR__ . '/services/notificaciones.php';
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Iglesia AD Rey de Reyes</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap Bundle JS (requerido para Collapse, Dropdowns, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Evitar caché en desarrollo visual -->
    <link rel="stylesheet" href="/ProyectoIglesia/assets/css/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/ProyectoIglesia/assets/css/index.css?v=<?php echo time(); ?>">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* --- Header Premium SaaS --- */
        .header {
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding: 0;
            margin: 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1050;
            height: 70px;
            /* Un poco más espacioso */
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            /* Sombra ultra suave Tailwind-style */
            display: flex;
            align-items: center;
        }

        .header .container-fluid {
            padding-left: 0;
            height: 100%;
        }

        .header .navbar {
            width: 100%;
            padding: 0;
            height: 100%;
        }

        /* Navbar Brand (Logo area matches sidebar width) */
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            height: 100%;
            width: 280px;
            /* Igual al ancho del sidebar global */
            padding-left: 1.5rem;
            background: linear-gradient(180deg, rgba(248, 249, 252, 0) 0%, rgba(248, 249, 252, 0.5) 100%);
            border-right: 1px solid rgba(0, 0, 0, 0.03);
            color: #1e293b !important;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -0.02em;
        }

        .navbar-brand img {
            border-radius: 6px;
            object-fit: contain;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Icon Buttons (Botones circulares interactivos) */
        .header .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            position: relative;
        }

        .header .icon-btn:hover,
        .header .icon-btn:focus {
            background-color: #f1f5f9;
            color: #2563eb;
        }

        /* Notificación Badge (Globo numérico) */
        .notification-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            min-width: 18px;
            height: 18px;
            border-radius: 10px;
            background: #ef4444;
            /* Rojo vibrante Tailwind */
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            padding: 0 4px;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        }

        /* Avatar Dinámico del Usuario */
        .user-avatar-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px rgba(226, 232, 240, 1), 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .nav-link.user-menu-toggle:hover .user-avatar-circle {
            box-shadow: 0 0 0 2px #3b82f6, 0 4px 6px rgba(59, 130, 246, 0.2);
            transform: scale(1.02);
        }

        /* Nombre del Usuario en el header */
        .header-username {
            color: #334155;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Dropdown Menus Soft (Menús Desplegables Estilo SaaS) */
        .header .dropdown-menu {
            right: 0;
            left: auto;
            position: absolute;
            margin-top: 18px !important;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 0.5rem 0;
            min-width: 240px;
            animation: dropdownFade 0.2s ease-out forwards;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header .dropdown-item {
            padding: 0.6rem 1.25rem;
            color: #475569;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .dropdown-item:hover {
            background-color: #f8fafc;
            color: #0f172a;
        }

        .header .dropdown-item i {
            width: 20px;
            text-align: center;
            opacity: 0.7;
        }

        .header .dropdown-divider {
            border-top: 1px solid #f1f5f9;
            margin: 0.4rem 0;
        }

        /* Custom Notificación Dropdown */
        .dropdown-menu-notifications {
            min-width: 320px !important;
            padding: 0;
        }

        .notification-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: start;
            gap: 12px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8fafc;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .HamburguerMenu {
            display: none;
        }

        @media (max-width: 991px) {
            .navbar-brand {
                width: auto;
                border-right: none;
                background: none;
            }

            .HamburguerMenu {
                display: flex;
            }
        }
    </style>

    <!-- SweetAlert2 para Notificaciones y Confirmaciones Amigables -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <header class="header">
        <nav class="navbar navbar-expand h-100 w-100">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center h-100">

                <!-- Logo & Brand (Bloque Izquierdo) -->
                <div class="d-flex align-items-center h-100">
                    <a class="navbar-brand text-decoration-none" href="/ProyectoIglesia/Vistas/html/index.php">
                        <img src="/ProyectoIglesia/img/logo.png" alt="Logo AD Rey de Reyes" style="height: 38px;">
                        <span class="ms-2 text-truncate d-none d-sm-inline">AD Rey de Reyes</span>
                    </a>

                    <!-- Toggle Boton para sidebar (Oculto en desktop por defecto, pero útil para mobile/tablet) -->
                    <a href="#" class="icon-btn ms-2 HamburguerMenu" data-bs-toggle="collapse"
                        data-bs-target="#sidebarCollapse" aria-label="Toggle navigation">
                        <i class="fas fa-bars fs-5"></i>
                    </a>
                </div>

                <!-- Iconos de Acción y Perfil (Bloque Derecho) -->
                <div class="d-flex align-items-center pe-3 pe-md-4">
                    <ul class="navbar-nav align-items-center flex-row gap-1 gap-sm-2">

                        <!-- Fullscreen Toggle -->
                        <li class="nav-item d-none d-sm-block">
                            <a class="icon-btn" href="#" id="fullscreenToggle" title="Pantalla Completa">
                                <i class="fas fa-expand fs-6"></i>
                            </a>
                        </li>

                        <!-- Campana de Notificaciones -->
                        <li class="nav-item dropdown">
                            <a class="icon-btn" href="#" id="notificationsDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones">
                                <i class="fas fa-bell fs-6"></i>
                                <?php if ($cantidadNotificaciones > 0): ?>
                                    <span
                                        class="notification-badge"><?php echo $cantidadNotificaciones > 9 ? '9+' : $cantidadNotificaciones; ?></span>
                                <?php endif; ?>
                            </a>

                            <!-- Menú Notificaciones Premium -->
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-notifications"
                                aria-labelledby="notificationsDropdown">
                                <div class="notification-header">
                                    <h6 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center">
                                        Notificaciones
                                        <?php if ($cantidadNotificaciones > 0): ?>
                                            <span
                                                class="badge bg-primary ms-2 rounded-pill px-2"><?php echo $cantidadNotificaciones; ?>
                                                Nuevas</span>
                                        <?php endif; ?>
                                    </h6>
                                    <a href="#"
                                        class="text-primary small fw-medium text-decoration-none hover-underline">Marcar
                                        leídas</a>
                                </div>

                                <div class="notification-list" style="max-height: 300px; overflow-y: auto;">
                                    <?php if (empty($notificaciones)): ?>
                                        <div class="p-4 text-center">
                                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 50px; height: 50px;">
                                                <i class="fas fa-bell-slash text-muted fs-4"></i>
                                            </div>
                                            <p class="text-muted mb-0 fw-medium">Estás al día</p>
                                            <small class="text-muted">No tienes notificaciones pendientes.</small>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($notificaciones as $notificacion): ?>
                                            <a href="#" class="notification-item">
                                                <div
                                                    class="notification-icon-wrapper <?php echo htmlspecialchars($notificacion['bg']); ?> bg-opacity-10">
                                                    <i class="<?php echo htmlspecialchars($notificacion['icono']); ?>"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="mb-0 text-dark fw-medium fs-sm text-truncate">
                                                        <?php echo htmlspecialchars($notificacion['mensaje']); ?>
                                                    </p>
                                                    <small class="text-muted d-flex align-items-center mt-1">
                                                        <i class="far fa-clock me-1 opacity-75"></i>
                                                        <?php echo htmlspecialchars($notificacion['tiempo']); ?>
                                                    </small>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($notificaciones)): ?>
                                    <div class="p-2 border-top text-center">
                                        <a href="#"
                                            class="btn btn-sm btn-link text-decoration-none fw-bold text-primary w-100">Ver
                                            historial completo</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>

                        <!-- Separador Vertical -->
                        <li class="nav-item">
                            <div class="vr h-50 mx-2 text-secondary opacity-25 d-none d-sm-block"
                                style="min-height: 30px;"></div>
                        </li>

                        <!-- Menú de Usuario -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center user-menu-toggle px-2 py-1 outline-none text-decoration-none"
                                href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                style="border: none !important;">
                                <!-- Nombre: Oculto en móbiles pequeños -->
                                <div class="d-none d-md-flex flex-column text-end me-3">
                                    <span
                                        class="header-username lh-1 mb-1"><?php echo htmlspecialchars($nombreUsuario); ?></span>
                                    <span class="text-muted small lh-1"
                                        style="font-size: 0.75rem;"><?php echo htmlspecialchars($rolUsuario); ?></span>
                                </div>
                                <!-- Avatar Generado -->
                                <div class="user-avatar-circle flex-shrink-0"
                                    style="background-color: <?php echo $colorAvatar; ?>;">
                                    <?php echo $iniciales; ?>
                                </div>
                            </a>

                            <!-- Menú Usuario Estilo Soft -->
                            <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown">
                                <li class="px-3 py-2 d-md-none border-bottom mb-1">
                                    <span
                                        class="d-block text-dark fw-bold"><?php echo htmlspecialchars($nombreUsuarioCompleto); ?></span>
                                    <small class="text-muted"><?php echo htmlspecialchars($rolUsuario); ?></small>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="#">
                                        <i class="far fa-id-badge text-primary fs-5 me-3 opacity-75"></i> <span
                                            style="font-weight: 500; color: #3a3b45;">Mi Perfil</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="#">
                                        <i class="fas fa-sliders-h text-primary fs-5 me-3 opacity-75"></i> <span
                                            style="font-weight: 500; color: #3a3b45;">Configuración</span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2 text-danger"
                                        href="/ProyectoIglesia/Vistas/html/logout.php">
                                        <i class="fas fa-sign-out-alt fs-5 me-3 opacity-75"></i> <span
                                            style="font-weight: 600;">Cerrar Sesión</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Overlay transparente para cuando se abre el sidebar en movil (Opcional, se maneja usualmente en sidebar) -->
    <div class="sidebar-overlay d-none d-lg-none"
        style="position: fixed; top:0; left:0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1030;">
    </div>

    <script>
        // Lógica del Sidebar para Móviles
        document.addEventListener('DOMContentLoaded', function () {
            const hamburgerBtn = document.querySelector('.HamburguerMenu');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            if (hamburgerBtn && sidebar && overlay) {
                // Función para abrir/cerrar sidebar
                function toggleSidebar() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('d-none');
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                }

                // Clic en hamburguesa
                hamburgerBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    toggleSidebar();
                });

                // Clic flotante afuera (overlay)
                overlay.addEventListener('click', toggleSidebar);
            }
        });

        // Función para alternar pantalla completa sin problemas de compatibilidad
        function toggleFullScreen() {
            if (!document.fullscreenElement &&    // alternative standard method
                !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {  // current working methods

                // Guardar preferencia
                localStorage.setItem('church_crm_fullscreen', 'true');

                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen().catch(err => { console.log(err) });
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
                // Remover preferencia
                localStorage.removeItem('church_crm_fullscreen');

                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }
        }

        const fullscreenBtn = document.getElementById('fullscreenToggle');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function (e) {
                e.preventDefault();
                toggleFullScreen();
            });
        }

        // Efectos visuales de toggle basados en los eventos nativos del navegador
        document.addEventListener('fullscreenchange', updateFullscreenIcon);
        document.addEventListener('webkitfullscreenchange', updateFullscreenIcon);
        document.addEventListener('mozfullscreenchange', updateFullscreenIcon);
        document.addEventListener('MSFullscreenChange', updateFullscreenIcon);

        function updateFullscreenIcon() {
            const icon = fullscreenBtn ? fullscreenBtn.querySelector('i') : null;
            if (!icon) return;

            if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
                icon.classList.replace('fa-expand', 'fa-compress');
                localStorage.setItem('church_crm_fullscreen', 'true');
            } else {
                icon.classList.replace('fa-compress', 'fa-expand');
                localStorage.removeItem('church_crm_fullscreen');
            }
        }

        // Intentar restaurar estado de pantalla completa automáticamente
        // NOTA: La mayoría de los navegadores modernos bloquean esto por seguridad si no hay interacción directa del usuario en esa recarga de página.
        // Solo funcionará en configuraciones específicas tipo PWA o Kiosco. Visualmente al menos lo dejamos preparado por si el navegador lo permite.    </script>