<?php
include 'header.php';
// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $miembro_id = $_POST['miembro_id']; // ID del miembro

    // Obtener el nombre completo del miembro desde la base de datos
    $stmt = mysqli_prepare($conn, "SELECT CONCAT(nombres, ' ', apellidos) as nombre_completo FROM miembros WHERE miembro_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $miembro_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre_completo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Generar referencia automática
    $año = date('Y');
    $mes = date('m');
    $dia = date('d');
    $letras = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90));
    $numeros = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $referencia = "{$año}{$mes}{$dia}-{$letras}-{$numeros}";

    $modo_pago = $_POST['modo_pago'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];

    try {
        mysqli_begin_transaction($conn);

        if ($id) {
            // Definir la query antes de usarla
            $query = "UPDATE diezmos SET miembro = ?, nombre_completo = ?, modo_pago = ?, monto = ?, fecha = ? WHERE id = ?";
            if (!$stmt = mysqli_prepare($conn, $query)) {
                throw new Exception("Error preparando la consulta: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "sssdsi", $miembro_id, $nombre_completo, $modo_pago, $monto, $fecha, $id);

            // Obtener referencia existente
            $stmt = mysqli_prepare($conn, "SELECT referencia FROM diezmos WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $referencia_actual);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Query de actualización
            $query = "UPDATE diezmos SET miembro = ?, nombre_completo = ?, modo_pago = ?, monto = ?, fecha = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssdsi", $miembro_id, $nombre_completo, $modo_pago, $monto, $fecha, $id);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_commit($conn);
                $response = [
                    'status' => 'success',
                    'message' => "Diezmo actualizado exitosamente.",
                    'referencia' => $referencia_actual
                ];
            } else {
                throw new Exception(mysqli_error($conn));
            }
        } else {
            // Insertar nuevo diezmo
            $query = "INSERT INTO diezmos (
                miembro, 
                nombre_completo,
                referencia, 
                modo_pago, 
                monto, 
                fecha
                ) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param(
                $stmt,
                "ssssds",
                $miembro_id,
                $nombre_completo,
                $referencia,
                $modo_pago,
                $monto,
                $fecha
            );
        }
        if (mysqli_stmt_execute($stmt)) {
            mysqli_commit($conn);
            $response = [
                'status' => 'success',
                'message' => $id ? "Diezmo actualizado exitosamente." : "Diezmo agregado exitosamente.",
                'referencia' => $referencia
            ];
        } else {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $response = [
            'status' => 'error',
            'message' => "Error: " . $e->getMessage()
        ];
    }

    // Devolver respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Procesamiento de la eliminación de diezmo
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($id === false) {
        $mensaje = "ID inválido.";
    } else {
        $query = "DELETE FROM diezmos WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $mensaje = "Diezmo eliminado exitosamente.";
        } else {
            $mensaje = "Error al eliminar diezmo: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Consulta para obtener todos los diezmos / Consulta principal

$query = "SELECT d.*, 
         m.miembro_id,
         m.nombres,
         m.apellidos,
         m.tel_celular,
         m.email,
         m.no_dpi
         FROM diezmos d
         LEFT JOIN miembros m ON d.miembro = m.miembro_id
         ORDER BY d.fecha DESC";

// Al final del archivo, después de la última consulta
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
                    <h1 class="h2 text-dark font-weight-bold">Control de Diezmos</h1>
                    <p class="text-muted">Gestione y registre los diezmos recibidos de los miembros.</p>
                </div>
                <div>
                    <a href="reporte_ingresos.php" class="btn btn-secondary px-3 py-2 me-2"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-file-alt me-2"></i>Reporte
                    </a>
                    <button class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                        onclick="addDiezmo()">
                        <i class="fas fa-plus me-2"></i>Nuevo Diezmo
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

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Barra de búsqueda y filtros -->
                    <div class="row mb-4 bg-light p-3 rounded" style="border: 1px solid #f0f0f0;">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label text-muted small fw-bold mb-1">Buscar diezmo</label>
                            <div class="input-group"
                                style="box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-radius: 8px; overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0"
                                    placeholder="Nombre, referencia..." style="box-shadow: none;">
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

                    <!-- Tabla de diezmos -->
                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100" id="diezmosTable">
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
                                $total_diezmos = 0;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $total_diezmos += $row['monto'];
                                    ?>
                                    <tr>
                                        <td class="text-muted fw-bold small"><i
                                                class="fas fa-barcode me-1"></i><?php echo $row['referencia']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px; background-color: #e0f2fe; color: #0284c7; font-weight: bold; font-size: 0.8rem;">
                                                    <?php echo strtoupper(substr($row['nombre_completo'], 0, 1)); ?>
                                                </div>
                                                <span
                                                    class="fw-bold text-dark"><?php echo htmlspecialchars($row['nombre_completo']); ?></span>
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
                                                    onclick='editDiezmo(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-action-delete me-1" title="Eliminar"
                                                    onclick="deleteDiezmo(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button
                                                    class="btn btn-action bg-info bg-opacity-10 text-info border border-info border-opacity-25 btn-sm px-2 py-1"
                                                    title="Imprimir Recibo" style="border-radius: 6px;"
                                                    onclick='imprimirRecibo(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }
                                mysqli_free_result($result);
                                mysqli_close($conn); ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold text-dark pe-4 fs-5">Monto Total Registrado:
                                    </td>
                                    <td class="text-success fw-bold fs-5" id="totalMonto">Q
                                        <?php echo number_format($total_diezmos, 2); ?></td>
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

<!-- Modal para agregar/editar diezmo -->
<div class="modal fade" id="diezmoModal" tabindex="-1" aria-labelledby="diezmoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold text-dark" id="diezmoModalLabel"><i
                        class="fas fa-hand-holding-usd text-primary me-2"></i>Nuevo Diezmo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <form id="diezmoForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="id" name="id">
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
                                            placeholder="Escriba para buscar miembro..." autocomplete="off"
                                            style="box-shadow: none;">
                                    </div>
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
                        <div class="col-md-6 mb-3">
                            <label for="modo_pago" class="form-label text-muted fw-bold small">Modo de Pago</label>
                            <select class="form-select" id="modo_pago" name="modo_pago" required
                                style="border-radius: 8px;">
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="CHEQUE">Cheque</option>
                                <option value="TRANSFERENCIA BANCARIA">Transferencia</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="monto" class="form-label text-muted fw-bold small">Monto (Q)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold border-end-0">Q</span>
                                <input type="number" step="0.01"
                                    class="form-control text-success fw-bold border-start-0 fs-5" id="monto"
                                    name="monto" required style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="text-end border-top pt-4 mt-2">
                        <button type="button" class="btn btn-light px-4 py-2 me-2"
                            style="border-radius: 8px; font-weight: 500;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 py-2"
                            style="border-radius: 8px; font-weight: 500;"><i class="fas fa-save me-2"></i>Guardar
                            Diezmo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--modal para la validación de contraseña -->
<div class="modal fade" id="adminPasswordModal" tabindex="-1" aria-labelledby="adminPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-danger text-white border-bottom-0">
                <h5 class="modal-title fw-bold" id="adminPasswordModalLabel"><i
                        class="fas fa-shield-alt me-2"></i>Autenticación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <p class="text-muted small mb-3">Se requiere contraseña de administrador para editar diezmos.</p>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function addDiezmo() {
        document.getElementById('diezmoModalLabel').textContent = 'Nuevo Diezmo';
        document.getElementById('diezmoForm').reset();
        document.getElementById('id').value = '';
        // Generar referencia
        const fecha = new Date();
        const año = fecha.getFullYear();
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const dia = String(fecha.getDate()).padStart(2, '0');
        const letras = Array.from({
            length: 4
        }, () => String.fromCharCode(65 + Math.floor(Math.random() * 26))).join('');
        const numeros = String(Math.floor(Math.random() * 10000)).padStart(4, '0');
        const referencia = `${año}${mes}${dia}-${letras}-${numeros}`;

        document.getElementById('referencia').value = referencia;

        var modal = new bootstrap.Modal(document.getElementById('diezmoModal'));
        modal.show();
    }

    function editDiezmo(diezmo) {
        // Primero validar que sea un administrador
        const adminPasswordModal = new bootstrap.Modal(document.getElementById('adminPasswordModal'));
        adminPasswordModal.show();

        // Guardar el diezmo para usarlo después de la validación
        window.diezmoToEdit = diezmo;
    }

    // Función para mostrar el modal de edición después de validar
    function showEditModal(diezmo) {
        document.getElementById('diezmoModalLabel').textContent = 'Editar Diezmo';

        // Establecer los valores del formulario
        document.getElementById('id').value = diezmo.id;
        document.getElementById('referencia').value = diezmo.referencia;
        document.getElementById('monto').value = diezmo.monto;
        document.getElementById('fecha').value = diezmo.fecha;
        document.getElementById('modo_pago').value = diezmo.modo_pago;

        // Establecer los datos del miembro
        document.getElementById('miembro_id').value = diezmo.miembro_id;
        document.getElementById('miembro_seleccionado').value = diezmo.nombre_completo;

        // Mostrar información adicional del miembro
        const memberInfo = `
        <div class="alert alert-info mb-3">
            <h6 class="alert-heading">Información del Miembro</h6>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>DPI:</strong> ${diezmo.no_dpi}</p>
                    <p class="mb-1"><strong>Email:</strong> ${diezmo.email}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Teléfono:</strong> ${diezmo.tel_celular}</p>
                    <p class="mb-1"><strong>Nombre:</strong> ${diezmo.nombre_completo}</p>
                </div>
            </div>
        </div>
    `;

        // Insertar la información del miembro antes del campo de búsqueda
        const memberInfoContainer = document.createElement('div');
        memberInfoContainer.id = 'member-info';
        memberInfoContainer.innerHTML = memberInfo;

        const buscarMiembroLabel = document.querySelector('label[for="buscar_miembro"]');
        buscarMiembroLabel.parentNode.insertBefore(memberInfoContainer, buscarMiembroLabel);

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('diezmoModal'));
        modal.show();
    }

    // Agregar evento para limpiar la información del miembro cuando se cierre el modal
    $('#diezmoModal').on('hidden.bs.modal', function () {
        const memberInfo = document.getElementById('member-info');
        if (memberInfo) {
            memberInfo.remove();
        }
    });

    // Modificar la función de validación de contraseña
    $('#validatePasswordBtn').click(function () {
        const passwordInput = $('#adminPassword');
        const passwordError = $('#passwordError');

        $.ajax({
            url: 'validar_admin.php',
            method: 'POST',
            data: {
                password: passwordInput.val()
            },
            success: function (response) {
                const result = JSON.parse(response);

                if (result.success) {
                    // Cerrar modal de contraseña
                    bootstrap.Modal.getInstance(document.getElementById('adminPasswordModal'))
                        .hide();

                    // Limpiar el campo de contraseña
                    passwordInput.val('');
                    passwordInput.removeClass('is-invalid');

                    // Mostrar el modal de edición con los datos del diezmo
                    showEditModal(window.diezmoToEdit);
                } else {
                    passwordInput.addClass('is-invalid');
                    passwordError.text(result.message);
                    passwordError.show();
                }
            }
        });
    });

    function deleteDiezmo(id) {
        if (confirm('¿Estás seguro de que quieres eliminar este diezmo?')) {
            window.location.href = '?delete=' + id;
        }
    }


    function imprimirRecibo(diezmo) {
        // Crear una nueva ventana para el recibo
        var ventanaRecibo = window.open('', '_blank');

        // Obtener la fecha actual para el recibo
        var fechaActual = new Date().toLocaleDateString('es-ES');

        // Generar el HTML del recibo
        var contenidoRecibo = `
        <html>
        <head>
            <title>Recibo de Diezmo</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0; 
                    padding: 20px; 
                    background-color: #f0f0f0;
                }
                .recibo { 
                    width: 80mm; 
                    margin: 0 auto; 
                    padding: 10mm; 
                    background-color: white;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }
                .encabezado { 
                    text-align: center; 
                    margin-bottom: 10mm;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5mm;
                }
                .logo {
                    font-size: 24px;
                    font-weight: bold;
                    color: #333;
                    margin-bottom: 5mm;
                }
                .detalle { 
                    margin-bottom: 10mm;
                }
                .detalle p {
                    margin: 2mm 0;
                }
                .pie { 
                    text-align: center; 
                    margin-top: 10mm;
                    font-size: 12px;
                    color: #666;
                }
                .numero-recibo {
                    font-size: 14px;
                    color: #888;
                    margin-bottom: 5mm;
                }
            </style>
        </head>
        <body>
            <div class="recibo">
                <div class="encabezado">
                    <div class="logo">Iglesia AD Rey de Reyes</div>
                    <div>Recibo de Diezmo</div>
                    <div class="numero-recibo">No. ${diezmo.id.padStart(6, '0')}</div>
                </div>
                <div class="detalle">
                    <p><strong>Fecha de emisión:</strong> ${fechaActual}</p>
                    <p><strong>Miembro:</strong> ${diezmo.miembro}</p>
                    <p><strong>Monto:</strong> Q${parseFloat(diezmo.monto).toFixed(2)}</p>
                    <p><strong>Fecha de diezmo:</strong> ${diezmo.fecha}</p>
                    <p><strong>Modo de Pago:</strong> ${diezmo.modo_pago}</p>
                    <p><strong>Referencia:</strong> ${diezmo.referencia}</p>
                </div>
                <div class="pie">
                    <p>Gracias por su generosa contribución</p>
                    <p>Este recibo es un comprobante válido de su diezmo</p>
                </div>
            </div>
        </body>
        </html>
    `;

        // Escribir el contenido del recibo en la nueva ventana
        ventanaRecibo.document.write(contenidoRecibo);
        ventanaRecibo.document.close();

        // Imprimir el recibo
        ventanaRecibo.print();

        // Cerrar la ventana del recibo después de imprimir (opcional)
        // ventanaRecibo.close();
    }


    $(document).ready(function () {

        // Función principal de búsqueda
        function searchDiezmos() {
            var searchText = $('#searchInput').val().toLowerCase();
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            // Convertir fechas a objetos Date para comparación
            var start = startDate ? new Date(startDate) : null;
            var end = endDate ? new Date(endDate) : null;

            // Si hay una fecha final, ajustarla al final del día
            if (end) {
                end.setHours(23, 59, 59, 999);
            }

            $('.table tbody tr').each(function () {
                var row = $(this);
                var showRow = true;

                // Búsqueda por texto
                if (searchText) {
                    var textFound = false;
                    row.find('td').each(function () {
                        if ($(this).text().toLowerCase().includes(searchText)) {
                            textFound = true;
                            return false; // Salir del bucle each
                        }
                    });
                    showRow = textFound;
                }

                // Filtrado por fechas
                if (showRow && (start || end)) {
                    var fechaDiezmo = new Date(row.find('td:eq(4)')
                        .text()); // Ajusta el índice según tu tabla

                    if (start && fechaDiezmo < start) {
                        showRow = false;
                    }
                    if (end && fechaDiezmo > end) {
                        showRow = false;
                    }
                }

                row.toggle(showRow);
            });

            // Actualizar el total después de filtrar
            updateTotal();
        }

        // Función para actualizar el total
        function updateTotal() {
            var total = 0;
            $('.table tbody tr:visible').each(function () {
                var montoText = $(this).find('td:eq(3)').text(); // Ajusta el índice según tu tabla
                var monto = parseFloat(montoText.replace('Q', '').replace(',', ''));
                if (!isNaN(monto)) {
                    total += monto;
                }
            });

            // Actualizar el total en el footer de la tabla
            $('#totalMonto').html('Q ' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g,
                '$&,'));
        }

        // Event listeners para los campos de búsqueda
        $('#searchInput').on('keyup', function (e) {
            if (e.key === 'Enter' || $(this).val() === '') {
                searchDiezmos();
            }
        });

        $('#searchButton').on('click', function () {
            searchDiezmos();
        });

        $('#startDate, #endDate').on('change', function () {
            searchDiezmos();
        });

        // Inicializar fechas con valores por defecto
        var today = new Date();
        var thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(today.getDate() - 30);

        $('#startDate').val(thirtyDaysAgo.toISOString().split('T')[0]);
        $('#endDate').val(today.toISOString().split('T')[0]);

        // Realizar búsqueda inicial
        searchDiezmos();
    });

    document.getElementById('diezmoForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        try {
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.status === 'success') {
                const modal = document.getElementById('diezmoModal');
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modal.querySelector(':focus')?.blur();
                    modalInstance.hide();
                }

                setTimeout(() => {
                    alert(data.message);
                    window.location.reload();
                }, 200);
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        }
    });

    // Event listeners para la búsqueda
    document.getElementById("searchInput").addEventListener("keyup", searchDiezmos);
    document.getElementById("searchButton").addEventListener("click", searchDiezmos);
    document.getElementById("startDate").addEventListener("change", searchDiezmos);
    document.getElementById("endDate").addEventListener("change", searchDiezmos);
</script>
</body>

</html>

<?php include 'footer.php'; ?>