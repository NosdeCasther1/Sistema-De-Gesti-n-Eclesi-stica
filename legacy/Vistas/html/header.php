<?php
/**
 * header.php - Layout Core
 * Responsabilidades: Iniciar sesión, renderizar <head> HTML, navbar superior y apertura de estructura.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_write_close();

// Definir constante BASE_URL si no existe (Útil para assets)
if (!defined('BASE_URL')) {
    define('BASE_URL', '/ProyectoIglesia');
}

// Cargar notificaciones
require_once __DIR__ . '/services/notificaciones.php';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Iglesia - AD Rey de Reyes</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Core Libraries (CDNs con Fallback) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Core Scripts (Moved to header for component availability) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Application Standard Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/index.css?v=<?= time() ?>">
    
    <!-- Theme Persistence -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('preferred-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

<!-- Estilos Críticos del Header (Alta Especificidad) -->
<style>
    /* --- Header Layout & Structure --- */
    .header {
        background-color: var(--nav-bg) !important;
        backdrop-filter: blur(10px);
        padding: 0;
        margin: 0;
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 1050;
        height: 70px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
    }
    
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
        width: 280px;
        padding-left: 1.5rem;
        background-color: var(--nav-bg) !important;
        border-right: 1px solid var(--border-color);
        color: var(--text-primary) !important;
        font-weight: 700;
        font-size: 1.15rem;
        letter-spacing: -0.02em;
    }
    
    .header .icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease-in-out;
        position: relative;
    }

    .header .icon-btn:hover {
        background-color: var(--border-color);
        color: var(--bs-primary);
    }

    /* --- Profile & Dropdown --- */
    .header .header-username {
        color: var(--text-primary) !important;
        font-weight: 600 !important;
        font-size: 0.9rem;
    }
    .header .user-role {
        color: var(--text-muted) !important;
        font-size: 0.75rem;
    }
    .user-avatar-circle {
        width: 42px !important;
        height: 42px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #10b981, #059669) !important;
        color: #ffffff !important;
        font-weight: 700;
        border: 2px solid rgba(var(--nav-bg-rgb), 0.2) !important;
    }
    .header .dropdown-menu {
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        box-shadow: var(--shadow-lg) !important;
    }
    .header .dropdown-item {
        color: var(--text-main);
        padding: 10px 15px !important;
        transition: all 0.2s;
        display: flex;
        align-items: center;
    }
    .header .dropdown-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.08) !important;
        color: var(--bs-primary) !important;
    }
    .header .dropdown-item.text-danger:hover {
        background-color: rgba(var(--bs-danger-rgb), 0.08) !important;
        color: var(--bs-danger) !important;
    }
</style>
</head>
<body class="d-flex flex-column min-vh-100">

    <header class="header">
        <nav class="navbar navbar-expand h-100 w-100">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center h-100">

                <!-- Logo & Brand (Bloque Izquierdo) -->
                <div class="d-flex align-items-center h-100">
                    <a class="navbar-brand text-decoration-none" href="/ProyectoIglesia/inicio">
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

                        <!-- Theme Toggle -->
                        <li class="nav-item">
                            <a class="icon-btn theme-toggle" href="#" title="Cambiar Tema">
                                <i class="fas fa-moon theme-icon-moon fs-6"></i>
                                <i class="fas fa-sun theme-icon-sun fs-6 d-none"></i>
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
                                    <span class="header-username lh-1 mb-1"><?php echo htmlspecialchars($nombreUsuario); ?></span>
                                    <span class="user-role lh-1"><?php echo htmlspecialchars($rolUsuario); ?></span>
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
                                            style="font-weight: 500;">Mi Perfil</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="#">
                                        <i class="fas fa-sliders-h text-primary fs-5 me-3 opacity-75"></i> <span
                                            style="font-weight: 500;">Configuración</span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2 text-danger"
                                        href="/ProyectoIglesia/logout">
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
        // Solo funcionará en configuraciones específicas tipo PWA o Kiosco. Visualmente al menos lo dejamos preparado por si el navegador lo permite.    
    </script>
