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
    ['icon' => 'fa-home', 'text' => 'Inicio', 'link' => BASE_URL . '/inicio'],
    [
        'icon' => 'fa-users',
        'text' => 'Miembros',
        'modulo' => 'miembros',
        'submenu' => [
            ['text' => 'Familias', 'link' => BASE_URL . '/familias'],
            ['text' => 'Miembros', 'link' => BASE_URL . '/miembros'],
            ['text' => 'Importar Miembros', 'link' => BASE_URL . '/miembros/importar'],
        ]
    ],
    ['icon' => 'fa-sitemap', 'text' => 'Células Familiares', 'link' => BASE_URL . '/celulas', 'modulo' => 'celulas'],
    [
        'icon' => 'fa-calendar-alt',
        'text' => 'Eventos y Seminarios',
        'modulo' => 'eventos',
        'submenu' => [
            ['text' => 'Crear', 'link' => BASE_URL . '/eventos/crear'],
            ['text' => 'Mostrar', 'link' => BASE_URL . '/eventos/ver'],
        ]
    ],
    [
        'icon' => 'fa-clipboard-check',
        'text' => 'Asistencia',
        'submenu' => [
            ['text' => 'Crear', 'link' => BASE_URL . '/asistencia/crear'],
            ['text' => 'Mostrar', 'link' => BASE_URL . '/asistencia/ver'],
        ]
    ],
    [
        'icon' => 'fa-money-bill-wave',
        'text' => 'Tesorería',
        'modulo' => 'tesoreria',
        'submenu' => [
            ['text' => 'Diezmos', 'link' => BASE_URL . '/tesoreria/diezmos'],
            ['text' => 'Ofrendas', 'link' => BASE_URL . '/tesoreria/ofrendas'],
            ['text' => 'Otros Ingresos', 'link' => BASE_URL . '/tesoreria/otros'],
            ['text' => 'Gastos', 'link' => BASE_URL . '/tesoreria/gastos'],
            ['text' => 'Categoría de Ingresos', 'link' => BASE_URL . '/tesoreria/categorias/ingresos'],
            ['text' => 'Categoría de Gastos', 'link' => BASE_URL . '/tesoreria/categorias/gastos'],
        ]
    ],
    ['icon' => 'fa-couch', 'text' => 'Bienes y Muebles', 'link' => BASE_URL . '/bienes', 'modulo' => 'bienes'],
    [
        'icon' => 'fa-chart-bar',
        'text' => 'Reportes',
        'modulo' => 'reportes',
        'submenu' => [
            ['text' => 'Balance General', 'link' => BASE_URL . '/reportes/balance'],
            ['text' => 'Ingresos', 'link' => BASE_URL . '/reportes/ingresos'],
            ['text' => 'Miembros', 'link' => BASE_URL . '/reportes/miembros'],
            ['text' => 'Eventos', 'link' => BASE_URL . '/reportes/eventos'],
            ['text' => 'Gastos', 'link' => BASE_URL . '/reportes/gastos'],
        ]
    ],
    [
        'icon' => 'fa-shield-alt',
        'text' => 'Seguridad',
        'modulo' => 'configuracion',
        'submenu' => [
            ['text' => 'Usuarios', 'link' => BASE_URL . '/usuarios'],
            ['text' => 'Grupos y Permisos', 'link' => BASE_URL . '/seguridad/grupos'],
        ]
    ],
    ['icon' => 'fa-cog', 'text' => 'Configuración', 'link' => BASE_URL . '/configuracion', 'modulo' => 'configuracion'],
];

// Función para generar el HTML del menú de forma recursiva
function generate_menu($items, $level = 0, $parent_container_id = 'sidebarMenu')
{
    // El contenedor principal del acordeón solo en el nivel 0
    $html = '<ul class="nav flex-column' . ($level > 0 ? ' submenu' : '') . '" ' . ($level === 0 ? 'id="' . $parent_container_id . '"' : '') . '>';

    foreach ($items as $item) {
        // Comprobación de permisos
        if (isset($item['modulo']) && class_exists('Permisos') && !Permisos::puede('view', $item['modulo'])) {
            continue;
        }

        $has_submenu = isset($item['submenu']);
        $item_id = 'menu-' . sanitize_id($item['text'] . '-' . bin2hex(random_bytes(2)));
        $is_active = is_active($item['link'] ?? '#');

        $html .= '<li class="nav-item">';

        if ($has_submenu) {
            // Elemento PADRE con Toggle de Bootstrap
            $html .= '<a class="nav-link ' . $is_active . ' has-submenu d-flex align-items-center" 
                        href="#' . $item_id . '" 
                        data-bs-toggle="collapse" 
                        role="button" 
                        aria-expanded="false" 
                        aria-controls="' . $item_id . '">';
        } else {
            // Elemento SIMPLE
            $html .= '<a class="nav-link ' . $is_active . ' d-flex align-items-center" href="' . ($item['link'] ?? '#') . '">';
        }

        // Icono y Texto
        $html .= '<i class="fas ' . ($item['icon'] ?? 'fa-circle') . ' menu-icon me-2"></i>';
        $html .= '<span class="menu-text">' . htmlspecialchars($item['text']) . '</span>';

        // Inyectar Chevron dinámicamente si tiene hijos
        if ($has_submenu) {
            $html .= '<i class="fas fa-chevron-right submenu-icon ms-auto"></i>';
        }

        $html .= '</a>';

        if ($has_submenu) {
            // Contenedor COLLAPSE para los hijos (Comportamiento Acordeón)
            $html .= '<div id="' . $item_id . '" class="collapse submenu-collapse" data-bs-parent="#' . $parent_container_id . '">';
            // Llamada recursiva incrementando nivel
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
<aside id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <?php echo generate_menu($menu_items); ?>
    </div>
</aside>

<!-- Funcionalidad de Sidebar manejada nativamente por Bootstrap 5 -->