<?php
require_once 'conexion.php';

class Sidebar {
    private $baseUrl = '/ProyectoIglesiaAd';
    private $menuItems = [
        [
            'icon' => 'fas fa-home',
            'text' => 'Inicio',
            'link' => '/ProyectoIglesiaAd/includes/index.php'
        ],
        [
            'icon' => 'fas fa-users',
            'text' => 'Miembros',
            'submenu' => [
                ['text' => 'Familias', 'link' => '/ProyectoIglesiaAd/includes/Familias.php'],
                ['text' => 'Miembros', 'link' => '/ProyectoIglesiaAd/includes/miembros.php'],
                ['text' => 'Importar Miembros', 'link' => '/ProyectoIglesiaAd/includes/ImportarMiembros.php']
            ]
        ],
        [
            'icon' => 'fas fa-sitemap',
            'text' => 'Células Familiares',
            'link' => '/ProyectoIglesiaAd/includes/CelulasFamiliares.php'
        ],
        [
            'icon' => 'fas fa-calendar-alt',
            'text' => 'Eventos y Seminarios',
            'submenu' => [
                ['text' => 'Crear', 'link' => '/ProyectoIglesiaAd/includes/CrearEvento.php'],
                ['text' => 'Mostrar', 'link' => '/ProyectoIglesiaAd/includes/MostrarEventos.php']
            ]
        ],
        [
            'icon' => 'fas fa-clipboard-check',
            'text' => 'Asistencia',
            'submenu' => [
                ['text' => 'Crear', 'link' => '/ProyectoIglesiaAd/includes/CrearAsistencia.php'],
                ['text' => 'Mostrar', 'link' => '/ProyectoIglesiaAd/includes/MostrarAsistencia.php']
            ]
        ],
        [
            'icon' => 'fas fa-money-bill-wave',
            'text' => 'Tesorería',
            'submenu' => [
                ['text' => 'Categoría de Ingresos', 'link' => '/ProyectoIglesiaAd/includes/CategoriaIngresos.php'],
                ['text' => 'Control de Ofrendas', 'link' => '/ProyectoIglesiaAd/includes/IngresoOfrendas.php'],
                ['text' => 'Control de Diezmos', 'link' => '/ProyectoIglesiaAd/includes/IngresoDiezmos.php'],
                ['text' => 'Tipos de Gastos', 'link' => '/ProyectoIglesiaAd/includes/TipoGastos.php'],
                ['text' => 'Control de Gastos', 'link' => '/ProyectoIglesiaAd/includes/ControlGastos.php']
            ]
        ],
        [
            'icon' => 'fas fa-couch',
            'text' => 'Bienes y Muebles',
            'link' => '/ProyectoIglesiaAd/includes/BienesMuebles.php'
        ],
        [
            'icon' => 'fas fa-chart-bar',
            'text' => 'Reportes',
            'submenu' => [
                ['text' => 'Reporte de Ingresos', 'link' => '/ProyectoIglesiaAd/includes/ReporteIngresos.php'],
                ['text' => 'Reporte de Miembros', 'link' => '/ProyectoIglesiaAd/includes/ReporteMiembros.php'],
                ['text' => 'Reporte de Eventos', 'link' => '/ProyectoIglesiaAd/includes/ReporteEventos.php'],
                ['text' => 'Reporte de Gastos', 'link' => '/ProyectoIglesiaAd/includes/ReporteGastos.php']
            ]
        ],
        [
            'icon' => 'fas fa-user',
            'text' => 'Usuarios',
            'link' => '/ProyectoIglesiaAd/includes/Usuarios.php'
        ],
        [
            'icon' => 'fas fa-cog',
            'text' => 'Configuración',
            'link' => '/ProyectoIglesiaAd/includes/Configuracion.php'
        ]
    ];

    public function render() {
        ?>
<nav class="sidebar">
    <div class="sidebar-header">
        <h3>Menú</h3>
        <button class="btn btn-link d-md-none" id="sidebarCollapse">
            <i class="fas fa-bars text-white"></i>
        </button>
    </div>
    <?php echo $this->generateMenu($this->menuItems); ?>
</nav>
<?php
    }

    private function generateMenu($items, $level = 0) {
        $html = '<ul class="nav flex-column' . ($level > 0 ? ' submenu collapse' : '') . '">';
        
        foreach ($items as $item) {
            $hasSubmenu = isset($item['submenu']);
            $submenuId = $hasSubmenu ? 'submenu-' . $this->sanitizeId($item['text']) : '';
            $isActive = $this->isActive($item['link'] ?? '');
            
            $html .= '<li class="nav-item">';
            $html .= '<a class="nav-link ' . ($isActive ? 'active' : '') . 
                    ($hasSubmenu ? ' has-submenu' : '') . '" ' .
                    'href="' . ($hasSubmenu ? '#' . $submenuId : $item['link']) . '" ' .
                    ($hasSubmenu ? 'data-bs-toggle="collapse" aria-expanded="false" ' .
                    'aria-controls="' . $submenuId . '"' : '') . '>';
            
            if (isset($item['icon'])) {
                $html .= '<i class="' . $item['icon'] . '"></i>';
            }
            
            $html .= '<span>' . $item['text'] . '</span>';
            
            if ($hasSubmenu) {
                $html .= '<i class="fas fa-chevron-right submenu-toggle"></i>';
            }
            
            $html .= '</a>';
            
            if ($hasSubmenu) {
                $html .= '<div class="collapse" id="' . $submenuId . '">';
                $html .= $this->generateMenu($item['submenu'], $level + 1);
                $html .= '</div>';
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        return $html;
    }

    private function isActive($link) {
        $currentPage = $_SERVER['PHP_SELF'];
        return ($currentPage === $link);
    }

    private function sanitizeId($text) {
        return strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $text));
    }
}