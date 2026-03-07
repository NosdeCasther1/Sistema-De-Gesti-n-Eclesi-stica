<?php
include 'header.php';
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// LOGICA BORRAR ASISTENCIA
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_asistencia') {
    $fecha = $_POST['fecha_asistencia'];
    $evento_id = (int) $_POST['evento_id'];

    // Asistencia se borra permanentemente (hard delete en asistencia)
    $query = "DELETE FROM asistencia WHERE fecha_asistencia = ? AND evento_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $fecha, $evento_id);
    if ($stmt->execute()) {
        $mensaje = "El pase de lista ha sido eliminado correctamente.";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Error al intentar eliminar el pase de lista.";
        $tipo_mensaje = "danger";
    }
    $stmt->close();
}

// Obtener todas las sesiones de asistencia agrupadas
$query = "SELECT a.evento_id, a.fecha_asistencia, e.nombre_evento, COUNT(a.miembro_id) as total_asistentes 
          FROM asistencia a 
          JOIN eventos e ON a.evento_id = e.evento_id 
          GROUP BY a.evento_id, a.fecha_asistencia, e.nombre_evento 
          ORDER BY a.fecha_asistencia DESC";
$result = mysqli_query($conn, $query);
?>

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Listado de Asistencias</h1>
                    <p class="text-muted">Consulta el registro histórico de asistencias a todos los eventos y
                        seminarios.</p>
                </div>
                <a href="CrearAsistencia.php" class="btn btn-primary px-4 py-2 shadow-sm"
                    style="border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-plus me-2"></i> Nuevo Pase de Lista
                </a>
            </div>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show d-flex align-items-center shadow-sm"
                    role="alert" style="border-radius: 10px;">
                    <i
                        class="fas <?php echo $tipo_mensaje == 'danger' ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?> fs-4 me-3"></i>
                    <div>
                        <?php echo $mensaje; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-0">

                    <!-- Barra de Búsqueda Integrada -->
                    <div class="p-4 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <div class="input-group"
                            style="max-width: 400px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <span class="input-group-text bg-white border-0 text-muted px-3"><i
                                    class="fas fa-search"></i></span>
                            <input type="text" id="searchInput" class="form-control border-0 ps-1"
                                placeholder="Buscar por nombre de evento o fecha..." style="box-shadow: none;">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover w-100 mb-0 align-middle table-softwys" id="asistenciaTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 0.8rem; padding: 15px 20px;">Sesión de Asistencia</th>
                                    <th class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 0.8rem; padding: 15px 20px;">Fecha del Pase</th>
                                    <th class="text-secondary fw-bold text-uppercase text-center"
                                        style="font-size: 0.8rem; padding: 15px 20px;">Total Asistentes</th>
                                    <th class="text-secondary fw-bold text-uppercase text-center"
                                        style="font-size: 0.8rem; padding: 15px 20px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                                while ($row = mysqli_fetch_assoc($result)):
                                    $initial = strtoupper(substr($row['nombre_evento'], 0, 1));
                                    $avatarColor = $colors[crc32($row['evento_id']) % count($colors)];
                                    ?>
                                    <tr class="searchable-row" style="transition: all 0.2s ease;">
                                        <td style="padding: 15px 20px;">
                                            <div class="d-flex align-items-center">
                                                <div class="text-white rounded d-flex justify-content-center align-items-center me-3 shadow-sm flex-shrink-0"
                                                    style="width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem; background-color: <?php echo $avatarColor; ?>;">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-dark fw-bold">
                                                        <?php echo htmlspecialchars($row['nombre_evento']); ?></h6>
                                                    <span
                                                        class="text-muted small event-name-hidden d-none"><?php echo strtolower($row['nombre_evento']); ?></span>
                                                </div>
                                            </div>
                                        </td>

                                        <td style="padding: 15px 20px;">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 text-center me-2 border">
                                                    <i class="far fa-calendar-alt text-primary opacity-75"></i>
                                                </div>
                                                <span
                                                    class="fw-bold text-dark date-search"><?php echo date('d M Y', strtotime($row['fecha_asistencia'])); ?></span>
                                            </div>
                                        </td>

                                        <td class="text-center" style="padding: 15px 20px;">
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold"
                                                style="font-size: 0.85rem;">
                                                <i class="fas fa-users me-1"></i> <?php echo $row['total_asistentes']; ?>
                                                asistentes
                                            </span>
                                        </td>

                                        <td class="text-center" style="padding: 15px 20px;">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <a href="ver_asistencia.php?fecha=<?php echo urlencode($row['fecha_asistencia']); ?>&evento_id=<?php echo $row['evento_id']; ?>"
                                                    class="btn btn-sm bg-primary bg-opacity-10 text-primary border-0 rounded-pill px-3 py-2 shadow-sm"
                                                    style="font-weight: 600;">
                                                    <i class="fas fa-eye me-1"></i> Ver Detalles
                                                </a>
                                                <form method="POST"
                                                    action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                                                    class="m-0 p-0 d-inline-block form-delete-asistencia">
                                                    <input type="hidden" name="action" value="delete_asistencia">
                                                    <input type="hidden" name="fecha_asistencia"
                                                        value="<?php echo htmlspecialchars($row['fecha_asistencia']); ?>">
                                                    <input type="hidden" name="evento_id"
                                                        value="<?php echo $row['evento_id']; ?>">
                                                    <button type="button"
                                                        class="btn btn-sm bg-danger bg-opacity-10 text-danger border-0 rounded-pill shadow-sm btn-delete-asistencia pt-2 pb-2 ps-3 pe-3"
                                                        title="Eliminar Pase de Lista" style="font-weight: 600;">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                                <?php if (mysqli_num_rows($result) == 0): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="text-muted">
                                                <div class="mb-3">
                                                    <i class="fas fa-clipboard-list fa-3x opacity-25"></i>
                                                </div>
                                                <h5 class="fw-bold">Sin registros</h5>
                                                <p>Todavía no se ha pasado lista en ningún evento.</p>
                                                <a href="CrearAsistencia.php"
                                                    class="btn btn-primary btn-sm mt-2 rounded-pill px-4">Pasar lista
                                                    ahora</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Mensaje de no resultados en búsqueda -->
                        <div id="noResults" class="text-center py-5 d-none">
                            <i class="fas fa-search fa-3x text-muted opacity-25 mb-3"></i>
                            <h5 class="fw-bold text-dark">No se encontraron coincidencias</h5>
                            <p class="text-muted">Intenta buscar con otro término.</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<style>
    .table-softwys tbody tr:hover td {
        background-color: #f8f9fc !important;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Buscador Dinámico
        $("#searchInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            var matches = 0;

            $("#asistenciaTable tbody tr.searchable-row").filter(function () {
                var eventName = $(this).find(".event-name-hidden").text().toLowerCase();
                var dateStr = $(this).find(".date-search").text().toLowerCase();
                var fullText = eventName + " " + dateStr;

                var isMatch = fullText.indexOf(value) > -1;
                $(this).toggle(isMatch);

                if (isMatch) matches++;
            });

            // Mostrar mensaje de sin resultados
            if (matches === 0 && $("#asistenciaTable tbody tr.searchable-row").length > 0) {
                $("#asistenciaTable").addClass("d-none");
                $("#noResults").removeClass("d-none");
            } else {
                $("#asistenciaTable").removeClass("d-none");
                $("#noResults").addClass("d-none");
            }
        });

        // ==========================
        // SweetAlert2 para Validar Eliminación de Pase de Lista
        // ==========================
        const deleteButtons = document.querySelectorAll('.btn-delete-asistencia');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('.form-delete-asistencia');

                Swal.fire({
                    title: '¿Eliminar Pase de Lista?',
                    text: 'Se eliminarán permanentemente todos los registros de asistencia asociados a esta fecha y evento.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Sí, eliminar registro',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

<?php include 'footer.php'; ?>
</body>

</html>