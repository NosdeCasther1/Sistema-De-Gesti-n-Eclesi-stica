<?php
/**
 * IngresoOtros.php — Vista
 * La lógica de negocio vive en components/ingreso_otros_logica.php
 */
require_once __DIR__ . '/components/ingreso_otros_logica.php';
include __DIR__ . '/header.php';
$x_placeholder = true;
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
                    <h1 class="h2 text-dark font-weight-bold">Control de Otros Ingresos</h1>
                    <p class="text-muted">Gestione y registre las otros ingresos recibidos de los miembros.</p>
                </div>
                <div>
                    <button class="btn btn-secondary px-3 py-2 me-2" style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-file-alt me-2"></i>Reporte
                    </button>
                    <button class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                        onclick="addIngreso()">
                        <i class="fas fa-plus me-2"></i>Nuevo Ingreso
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
                                html: "<?php echo $mensaje; ?><br><br>¿Desea imprimir el recibo de este ingreso?",
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#0d6efd',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: '<i class="fas fa-print me-2"></i>Imprimir Recibo',
                                cancelButtonText: 'No imprimir',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    imprimirRecibo(<?php echo $print_receipt_data; ?>);
                                }
                            });
                        }, 200);
                    });
                </script>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Barra de búsqueda y filtros -->
                    <div class="row mb-4 bg-light p-3 rounded" style="border: 1px solid #f0f0f0;">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label text-muted small fw-bold mb-1">Buscar ingreso</label>
                            <div class="input-group"
                                style="box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-radius: 8px; overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0"
                                    placeholder="Nombre, referencia..." autocomplete="nope" readonly
                                    onfocus="this.removeAttribute('readonly');" style="box-shadow: none;">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label text-muted small fw-bold mb-1">Fecha Inicial</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i
                                        class="far fa-calendar text-muted"></i></span>
                                <input type="date" id="startDate" class="form-control"
                                    style="border-radius: 0 8px 8px 0;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold mb-1">Fecha Final</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i
                                        class="far fa-calendar-check text-muted"></i></span>
                                <input type="date" id="endDate" class="form-control"
                                    style="border-radius: 0 8px 8px 0;">
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de ofrendas -->
                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100" id="ingresosTable">
                            <thead>
                                <tr>
                                    <th>Cód. Ref.</th>
                                    <th>Miembro</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Modo Pago</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_ingresos = 0;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $total_ingresos += $row['monto'];
                                    ?>
                                    <tr>
                                        <td class="text-muted fw-bold small"><i
                                                class="fas fa-barcode me-1"></i><?php echo $row['referencia']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px; background-color: #f0ecff; color: #6b21a8; font-weight: bold; font-size: 0.8rem;">
                                                    <?php echo strtoupper(substr($row['nombres'], 0, 1)); ?>
                                                </div>
                                                <span
                                                    class="fw-bold text-dark"><?php echo htmlspecialchars($row['nombres']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-success fw-bold">Q <?php echo number_format($row['monto'], 2); ?>
                                        </td>
                                        <td class="text-muted"><i
                                                class="far fa-calendar-alt me-1"></i><?php echo date('d/m/Y', strtotime($row['fecha'])); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $badgeClass = '';
                                            switch (strtoupper($row['modo_pago'])) {
                                                case 'EFECTIVO':
                                                    $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                                    break;
                                                case 'CHEQUE':
                                                    $badgeClass = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                                    break;
                                                case 'TRANSFERENCIA BANCARIA':
                                                    $badgeClass = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge rounded-pill px-3 py-2 <?php echo $badgeClass; ?>"><i
                                                    class="fas fa-wallet me-1"></i><?php echo htmlspecialchars($row['modo_pago']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-action btn-action-edit me-1" title="Editar"
                                                    onclick='editIngreso(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-action-delete" title="Eliminar"
                                                    onclick="deleteIngreso(<?php echo $row['ingreso_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold text-dark pe-4 fs-5">Monto Total Registrado:
                                    </td>
                                    <td class="text-success fw-bold fs-5">Q
                                        <?php echo number_format($total_ingresos, 2); ?>
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

</div>
</main>
</div>

<!-- Modal para agregar/editar ofrenda -->
<div class="modal fade" id="ingresoModal" tabindex="-1" aria-labelledby="ingresoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold text-dark" id="ingresoModalLabel"><i
                        class="fas fa-hand-holding-usd text-primary me-2"></i>Nuevo Ingreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <form id="ingresoForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="ingreso_id" name="ingreso_id">
                    <input type="hidden" id="miembro_id" name="miembro_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="referencia" class="form-label text-muted fw-bold small">Cód. Referencia</label>
                            <input type="text" class="form-control text-primary fw-bold bg-light" id="referencia"
                                name="referencia" readonly style="border-radius: 8px; border: 1px dashed #ced4da;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label text-muted fw-bold small">Fecha del Aporte</label>
                            <input type="date" class="form-control" id="fecha" name="fecha"
                                value="<?php echo date('Y-m-d'); ?>" required style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="card border-0 bg-light mb-4" style="border-radius: 10px;">
                        <div class="card-body">
                            <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>Información del
                                Aportante</h6>
                            <div class="mb-3">
                                <label for="buscar_miembro" class="form-label text-muted small">Buscar Miembro en el
                                    Sistema</label>
                                <div class="position-relative">
                                    <div class="input-group"
                                        style="box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-radius: 8px; overflow: hidden;">
                                        <span class="input-group-text bg-white border-end-0"><i
                                                class="fas fa-search text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="buscar_miembro"
                                            placeholder="Escriba nombre, DPI..." autocomplete="nope" readonly
                                            onfocus="this.removeAttribute('readonly');" style="box-shadow: none;">
                                    </div>
                                    <div id="info-miembro"></div>
                                    <div id="resultados_miembros"
                                        class="position-absolute w-100 bg-white shadow-sm border mt-1"
                                        style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none; border-radius: 8px;">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="miembro_seleccionado" class="form-label text-muted small">Miembro
                                    Vinculado</label>
                                <input type="text"
                                    class="form-control fw-bold border-0 border-bottom bg-transparent px-0"
                                    id="miembro_seleccionado" readonly placeholder="Ningún miembro seleccionado..."
                                    style="border-radius: 0;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="categoria" class="form-label text-muted fw-bold small">Categoría del
                                Aporte</label>
                            <select class="form-select" id="categoria" name="categoria" required
                                style="border-radius: 8px;">
                                <option value="" selected disabled>Seleccione...</option>
                                <?php
                                mysqli_data_seek($resultado_cat, 0); // Para poder reutilizar la consulta si hay más de 1 select
                                while ($cat = mysqli_fetch_assoc($resultado_cat)):
                                    ?>
                                    <option value="<?php echo htmlspecialchars($cat['nombre']); ?>">
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="modo_pago" class="form-label text-muted fw-bold small">Modo de Pago</label>
                            <select class="form-select" id="modo_pago" name="modo_pago" required
                                style="border-radius: 8px;">
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="CHEQUE">Cheque</option>
                                <option value="TRANSFERENCIA BANCARIA">Transferencia</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="monto" class="form-label text-muted fw-bold small">Monto (Q)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold border-end-0">Q</span>
                                <input type="number" step="0.01"
                                    class="form-control text-success fw-bold border-start-0 fs-5" id="monto"
                                    name="monto" required style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacion" class="form-label text-muted fw-bold small">Comentarios u
                            Observaciones</label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="3"
                            style="border-radius: 8px;"></textarea>
                    </div>

                    <div class="text-end border-top pt-4">
                        <button type="button" class="btn btn-light px-4 py-2 me-2"
                            style="border-radius: 8px; font-weight: 500;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 py-2"
                            style="border-radius: 8px; font-weight: 500;"><i class="fas fa-save me-2"></i>Guardar
                            Ofrenda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para validación de contraseña -->
<div class="modal fade" id="adminPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-danger text-white border-bottom-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-shield-alt me-2"></i>Autenticación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <p class="text-muted small mb-3">Se requiere contraseña de administrador para editar ingresos.</p>
                <div class="mb-3">
                    <label for="adminPassword" class="form-label fw-bold text-dark">Contraseña:</label>
                    <input type="password" class="form-control text-center fs-5" id="adminPassword"
                        style="border-radius: 8px;">
                    <div id="passwordError" class="invalid-feedback text-center mt-2 fw-bold">
                        Contraseña incorrecta.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light justify-content-center">
                <button type="button" class="btn btn-danger px-4" id="validatePasswordBtn"
                    style="border-radius: 8px; font-weight: 500;">Validar <i class="fas fa-lock-open ms-2"></i></button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>


<script>
    $(document).ready(function () {
        let typingTimer;
        const doneTypingInterval = 300;

        $('#buscar_miembro').on('input', function () {
            clearTimeout(typingTimer);
            const inputValue = $(this).val();

            if (inputValue.length > 2) {
                typingTimer = setTimeout(() => buscarMiembros(inputValue), doneTypingInterval);
            } else {
                $('#resultados_miembros').empty().hide();
            }
        });

        function buscarMiembros(query) {
            $.ajax({
                url: 'buscar_miembros.php',
                method: 'GET',
                data: {
                    q: query
                },
                success: function (response) {
                    const miembros = JSON.parse(response);
                    let html = '';

                    miembros.forEach(miembro => {
                        html += `
                        <div class="p-2 border-bottom miembro-item" 
                             data-id="${miembro.miembro_id}" 
                             data-nombre="${miembro.nombres} ${miembro.apellidos}">
                            ${miembro.nombres} ${miembro.apellidos} - ${miembro.no_dpi}
                        </div>`;
                    });

                    $('#resultados_miembros').html(html).show();
                }
            });
        }

        $(document).on('click', '.miembro-item', function () {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');

            $('#miembro_id').val(id);
            $('#miembro_seleccionado').val(nombre);
            $('#buscar_miembro').val('');
            $('#resultados_miembros').empty().hide();

            generarReferencia();
        });

        function generarReferencia() {
            const fecha = new Date();
            const año = fecha.getFullYear();
            const mes = String(fecha.getMonth() + 1).padStart(2, '0');
            const dia = String(fecha.getDate()).padStart(2, '0');
            const letras = Array.from({
                length: 4
            }, () =>
                String.fromCharCode(65 + Math.floor(Math.random() * 26))).join('');
            const numeros = String(Math.floor(Math.random() * 10000)).padStart(4, '0');

            const referencia = `${año}${mes}${dia}-${letras}-${numeros}`;
            $('#referencia').val(referencia);
        }
    });

    function addIngreso() {
        document.getElementById('ingresoModalLabel').textContent = 'Nuevo Ingreso';
        document.getElementById('ingresoForm').reset();
        document.getElementById('ingreso_id').value = '';
        var modal = new bootstrap.Modal(document.getElementById('ingresoModal'));
        modal.show();

    }

    function editIngreso(ofrenda) {
        document.getElementById('ingresoModalLabel').textContent = 'Editar Ingreso';
        document.getElementById('ingreso_id').value = ofrenda.ingreso_id;
        document.getElementById('miembro_id').value = ofrenda.miembro_id;
        document.getElementById('nombre').value = ofrenda.nombres + '' + ofrenda.apellidos;
        document.getElementById('referencia').value = ofrenda.referencia;
        document.getElementById('categoria').value = ofrenda.categoria;
        document.getElementById('modo_pago').value = ofrenda.modo_pago;
        document.getElementById('monto').value = ofrenda.monto;
        document.getElementById('fecha').value = ofrenda.fecha;
        document.getElementById('observacion').value = ofrenda.observacion;

        var modal = new bootstrap.Modal(document.getElementById('ingresoModal'));
        modal.show();
    }

    function deleteIngreso(ingreso_id) {
        if (confirm('¿Estás seguro de que quieres eliminar este ingreso?')) {
            window.location.href = '?delete=' + ingreso_id;
        }
    }

    // Función para agregar modal de validación
    function editIngreso(ofrenda) {
        const adminPasswordModal = new bootstrap.Modal(document.getElementById('adminPasswordModal'));
        adminPasswordModal.show();
        window.ingresoToEdit = ofrenda;
    }

    // Función para mostrar modal de edición después de la validación
    function showEditModal(ofrenda) {
        document.getElementById('ingresoModalLabel').textContent = 'Editar Ingreso';

        // Establecer valores del formulario
        document.getElementById('ingreso_id').value = ofrenda.ingreso_id;
        document.getElementById('miembro_id').value = ofrenda.miembro_id;
        document.getElementById('nombre').value = ofrenda.miembro_id;
        document.getElementById('referencia').value = ofrenda.referencia;
        document.getElementById('categoria').value = ofrenda.categoria;
        document.getElementById('modo_pago').value = ofrenda.modo_pago;
        document.getElementById('monto').value = ofrenda.monto;
        document.getElementById('fecha').value = ofrenda.fecha;
        document.getElementById('observacion').value = ofrenda.observacion;

        // Agregar información del miembro
        const infoMiembro = `
        <div class="alert alert-info mb-3" id="info-miembro">
            <h6 class="alert-heading">Información del Miembro</h6>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>DPI:</strong> ${ofrenda.no_dpi || ''}</p>
                    <p class="mb-1"><strong>Email:</strong> ${ofrenda.email || ''}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Teléfono:</strong> ${ofrenda.tel_celular || ''}</p>
                    <p class="mb-1"><strong>Nombre:</strong> ${ofrenda.nombres || ''} ${ofrenda.apellidos || ''}</p>
                </div>
            </div>
        </div>
    `;

        const labelBuscarMiembro = document.querySelector('label[for="nombre"]');
        labelBuscarMiembro.parentNode.insertBefore(
            infoMiembro,
            labelBuscarMiembro
        );

        const modal = new bootstrap.Modal(document.getElementById('ingresoModal'));
        modal.show();
    }

    function imprimirRecibo(ingreso) {
        var ventanaRecibo = window.open('', '_blank');
        var fechaActual = new Date().toLocaleDateString('es-ES');
        var nombreCompleto = ingreso.nombres + (ingreso.apellidos ? ' ' + ingreso.apellidos : '');

        var contenidoRecibo = `
        <html>
        <head>
            <title>Recibo de Ingreso</title>
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
                    <div>Recibo de Ingreso</div>
                    <div class="numero-recibo">No. ${String(ingreso.ingreso_id).padStart(6, '0')}</div>
                </div>
                <div class="detalle">
                    <p><strong>Fecha de emisión:</strong> ${fechaActual}</p>
                    <p><strong>Aportante:</strong> ${nombreCompleto}</p>
                    <p><strong>Categoría:</strong> ${ingreso.categoria || 'Varios'}</p>
                    <p><strong>Monto:</strong> Q${parseFloat(ingreso.monto).toFixed(2)}</p>
                    <p><strong>Fecha de aporte:</strong> ${ingreso.fecha}</p>
                    <p><strong>Modo de Pago:</strong> ${ingreso.modo_pago}</p>
                    <p><strong>Referencia:</strong> ${ingreso.referencia}</p>
                </div>
                <div class="pie">
                    <p>Gracias por su generosa contribución</p>
                    <p>Este recibo es un comprobante válido de su ingreso</p>
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

    // Manejador de validación de contraseña
    $('#validatePasswordBtn').click(function () {
        const inputPassword = $('#adminPassword');
        const errorPassword = $('#passwordError');

        $.ajax({
            url: 'validar_admin.php',
            method: 'POST',
            data: {
                password: inputPassword.val()
            },
            success: function (response) {
                const resultado = JSON.parse(response);
                if (resultado.success) {
                    bootstrap.Modal.getInstance(document.getElementById('adminPasswordModal'))
                        .hide();
                    inputPassword.val('');
                    inputPassword.removeClass('is-invalid');
                    showEditModal(window.ingresoToEdit);
                } else {
                    inputPassword.addClass('is-invalid');
                    errorPassword.text(resultado.message);
                    errorPassword.show();
                }
            }
        });
    });

    // Funcionalidad de búsqueda
    $(document).ready(function () {
        let typingTimer;
        const doneTypingInterval = 300;

        $('#buscar_miembro').on('input', function () {
            clearTimeout(typingTimer);
            const inputValue = $(this).val();

            if (inputValue.length > 2) {
                typingTimer = setTimeout(() => buscarMiembros(inputValue), doneTypingInterval);
            } else {
                $('#resultados_miembros').empty().hide();
            }
        });

        function buscarMiembros(query) {
            $.ajax({
                url: 'buscar_miembros.php',
                method: 'GET',
                data: { q: query },
                success: function (response) {
                    const miembros = typeof response === 'string' ? JSON.parse(response) : response;
                    let html = '';
                    if (miembros.length === 0) {
                        html = '<div class="p-2 text-muted text-center"><small>No se encontraron resultados.</small></div>';
                    } else {
                        miembros.forEach(miembro => {
                            const dpi = miembro.no_dpi ? miembro.no_dpi : 'N/A';
                            const tel = miembro.tel_celular ? miembro.tel_celular : 'N/A';
                            html += `
                            <div class="p-2 border-bottom miembro-item cursor-pointer" 
                                 data-id="${miembro.id}" 
                                 data-nombres="${miembro.nombres}"
                                 data-apellidos="${miembro.apellidos}"
                                 data-dpi="${dpi}"
                                 data-tel="${tel}"
                                 data-email="${miembro.email}">
                                <div class="fw-bold">${miembro.nombres} ${miembro.apellidos}</div>
                                <small class="text-muted"><i class="fas fa-id-card me-1"></i> DPI: ${dpi} &nbsp;|&nbsp; <i class="fas fa-phone-alt me-1"></i> Tel: ${tel}</small>
                            </div>`;
                        });
                    }
                    $('#resultados_miembros').html(html).show();
                }
            });
        }

        $(document).on('click', '.miembro-item', function () {
            const $item = $(this);
            const id = $item.data('id');
            const nombres = $item.data('nombres');
            const apellidos = $item.data('apellidos');
            const dpi = $item.data('dpi');
            const tel = $item.data('tel');
            const email = $item.data('email');

            $('#miembro_id').val(id);
            $('#miembro_seleccionado').val(`${nombres} ${apellidos}`);
            $('#buscar_miembro').val('');

            const infoMiembro = `
            <div class="alert alert-info mt-2">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>DPI:</strong> ${dpi}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Teléfono:</strong> ${tel}</p>
                    </div>
                </div>
            </div>`;

            $('#info-miembro').html(infoMiembro);
            $('#resultados_miembros').hide();
            generarReferencia();
        });

        // Función para generar referencia automática
        function generarReferencia() {
            const fecha = new Date();
            const año = fecha.getFullYear();
            const mes = String(fecha.getMonth() + 1).padStart(2, '0');
            const dia = String(fecha.getDate()).padStart(2, '0');
            const letras = Array.from({
                length: 4
            }, () =>
                String.fromCharCode(65 + Math.floor(Math.random() * 26))).join('');
            const numeros = String(Math.floor(Math.random() * 10000)).padStart(4, '0');

            const referencia = `${año}${mes}${dia}-${letras}-${numeros}`;
            $('#referencia').val(referencia);
        }

        // Generar referencia al abrir el modal de nueva ofrenda
        $('#ingresoModal').on('shown.bs.modal', function () {
            if (!$('#ingreso_id').val()) { // Solo para nuevas ofrendas
                generarReferencia();
            }
        });
    });
</script>
</body>

</html>

<?php include 'footer.php'; ?>