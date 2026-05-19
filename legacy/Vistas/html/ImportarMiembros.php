<?php

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Función para procesar el archivo Excel
function processExcelFile($file)
{
    require 'vendor/autoload.php';

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    array_shift($rows);
    return $rows;
}

$mensaje = '';

// Procesar la importación del archivo
if (isset($_FILES['archivo_excel']) && $_FILES['archivo_excel']['error'] == 0) {
    try {
        $rows = processExcelFile($_FILES['archivo_excel']);

        foreach ($rows as $row) {
            $nombres = $row[0];
            $apellidos = $row[1];
            $direccion = $row[2];
            $telefono = $row[3];
            $fecha_nacimiento = date('Y-m-d', strtotime($row[4]));
            $no_dpi = $row[5];
            $email = $row[6];

            $query = "INSERT INTO miembros (nombres, apellidos, direccion, tel_celular, fecha_nacimiento, no_dpi, email, estado) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo')";

            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssssss", $nombres, $apellidos, $direccion, $telefono, $fecha_nacimiento, $no_dpi, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        $mensaje = "Importación completada exitosamente.";
    } catch (Exception $e) {
        $mensaje = "Error al importar: " . $e->getMessage();
    }
}
?>

<?php require_once 'header.php'; ?>

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid p-3 p-md-4 mb-5">
            <!-- Header del Módulo -->
            <div class="module-header">
                <div class="module-title-section">
                    <ul class="breadcrumb-custom mb-2">
                        <li class="breadcrumb-item-custom"><a href="/ProyectoIglesia/inicio">Inicio</a></li>
                        <li class="breadcrumb-item-custom active">Importar Miembros</li>
                    </ul>
                    <h1 class="h2 text-dark font-weight-bold">Importar Miembros</h1>
                    <p class="text-muted mb-0">Carga masiva de registros desde archivos Excel.</p>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-info-circle me-2"></i> <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Columna Izquierda: Instrucciones y Formulario -->
                <div class="col-lg-5 mb-4">
                    <div class="card-module h-100 p-4">
                        <div class="template-download mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="text-muted fw-bold mb-0">1. Plantilla de Importación</h6>
                                <a href="templates/plantilla_miembros.xlsx" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-2"></i>Descargar .xlsx
                                </a>
                            </div>
                            <p class="text-muted small mb-0">Asegúrese de usar el formato oficial para evitar errores en la carga.</p>
                        </div>

                        <hr class="my-4">

                        <div class="import-form">
                            <h5 class="text-white fw-bold mb-3"><i class="fas fa-upload me-2 text-primary"></i> 2. Carga de Archivo</h5>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="dropzone-premium mb-4" id="dropzone">
                                    <i class="fas fa-file-excel dropzone-icon"></i>
                                    <h6 class="fw-bold text-white mb-1">Arrastra tu archivo Excel aquí</h6>
                                    <p class="text-muted small mb-0">o haz clic para explorar en tu computadora (.xlsx, .xls)</p>
                                    <input type="file" name="archivo_excel" id="archivo_excel" accept=".xlsx, .xls" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-3 shadow-sm" style="border-radius: 12px; font-weight: 600;">
                                    <i class="fas fa-cloud-upload-alt me-2"></i> Procesar e Importar Registros
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Tabla de Registros Recientes -->
                <div class="col-lg-7 mb-4">
                    <div class="card-module h-100">
                        <div class="card-header-custom">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-history text-muted me-2"></i> Registros
                                Importados</h5>
                        </div>
                        <div class="p-4">
                            <div class="table-responsive border rounded-3">
                                <table class="table-custom table-hover w-100 mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nombres</th>
                                            <th>Apellidos</th>
                                            <th>Dirección</th>
                                            <th>Teléfono</th>
                                            <th>Nacimiento</th>
                                            <th>Documento</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM miembros ORDER BY miembro_id DESC LIMIT 5";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>{$row['nombres']}</td>";
                                            echo "<td>{$row['apellidos']}</td>";
                                            echo "<td>{$row['direccion']}</td>";
                                            echo "<td>{$row['tel_celular']}</td>";
                                            echo "<td>{$row['fecha_nacimiento']}</td>";
                                            echo "<td>{$row['no_dpi']}</td>";
                                            echo "<td>{$row['email']}</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script>
    document.getElementById('archivo_excel').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            const dropzone = this.closest('.dropzone-premium');
            dropzone.querySelector('h6').innerHTML = `<i class="fas fa-check-circle text-success me-2"></i> ${fileName}`;
            dropzone.querySelector('p').innerText = 'Archivo listo para procesar';
            dropzone.style.borderColor = 'var(--bs-success)';
            dropzone.style.backgroundColor = 'rgba(var(--bs-success-rgb), 0.05)';
        }
    });

    // Drag over feedback
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('archivo_excel');

    fileInput.addEventListener('dragenter', () => dropzone.classList.add('dragover'));
    fileInput.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
    fileInput.addEventListener('drop', () => dropzone.classList.remove('dragover'));
    </script>

    <?php require_once 'footer.php'; ?>

