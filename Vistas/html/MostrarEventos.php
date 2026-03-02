<?php
include 'header.php';

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible: " . mysqli_connect_error());
}

// Consulta para obtener todos los eventos
$query = "SELECT * FROM eventos ORDER BY fecha_inicio DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Cartelera de Eventos</h1>
                    <p class="text-muted">Consulta, filtra y administra todas las actividades y eventos programados.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="reporte_eventos.php" class="btn btn-secondary px-4 py-2" style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-print me-2"></i> Reporte
                    </a>
                    <a href="CrearEvento.php" class="btn btn-primary px-4 py-2"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-calendar-plus me-2"></i> Nuevo Evento
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-body p-4">
                    <!-- Buscador Unificado -->
                    <div class="row mb-4 bg-light p-3 rounded align-items-end" style="border: 1px solid #f0f0f0;">
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold mb-1"><i
                                    class="fas fa-search me-1"></i>Buscador de Eventos</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i
                                        class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0 search-input"
                                    placeholder="Buscar por Nombre del Evento o Lugar..."
                                    style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Detalles del Evento</th>
                                    <th style="width: 20%;">Inicio</th>
                                    <th style="width: 20%;">Fin</th>
                                    <th style="width: 15%; text-align: center;">Estado</th>
                                    <th style="width: 15%; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)):
                                    $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                                    $initial = strtoupper(substr($row['nombre_evento'], 0, 1));
                                    $avatarColor = $colors[crc32($row['evento_id']) % count($colors)];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="text-white rounded me-3 d-flex justify-content-center align-items-center shadow-sm flex-shrink-0"
                                                    style="width: 48px; height: 48px; font-weight: bold; font-size: 1.4rem; background-color: <?php echo $avatarColor; ?>;">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark fs-6 text-truncate"
                                                        style="max-width: 250px;"
                                                        title="<?php echo htmlspecialchars($row['nombre_evento']); ?>">
                                                        <?php echo htmlspecialchars($row['nombre_evento']); ?>
                                                    </div>
                                                    <div class="small text-muted text-truncate" style="max-width: 250px;">
                                                        <i
                                                            class="fas fa-map-marker-alt text-danger opacity-75 me-1"></i><?php echo htmlspecialchars($row['lugar']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="text-dark fw-bold small"><i
                                                    class="far fa-calendar-alt text-primary opacity-75 me-1"></i>
                                                <?php echo date('d M Y', strtotime($row['fecha_inicio'])); ?></div>
                                            <div class="text-muted small"><i class="far fa-clock opacity-50 me-1"></i>
                                                <?php echo date('h:i A', strtotime($row['fecha_inicio'])); ?></div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="text-dark fw-bold small"><i
                                                    class="far fa-calendar-check text-success opacity-75 me-1"></i>
                                                <?php echo date('d M Y', strtotime($row['fecha_fin'])); ?></div>
                                            <div class="text-muted small"><i class="far fa-clock opacity-50 me-1"></i>
                                                <?php echo date('h:i A', strtotime($row['fecha_fin'])); ?></div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php
                                            $estado = strtolower($row['estado']);
                                            if ($estado == 'programado'): ?>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="far fa-calendar me-1"></i> Programado
                                                </span>
                                            <?php elseif ($estado == 'en curso'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="fas fa-play me-1"></i> En Curso
                                                </span>
                                            <?php elseif ($estado == 'finalizado'): ?>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="fas fa-check-double me-1"></i> Finalizado
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="fas fa-ban me-1"></i> Cancelado
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle flex-nowrap">
                                            <a href="editar_evento.php?id=<?php echo $row['evento_id']; ?>"
                                                class="btn btn-sm btn-action btn-action-edit text-primary me-1"
                                                title="Editar"
                                                style="background: rgba(78, 115, 223, 0.1); border-radius: 6px;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="eliminar_evento.php?id=<?php echo $row['evento_id']; ?>"
                                                class="btn btn-sm btn-action btn-action-delete text-danger" title="Eliminar"
                                                style="background: rgba(231, 74, 59, 0.1); border-radius: 6px;"
                                                onclick="return confirm('¿Está seguro de que desea eliminar este evento de forma definitiva?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        const $searchInput = $('.search-input');

        $searchInput.on('keyup', function () {
            const searchTerm = $(this).val().toLowerCase();

            $('.table-softwys tbody tr').each(function () {
                const $row = $(this);
                // Search by Name or Location (Columns 0)
                const nameAndLocation = $row.find('td:eq(0)').text().toLowerCase();
                $row.toggle(nameAndLocation.includes(searchTerm));
            });
        });

        $searchInput.on('search', function () {
            if ($(this).val() === '') {
                $('.table-softwys tbody tr').show();
            }
        });
    });

    function initializePagination() {
        const rowsPerPage = 10;
        const table = document.querySelector('.table-softwys');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        const pageCount = Math.ceil(rows.length / rowsPerPage);

        if (pageCount <= 1) return;

        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
        }

        if (!document.querySelector('.pagination-container')) {
            const paginationContainer = document.createElement('div');
            paginationContainer.className = 'd-flex justify-content-end mt-4 pagination-container';

            let html = '<ul class="pagination pagination-sm shadow-sm opacity-75">';
            for (let i = 1; i <= pageCount; i++) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); window.changeListPage(${i})">${i}</a></li>`;
            }
            html += '</ul>';
            paginationContainer.innerHTML = html;

            table.parentNode.appendChild(paginationContainer);
        }

        window.changeListPage = showPage;
        showPage(1);
    }

    document.addEventListener('DOMContentLoaded', initializePagination);
</script>

<?php
mysqli_free_result($result);
mysqli_close($conn);
include 'footer.php';
?>