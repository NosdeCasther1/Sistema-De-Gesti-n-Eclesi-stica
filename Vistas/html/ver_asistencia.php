<?php
include 'header.php';
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

$evento_id = $_GET['evento_id'] ?? 0;
$fecha = $_GET['fecha'] ?? '';

if (!$evento_id || !$fecha) {
    // Redirigir si no hay parametros
    echo "<script>window.location.href='asistencia_mostrar.php';</script>";
    exit;
}

// Obtener info del evento y los asistentes
$query = "SELECT m.miembro_id, m.nombres, m.apellidos, m.tel_celular, m.email, e.nombre_evento, a.fecha_asistencia 
          FROM asistencia a
          JOIN miembros m ON a.miembro_id = m.miembro_id
          JOIN eventos e ON a.evento_id = e.evento_id
          WHERE a.evento_id = ? AND DATE(a.fecha_asistencia) = ?
          ORDER BY m.apellidos, m.nombres";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $evento_id, $fecha);
$stmt->execute();
$result = $stmt->get_result();

$asistentes = [];
$nombre_evento = "";
$fecha_asistencia = $fecha;

while ($row = $result->fetch_assoc()) {
    $asistentes[] = $row;
    if (empty($nombre_evento)) {
        $nombre_evento = $row['nombre_evento'];
        $fecha_asistencia = $row['fecha_asistencia'];
    }
}
$stmt->close();
$total_asistentes = count($asistentes);

// Si no hay asistentes pero se enviaron parametros válidos, buscamos el nombre del evento
if (empty($nombre_evento)) {
    $q_ev = "SELECT nombre_evento FROM eventos WHERE evento_id = ?";
    $stmt_ev = $conn->prepare($q_ev);
    $stmt_ev->bind_param("i", $evento_id);
    $stmt_ev->execute();
    $res_ev = $stmt_ev->get_result();
    if ($r = $res_ev->fetch_assoc()) {
        $nombre_evento = $r['nombre_evento'];
    }
    $stmt_ev->close();
}
?>

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid py-4 px-4">

            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="asistencia_mostrar.php"
                            class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-1"></i>Listado de
                            Asistencias</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detalles del Evento</li>
                </ol>
            </nav>

            <div class="card shadow-sm border-0 mb-4"
                style="border-radius: 12px; overflow: hidden; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="card-body p-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase fw-bold mb-2"
                            style="letter-spacing: 1px; font-size: 0.8rem; color: rgba(255,255,255,0.8);">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?php echo date('d M Y', strtotime($fecha_asistencia)); ?>
                        </div>
                        <h2 class="fw-bold mb-0">
                            <?php echo htmlspecialchars($nombre_evento ?: 'Evento Desconocido'); ?>
                        </h2>
                    </div>
                    <div class="text-end">
                        <div class="bg-white text-primary rounded-circle d-flex justify-content-center align-items-center mx-auto mb-2 shadow-sm"
                            style="width: 60px; height: 60px;">
                            <span class="h3 fw-bold mb-0">
                                <?php echo $total_asistentes; ?>
                            </span>
                        </div>
                        <span class="text-uppercase fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Asistentes
                            Confirmados</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark mb-0">Directorio de Asistentes</h5>
                <div>
                    <!-- Botón para reportes si se necesita en un futuro -->
                </div>
            </div>

            <div class="row g-4">
                <?php
                $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                foreach ($asistentes as $miembro):
                    $initial = strtoupper(substr($miembro['nombres'], 0, 1));
                    $avatarColor = $colors[crc32($miembro['miembro_id']) % count($colors)];
                    $nombreCompleto = htmlspecialchars($miembro['apellidos'] . ', ' . $miembro['nombres']);
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <div class="card border-0 shadow-sm h-100 contact-card"
                            style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;">
                            <div class="card-body p-4 text-center">
                                <div class="text-white rounded-circle d-flex justify-content-center align-items-center mx-auto mb-3 shadow-sm"
                                    style="width: 70px; height: 70px; font-weight: bold; font-size: 1.8rem; background-color: <?php echo $avatarColor; ?>;">
                                    <?php echo $initial; ?>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?php echo $nombreCompleto; ?>">
                                    <?php echo $nombreCompleto; ?>
                                </h6>
                                <p class="text-muted small mb-3">ID: #
                                    <?php echo str_pad($miembro['miembro_id'], 4, '0', STR_PAD_LEFT); ?>
                                </p>

                                <div class="d-flex justify-content-center gap-2 mt-auto">
                                    <?php if (!empty($miembro['tel_celular'])): ?>
                                        <a href="tel:<?php echo htmlspecialchars($miembro['tel_celular']); ?>"
                                            class="btn btn-light rounded-circle text-success shadow-sm"
                                            style="width: 35px; height: 35px; padding: 0; line-height: 35px;" title="Llamar">
                                            <i class="fas fa-phone-alt"></i>
                                        </a>
                                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $miembro['tel_celular']); ?>"
                                            target="_blank" class="btn btn-light rounded-circle text-success shadow-sm"
                                            style="width: 35px; height: 35px; padding: 0; line-height: 35px;" title="WhatsApp">
                                            <i class="fab fa-whatsapp fs-5 mt-1"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="btn btn-light rounded-circle text-muted opacity-50 shadow-sm"
                                            style="width: 35px; height: 35px; padding: 0; line-height: 35px; cursor: not-allowed;"
                                            title="Sin número">
                                            <i class="fas fa-phone-slash"></i>
                                        </span>
                                    <?php endif; ?>

                                    <?php if (!empty($miembro['email'])): ?>
                                        <a href="mailto:<?php echo htmlspecialchars($miembro['email']); ?>"
                                            class="btn btn-light rounded-circle text-primary shadow-sm"
                                            style="width: 35px; height: 35px; padding: 0; line-height: 35px;"
                                            title="Enviar Correo">
                                            <i class="far fa-envelope"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="btn btn-light rounded-circle text-muted opacity-50 shadow-sm"
                                            style="width: 35px; height: 35px; padding: 0; line-height: 35px; cursor: not-allowed;"
                                            title="Sin correo">
                                            <i class="far fa-envelope"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($total_asistentes == 0): ?>
                    <div class="col-12 text-center py-5 bg-white rounded-3 shadow-sm border-0">
                        <i class="fas fa-user-slash fa-3x text-muted opacity-25 mb-3"></i>
                        <h5 class="fw-bold text-dark">Sin Asistentes Reportados</h5>
                        <p class="text-muted mb-0">No hay información de miembros para este evento y fecha seleccionada.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<style>
    .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>
</body>

</html>