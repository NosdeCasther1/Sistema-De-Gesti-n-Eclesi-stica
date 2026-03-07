<?php
/**
 * notificaciones.php
 * Servicio que recopila y prepara las notificaciones del panel.
 * 
 * Retorna:
 *  - $notificaciones        array con todas las notificaciones ordenadas
 *  - $cantidadNotificaciones int
 *  - $nombreUsuario         string (nombre corto para el header)
 *  - $nombreUsuarioCompleto string
 *  - $rolUsuario            string
 *  - $iniciales             string (para el avatar)
 *  - $colorAvatar           string (color hex)
 */

require_once __DIR__ . '/../../../Config/conexion.php';

$conn = getDBConnection();

$notificaciones = [];

// ─────────────────────────────────────────────
// Helper: tiempo relativo legible
// ─────────────────────────────────────────────
if (!function_exists('tiempoRelativo')) {
    function tiempoRelativo(string $fechaStr, bool $esFuturo = false): string
    {
        $fecha     = new DateTime($fechaStr);
        $ahora     = new DateTime();
        $diferencia = $ahora->diff($fecha);

        if ($diferencia->y > 0)
            return $esFuturo ? "En {$diferencia->y} año(s)" : "Hace {$diferencia->y} año(s)";
        if ($diferencia->m > 0)
            return $esFuturo ? "En {$diferencia->m} mes(es)" : "Hace {$diferencia->m} mes(es)";
        if ($diferencia->d > 0) {
            if ($diferencia->d === 1)
                return $esFuturo ? "Mañana" : "Ayer";
            return $esFuturo ? "En {$diferencia->d} días" : "Hace {$diferencia->d} días";
        }
        if ($diferencia->h > 0)
            return $esFuturo ? "En {$diferencia->h} hora(s)" : "Hace {$diferencia->h} hora(s)";
        if ($diferencia->i > 0)
            return $esFuturo ? "En {$diferencia->i} min" : "Hace {$diferencia->i} min";

        return "Ahora mismo";
    }
}

// ─────────────────────────────────────────────
// 1. Nuevos Miembros (últimos 7 días)
// ─────────────────────────────────────────────
$resMiembros = mysqli_query($conn,
    "SELECT CONCAT(nombres, ' ', apellidos) AS nombre, fecha_ingreso
     FROM miembros
     WHERE fecha_ingreso >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
     ORDER BY fecha_ingreso DESC LIMIT 3"
);
if ($resMiembros) {
    while ($row = mysqli_fetch_assoc($resMiembros)) {
        $notificaciones[] = [
            'tiempo_real' => $row['fecha_ingreso'],
            'mensaje'     => 'Nuevo miembro: ' . htmlspecialchars($row['nombre']),
            'tiempo'      => tiempoRelativo($row['fecha_ingreso']),
            'icono'       => 'fas fa-user-plus text-primary',
            'bg'          => 'bg-primary',
        ];
    }
}

// ─────────────────────────────────────────────
// 2. Próximos Eventos (próximos 7 días)
// ─────────────────────────────────────────────
$resEventos = mysqli_query($conn,
    "SELECT nombre_evento, fecha_inicio
     FROM eventos
     WHERE fecha_inicio >= CURDATE()
       AND fecha_inicio <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
       AND estado = 'Activo'
     ORDER BY fecha_inicio ASC LIMIT 3"
);
if ($resEventos) {
    while ($row = mysqli_fetch_assoc($resEventos)) {
        $notificaciones[] = [
            'tiempo_real' => $row['fecha_inicio'],
            'mensaje'     => 'Próximo evento: ' . htmlspecialchars($row['nombre_evento']),
            'tiempo'      => tiempoRelativo($row['fecha_inicio'], true),
            'icono'       => 'far fa-calendar-alt text-warning',
            'bg'          => 'bg-warning',
        ];
    }
}

// ─────────────────────────────────────────────
// 3. Cumpleaños (hoy o próximos 7 días)
// ─────────────────────────────────────────────
$resCumpleanos = mysqli_query($conn,
    "SELECT CONCAT(nombres, ' ', apellidos) AS nombre, fecha_nacimiento
     FROM miembros
     WHERE estado = 'Activo' AND fecha_nacimiento IS NOT NULL
     AND (
         DATE_FORMAT(fecha_nacimiento, '%m-%d') BETWEEN DATE_FORMAT(CURDATE(), '%m-%d') AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d')
         OR (MONTH(CURDATE()) = 12 AND MONTH(DATE_ADD(CURDATE(), INTERVAL 7 DAY)) = 1
             AND DATE_FORMAT(fecha_nacimiento, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d'))
         OR (MONTH(CURDATE()) = 12 AND MONTH(DATE_ADD(CURDATE(), INTERVAL 7 DAY)) = 1
             AND DATE_FORMAT(fecha_nacimiento, '%m-%d') <= DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d'))
     ) LIMIT 3"
);
if ($resCumpleanos) {
    while ($row = mysqli_fetch_assoc($resCumpleanos)) {
        $fechaNac      = new DateTime($row['fecha_nacimiento']);
        $hoy           = new DateTime();
        $cumpleEsteAno = new DateTime($hoy->format('Y') . '-' . $fechaNac->format('m-d'));

        if ($cumpleEsteAno < $hoy && $hoy->format('m') === '12') {
            $cumpleEsteAno->modify('+1 year');
        }

        $notificaciones[] = [
            'tiempo_real' => $cumpleEsteAno->format('Y-m-d'),
            'mensaje'     => 'Cumpleaños: ' . htmlspecialchars($row['nombre']),
            'tiempo'      => tiempoRelativo($cumpleEsteAno->format('Y-m-d'), true),
            'icono'       => 'fas fa-birthday-cake text-success',
            'bg'          => 'bg-success',
        ];
    }
}

// ─────────────────────────────────────────────
// Ordenar por proximidad al momento actual y limitar a 5
// ─────────────────────────────────────────────
usort($notificaciones, function ($a, $b) {
    $ahora = time();
    return abs($ahora - strtotime($a['tiempo_real'])) - abs($ahora - strtotime($b['tiempo_real']));
});
$notificaciones        = array_slice($notificaciones, 0, 5);
$cantidadNotificaciones = count($notificaciones);

// ─────────────────────────────────────────────
// Datos del usuario autenticado (sesión)
// ─────────────────────────────────────────────
$nombreUsuarioCompleto = $_SESSION['nombres'] ?? 'Usuario Desconocido';
$partes               = explode(' ', trim($nombreUsuarioCompleto), 2);
$nombreUsuario        = trim(($partes[0] ?? '') . ' ' . (explode(' ', $partes[1] ?? '')[0] ?? ''));
$rolUsuario           = isset($_SESSION['rol']) ? ucfirst(strtolower($_SESSION['rol'])) : 'Invitado';

// Iniciales del avatar (máx 2 letras)
$iniciales = '';
foreach (explode(' ', $nombreUsuario) as $palabra) {
    if ($palabra !== '') $iniciales .= strtoupper($palabra[0]);
}
$iniciales = substr($iniciales, 0, 2);

// Color consistente basado en el nombre
$coloresAvatar = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14'];
$colorAvatar   = $coloresAvatar[abs(crc32($nombreUsuario)) % count($coloresAvatar)];
