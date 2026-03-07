<?php
/**
 * ControlGastos.php — Vista
 * La lógica de negocio vive en components/control_gastos_logica.php
 */
include __DIR__ . '/header.php';
require_once __DIR__ . '/components/control_gastos_logica.php';
?>

<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Control de Gastos</h1>
                    <p class="text-muted">Administre los egresos, asigne referencias y clasifique sus gastos.</p>
                </div>
                <div>
                    <button class="btn btn-secondary px-3 py-2 me-2" style="border-radius: 8px; font-weight: 500;"
                        onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Reporte
                    </button>
                    <button class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                        onclick="addGasto()">
                        <i class="fas fa-plus me-2"></i>Nuevo Gasto
                    </button>
                </div>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo strpos($mensaje, 'exitosamente') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show shadow-sm"
                    role="alert">
                    <i
                        class="fas <?php echo strpos($mensaje, 'exitosamente') !== false ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($print_receipt_data)): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        setTimeout(() => {
                            Swal.fire({
                                title: '¡Guardado Exitosamente!',
                                html: "<?php echo $mensaje; ?><br><br>¿Desea imprimir el comprobante de este gasto?",
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#0d6efd',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: '<i class="fas fa-print me-2"></i>Imprimir Comprobante',
                                cancelButtonText: 'No imprimir',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    imprimirComprobante(<?php echo $print_receipt_data; ?>);
                                }
                            });
                        }, 200);
                    });
                </script>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row mb-4 bg-light p-3 rounded" style="border: 1px solid #f0f0f0;">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <label class="form-label text-muted small fw-bold mb-1">Buscar Gasto</label>
                            <div class="input-group"
                                style="box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-radius: 8px; overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="searchInput"
                                    placeholder="Buscar por referencia o tipo..." style="box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100" id="tabla-gastos">
                            <thead>
                                <tr>
                                    <th>Cód. Referencia</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Tipo de Gasto</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_gastos = 0;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $total_gastos += $row['monto'];
                                    ?>
                                    <tr>
                                        <td class="text-muted fw-bold small"><i
                                                class="fas fa-receipt me-1"></i><?php echo $row['referencia']; ?></td>
                                        <td class="text-muted"><i
                                                class="far fa-calendar-alt me-1"></i><?php echo date('d/m/Y', strtotime($row['fecha'])); ?>
                                        </td>
                                        <td class="text-danger fw-bold">Q <?php echo number_format($row['monto'], 2); ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                                <i
                                                    class="fas fa-tag me-1"></i><?php echo htmlspecialchars($row['tipo_gasto_nombre']); ?>
                                            </span>
                                        </td>
                                        <td class="text-muted text-wrap" style="max-width: 250px;">
                                            <?php echo htmlspecialchars($row['descripcion']); ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-action btn-action-edit me-1" title="Editar"
                                                    onclick='editGasto(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-action-delete" title="Eliminar"
                                                    onclick="deleteGasto(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold text-dark pe-4 fs-5">Total de Egresos:</td>
                                    <td class="text-danger fw-bold fs-5">Q <span
                                            id="totalGastos"><?php echo number_format($total_gastos, 2); ?></span></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para agregar/editar gasto -->
<div class="modal fade" id="gastoModal" tabindex="-1" aria-labelledby="gastoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark" id="gastoModalLabel"><i
                        class="fas fa-file-invoice-dollar text-primary me-2"></i>Nuevo Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <form id="gastoForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="gasto_id" name="gasto_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold small">Cód. Referencia</label>
                            <div class="form-control text-primary fw-bold bg-light"
                                style="border-radius: 8px; border: 1px dashed #ced4da;" id="referenciaLabel"></div>
                            <input type="hidden" id="referencia" name="referencia">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label text-muted fw-bold small">Fecha del Gasto:</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required
                                style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_gasto" class="form-label text-muted fw-bold small">Clasificación / Tipo de
                                Gasto:</label>
                            <select class="form-select" id="tipo_gasto" name="tipo_gasto" required
                                style="border-radius: 8px;">
                                <option value="" selected disabled>-- Seleccione --</option>
                                <?php
                                mysqli_data_seek($tipos_result, 0);
                                while ($tipo = mysqli_fetch_assoc($tipos_result)) {
                                    ?>
                                    <option value="<?php echo $tipo['id']; ?>">
                                        <?php echo htmlspecialchars($tipo['nombre']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="monto" class="form-label text-muted fw-bold small">Monto (Q):</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold border-end-0">Q</span>
                                <input type="number" step="0.01"
                                    class="form-control text-danger fw-bold border-start-0 fs-5" id="monto" name="monto"
                                    required style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label text-muted fw-bold small">Descripción del
                            Gasto:</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                            style="border-radius: 8px; resize: none;"></textarea>
                    </div>

                    <div class="text-end border-top pt-4 mt-2">
                        <button type="button" class="btn btn-light px-4 py-2 me-2"
                            style="border-radius: 8px; font-weight: 500;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 py-2"
                            style="border-radius: 8px; font-weight: 500;"><i class="fas fa-save me-2"></i>Guardar
                            Gasto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#tabla-gastos tbody tr');

        rows.forEach(row => {
            const referencia = row.cells[0].textContent.toLowerCase();
            const tipoGasto = row.cells[3].textContent.toLowerCase();

            // Mostrar la fila si la referencia o el tipo de gasto contienen el texto de búsqueda
            const shouldShow = referencia.includes(searchValue) || tipoGasto.includes(searchValue);
            row.style.display = shouldShow ? '' : 'none';
        });
    });

    // Función para editar un gasto
    function deleteGasto(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer. ¿Desea eliminar completamente este gasto?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?delete=' + id;
            }
        });
    }

    // Función para generar referencia hexadecimal en el frontend
    function generateHexReference() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * (9999 - 1000 + 1)) + 1000;
        const combined = `${timestamp}${random}`;
        // Convertir a hexadecimal y tomar los primeros 8 caracteres
        const hex = combined.toString(16).toUpperCase().slice(0, 8);
        return hex;
    }

    function addGasto() {
        document.getElementById('gastoModalLabel').textContent = 'Nuevo Gasto';
        document.getElementById('gastoForm').reset();
        document.getElementById('gasto_id').value = '';
        // Generar y mostrar la nueva referencia
        const newReference = generateHexReference();
        document.getElementById('referencia').value = newReference;
        document.getElementById('referenciaLabel').textContent = `Referencia: ${newReference}`;
        var modal = new bootstrap.Modal(document.getElementById('gastoModal'));
        modal.show();
    }

    function editGasto(gasto) {
        document.getElementById('gastoModalLabel').textContent = 'Editar Gasto';
        document.getElementById('gasto_id').value = gasto.id;
        document.getElementById('referencia').value = gasto.referencia;
        // Actualizar el label con la referencia existente
        document.getElementById('referenciaLabel').textContent = `Referencia: ${gasto.referencia}`;
        document.getElementById('fecha').value = gasto.fecha;
        document.getElementById('monto').value = gasto.monto;
        document.getElementById('tipo_gasto').value = gasto.tipo_gasto_id;
        document.getElementById('descripcion').value = gasto.descripcion;
        var modal = new bootstrap.Modal(document.getElementById('gastoModal'));
        modal.show();
    }

    function imprimirComprobante(gasto) {
        var ventanaRecibo = window.open('', '_blank');
        var fechaActual = new Date().toLocaleDateString('es-ES');

        var contenidoRecibo = `
        <html>
        <head>
            <title>Comprobante de Egreso</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f0f0; }
                .recibo { width: 80mm; margin: 0 auto; padding: 10mm; background-color: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .encabezado { text-align: center; margin-bottom: 10mm; border-bottom: 1px solid #ddd; padding-bottom: 5mm; }
                .logo { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 5mm; }
                .detalle { margin-bottom: 10mm; }
                .detalle p { margin: 2mm 0; font-size: 14px;}
                .pie { text-align: center; margin-top: 10mm; font-size: 12px; color: #666; }
                .numero-recibo { font-size: 14px; color: #888; margin-bottom: 5mm; }
            </style>
        </head>
        <body>
            <div class="recibo">
                <div class="encabezado">
                    <div class="logo">Iglesia AD Rey de Reyes</div>
                    <div>Comprobante de Egreso</div>
                    <div class="numero-recibo">No. ${String(gasto.id).padStart(6, '0')}</div>
                </div>
                <div class="detalle">
                    <p><strong>Fecha de impresión:</strong> ${fechaActual}</p>
                    <p><strong>Clasificación:</strong> ${gasto.tipo_gasto}</p>
                    <p><strong>Monto:</strong> Q${parseFloat(gasto.monto).toFixed(2)}</p>
                    <p><strong>Fecha del pago:</strong> ${gasto.fecha}</p>
                    <p><strong>Referencia:</strong> ${gasto.referencia}</p>
                    <p><strong>Descripción:</strong> ${gasto.descripcion || 'Ninguna'}</p>
                </div>
                <div class="pie">
                    <p>Documento oficial interno de control de gastos</p>
                    <p>Iglesia AD Rey de Reyes</p>
                </div>
            </div>
        </body>
        </html>
        `;

        ventanaRecibo.document.write(contenidoRecibo);
        ventanaRecibo.document.close();

        setTimeout(function () {
            ventanaRecibo.focus();
            ventanaRecibo.print();
        }, 500);
    }
</script>

<?php include 'footer.php'; ?>