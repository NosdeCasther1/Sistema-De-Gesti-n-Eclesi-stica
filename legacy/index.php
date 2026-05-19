<?php

declare(strict_types=1);

/**
 * Punto de entrada único (Front Controller) para ProyectoIglesia.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Nosde\ProyectoIglesia\Router;

try {
    // Inicializar el enrutador con la base de la URL del proyecto
    $router = new Router('/ProyectoIglesia');

    // --- RUTAS DE VISTAS (URLs AMIGABLES) ---
    // Definimos las vistas que pueden recibir tanto GET (ver) como POST (procesar formularios)
    $vistas = [
        '/login' => '/Vistas/html/Login.php',
        '/logout' => '/Vistas/html/logout.php',
        '/inicio' => '/Vistas/html/Principal.php',
        '/miembros' => '/Vistas/html/Miembros.php',
        '/miembros/importar' => '/Vistas/html/ImportarMiembros.php',
        '/familias' => '/Vistas/html/Familias.php',
        '/usuarios' => '/Vistas/html/Usuarios.php',
        '/celulas' => '/Vistas/html/CelulasFamiliares.php',
        '/bienes' => '/Vistas/html/BienesMuebles.php',
        '/tesoreria/diezmos' => '/Vistas/html/IngresoDiezmos.php',
        '/tesoreria/ofrendas' => '/Vistas/html/IngresoOfrendas.php',
        '/tesoreria/otros' => '/Vistas/html/IngresoOtros.php',
        '/tesoreria/gastos' => '/Vistas/html/ControlGastos.php',
        '/tesoreria/categorias/ingresos' => '/Vistas/html/CategoriaIngresos.php',
        '/tesoreria/categorias/gastos' => '/Vistas/html/TipoGastos.php',
        '/reportes/balance' => '/Vistas/html/reporte_balance.php',
        '/reportes/ingresos' => '/Vistas/html/reporte_ingresos.php',
        '/reportes/miembros' => '/Vistas/html/reporte_miembros.php',
        '/reportes/eventos' => '/Vistas/html/reporte_eventos.php',
        '/reportes/gastos' => '/Vistas/html/reporte_gastos.php',
        '/eventos/crear' => '/Vistas/html/CrearEvento.php',
        '/eventos/ver' => '/Vistas/html/MostrarEventos.php',
        '/asistencia/crear' => '/Vistas/html/CrearAsistencia.php',
        '/asistencia/ver' => '/Vistas/html/asistencia_mostrar.php',
        '/configuracion' => '/Vistas/html/configuracion.php',
        '/acceso-denegado' => '/Vistas/html/acceso_denegado.php',
    ];

    foreach ($vistas as $route => $file) {
        $router->add('GET', $route, function() use ($file) { require_once __DIR__ . $file; });
        $router->add('POST', $route, function() use ($file) { require_once __DIR__ . $file; });
    }

    $router->get('/', function() {
        header("Location: /ProyectoIglesia/login");
        exit;
    });

    $router->get('/seguridad/grupos', function() { 
        $_GET['tab'] = 'grupos';
        require_once __DIR__ . '/Vistas/html/configuracion.php'; 
    });

    // --- RUTAS DE SERVICIOS INTERNOS (API MÍNIMA) ---
    // Prefijo '/_/' para mayor brevedad y apariencia de sistema

    // Grupos (g)
    $router->add('GET', '/_/g', 'GruposUsuariosController@index');
    $router->add('GET', '/_/g/perm', 'GruposUsuariosController@getPermisos');
    $router->add('POST', '/_/g/save', 'GruposUsuariosController@save');
    $router->add('POST', '/_/g/del', 'GruposUsuariosController@delete');
    $router->add('POST', '/_/g/perm/save', 'GruposUsuariosController@savePermisos');

    // Usuarios (u)
    $router->add('GET', '/_/u', 'UsuarioController@index');
    $router->add('POST', '/_/u/save', 'UsuarioController@store');
    $router->add('POST', '/_/u/del', 'UsuarioController@delete');
    $router->add('POST', '/_/u/status', 'UsuarioController@toggleStatus');

    // Miembros (m)
    $router->add('GET', '/_/m', 'MiembroController@index');
    $router->add('POST', '/_/m/save', 'MiembroController@store');
    $router->add('POST', '/_/m/del', 'MiembroController@delete');
    $router->add('GET', '/_/m/stats', 'MiembroController@contribuciones');
    $router->add('GET', '/_/m/form-data', 'MiembroController@getFormData');
    $router->get('/_/m/carnet', 'MiembroController@generarCarnet');

    // Familias (f)
    $router->add('GET', '/_/f', 'FamiliaController@index');
    $router->add('POST', '/_/f/save', 'FamiliaController@store');
    $router->add('POST', '/_/f/del', 'FamiliaController@delete');

    // Sistema / Configuración
    $router->post('/configuracion/guardar', function() {
        require_once __DIR__ . '/Vistas/html/guardar_configuracion.php';
    });

    // --- Servicios Compartidos (API) ---
    $router->post('/_/validar_admin', function() { require_once __DIR__ . '/Vistas/html/validar_admin.php'; });
    $router->get('/_/buscar_miembros', function() { require_once __DIR__ . '/Vistas/html/buscar_miembros.php'; });
    $router->get('/_/obtener_asistencias', function() { require_once __DIR__ . '/Vistas/html/obtener_asistencias.php'; });
    $router->get('/_/obtener_calendario', function() { require_once __DIR__ . '/Vistas/html/obtener_calendario.php'; });
    $router->post('/_/procesar_restore', function() { require_once __DIR__ . '/Vistas/html/procesar_restore.php'; });
    $router->get('/_/generar_backup', function() { require_once __DIR__ . '/Vistas/html/generar_backup.php'; });

    // --- Reportes PDF (Directos) ---
    $router->get('/exportar_pdf.php', function() { require_once __DIR__ . '/Vistas/html/exportar_pdf.php'; });
    $router->get('/exportar_pdf_eventos.php', function() { require_once __DIR__ . '/Vistas/html/exportar_pdf_eventos.php'; });
    $router->get('/exportar_pdf_celulas.php', function() { require_once __DIR__ . '/Vistas/html/exportar_pdf_celulas.php'; });

    // Despachar la ruta
    $router->dispatch();

} catch (Exception $e) {
    $code = $e->getCode() ?: 500;
    if ($code < 100 || $code > 599) $code = 500;
    http_response_code($code);
    
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } else {
        echo "<h1>Error {$code}</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
