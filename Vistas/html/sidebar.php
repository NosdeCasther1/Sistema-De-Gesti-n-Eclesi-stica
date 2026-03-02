<?php
// Verificar si la constante BASE_URL está definida
if (!defined('BASE_URL')) {
    define('BASE_URL', '/ProyectoIglesia');
}

// Función para determinar si un enlace está activo comparando con la URL actual
function is_active($page_name)
{
    if (empty($page_name) || $page_name === '#')
        return '';

    $current_path = $_SERVER['PHP_SELF'];
    $page_path = parse_url($page_name, PHP_URL_PATH);
    $page_path = str_replace(BASE_URL, '', $page_path);

    return (strpos($current_path, $page_path) !== false) ? 'active' : '';
}

// Función para generar IDs únicos y seguros para los elementos del menú
function sanitize_id($text)
{
    return preg_replace('/[^a-z0-9-]/', '-', strtolower($text));
}

// Definición de la estructura del menú
$menu_items = [
    ['icon' => 'fa-home', 'text' => 'Inicio', 'link' => BASE_URL . '/Vistas/html/index.php'],
    [
        'icon' => 'fa-users',
        'text' => 'Miembros',
        'submenu' => [
            ['text' => 'Familias', 'link' => BASE_URL . '/Vistas/html/Familias.php'],
            ['text' => 'Miembros', 'link' => BASE_URL . '/Vistas/html/Miembros.php'],
            ['text' => 'Importar Miembros', 'link' => BASE_URL . '/Vistas/html/ImportarMiembros.php'],
        ]
    ],
    ['icon' => 'fa-sitemap', 'text' => 'Células Familiares', 'link' => BASE_URL . '/Vistas/html/CelulasFamiliares.php'],
    [
        'icon' => 'fa-calendar-alt',
        'text' => 'Eventos y Seminarios',
        'submenu' => [
            ['text' => 'Crear', 'link' => BASE_URL . '/Vistas/html/CrearEvento.php'],
            ['text' => 'Mostrar', 'link' => BASE_URL . '/Vistas/html/MostrarEventos.php'],
        ]
    ],
    [
        'icon' => 'fa-clipboard-check',
        'text' => 'Asistencia',
        'submenu' => [
            ['text' => 'Crear', 'link' => BASE_URL . '/Vistas/html/CrearAsistencia.php'],
            ['text' => 'Mostrar', 'link' => BASE_URL . '/Vistas/html/asistencia_mostrar.php'],
        ]
    ],
    [
        'icon' => 'fa-money-bill-wave',
        'text' => 'Tesorería',
        'submenu' => [
            ['text' => 'Categoría de Ingresos', 'link' => BASE_URL . '/Vistas/html/CategoriaIngresos.php'],
            ['text' => 'Control de Ofrendas', 'link' => BASE_URL . '/Vistas/html/IngresoOfrendas.php'],
            ['text' => 'Control de Diezmos', 'link' => BASE_URL . '/Vistas/html/IngresoDiezmos.php'],
            ['text' => 'Tipos de Gastos', 'link' => BASE_URL . '/Vistas/html/TipoGastos.php'],
            ['text' => 'Control de Gastos', 'link' => BASE_URL . '/Vistas/html/ControlGastos.php'],
        ]
    ],
    ['icon' => 'fa-couch', 'text' => 'Bienes y Muebles', 'link' => BASE_URL . '/Vistas/html/BienesMuebles.php'],
    [
        'icon' => 'fa-chart-bar',
        'text' => 'Reportes',
        'submenu' => [
            ['text' => 'Reporte de Ingresos', 'link' => BASE_URL . '/Vistas/html/reporte_ingresos.php'],
            ['text' => 'Reporte De Miembros', 'link' => BASE_URL . '/Vistas/html/reporte_miembros.php'],
            ['text' => 'Reporte de Eventos', 'link' => BASE_URL . '/Vistas/html/reporte_eventos.php'],
            ['text' => 'Reporte de Gastos', 'link' => BASE_URL . '/Vistas/html/reporte_gastos.php'],
        ]
    ],
    ['icon' => 'fa-user', 'text' => 'Usuarios', 'link' => BASE_URL . '/Vistas/html/Usuarios.php'],
    ['icon' => 'fa-cog', 'text' => 'Configuración', 'link' => BASE_URL . '/Vistas/html/configuracion.php'],
];

// Función para generar el HTML del menú de forma recursiva
function generate_menu($items, $level = 0, $parent_id = '')
{
    $html = '<ul class="nav flex-column' . ($level > 0 ? ' submenu' : '') . '">';

    foreach ($items as $item) {
        $has_submenu = isset($item['submenu']);
        // Generamos un ID único para cada submenú
        $item_id = 'submenu-' . sanitize_id($item['text'] . '-' . uniqid());
        $is_active = is_active($item['link'] ?? '#');

        $html .= '<li class="nav-item">';

        if ($has_submenu) {
            // Para elementos con submenú, usamos data-bs-target en lugar de href
            $html .= '<a class="nav-link ' . $is_active . ' has-submenu" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#' . $item_id . '" 
                        role="button" 
                        aria-expanded="false" 
                        aria-controls="' . $item_id . '">';
        } else {
            $html .= '<a class="nav-link ' . $is_active . '" href="' . ($item['link'] ?? '#') . '">';
        }

        $html .= '<i class="fas ' . ($item['icon'] ?? 'fa-circle') . ' menu-icon"></i>';
        $html .= '<span class="menu-text">' . htmlspecialchars($item['text']) . '</span>';

        if ($has_submenu) {
            $html .= '<i class="fas fa-chevron-right submenu-icon ms-auto"></i>';
        }

        $html .= '</a>';

        if ($has_submenu) {
            // El div collapse debe estar fuera del enlace pero dentro del li
            $html .= '<div id="' . $item_id . '" class="collapse submenu-collapse">';
            $html .= generate_menu($item['submenu'], $level + 1, $item_id);
            $html .= '</div>';
        }

        $html .= '</li>';
    }

    $html .= '</ul>';
    return $html;
}
?>
<!-- Estructura HTML del Sidebar -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <?php echo generate_menu($menu_items); ?>
    </div>
</nav>

<!-- Script para la funcionalidad del Sidebar -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const submenuLinks = document.querySelectorAll('.has-submenu');

        // Crear un objeto para almacenar las instancias de Collapse
        const collapseInstances = {};

        submenuLinks.forEach(function (link) {
            const targetId = link.getAttribute('data-bs-target');
            const submenuCollapse = document.querySelector(targetId);
            const submenuIcon = link.querySelector('.submenu-icon');

            // Crear una instancia de Collapse para cada submenú
            collapseInstances[targetId] = new bootstrap.Collapse(submenuCollapse, {
                toggle: false // Inicialmente todos cerrados
            });

            // Agregar los event listeners una sola vez al elemento collapse
            submenuCollapse.addEventListener('show.bs.collapse', function () {
                submenuIcon.style.transform = 'rotate(90deg)';
            });

            submenuCollapse.addEventListener('hide.bs.collapse', function () {
                submenuIcon.style.transform = 'rotate(0deg)';
            });

            // Manejar el clic en el enlace
            link.addEventListener('click', function (e) {
                e.preventDefault(); // Prevenir el comportamiento por defecto

                // Cerrar todos los otros submenús
                submenuLinks.forEach(function (otherLink) {
                    const otherId = otherLink.getAttribute('data-bs-target');
                    if (otherId !== targetId && collapseInstances[otherId]) {
                        collapseInstances[otherId].hide();
                    }
                });

                // Toggle el submenú actual
                collapseInstances[targetId].toggle();
            });
        });
    });
</script>