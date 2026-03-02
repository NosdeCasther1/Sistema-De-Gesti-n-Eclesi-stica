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
if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
    try {
        $rows = processExcelFile($_FILES['excel_file']);

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

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4">
                <h1 class="h2 text-dark font-weight-bold">Importar Miembros desde Excel</h1>
                <p class="text-muted">Siga los pasos a continuación para agregar nuevos miembros masivamente al sistema.
                </p>
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
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="template-download mb-5">
                                <h5 class="text-primary fw-bold mb-3"><i class="fas fa-file-excel me-2"></i> 1. Descarga
                                    la plantilla</h5>
                                <p class="text-muted small mb-3">Utilice este formato de Excel para asegurar que los
                                    datos se importen correctamente. No modifique los encabezados de las columnas.</p>
                                <a href="templates/plantilla_miembros.xlsx" class="btn btn-outline-primary w-100"
                                    style="border-radius: 8px;">
                                    <i class="fas fa-download me-2"></i> Descargar Plantilla .xlsx
                                </a>
                            </div>

                            <hr class="my-4">

                            <div class="import-form">
                                <h5 class="text-success fw-bold mb-3"><i class="fas fa-upload me-2"></i> 2. Importa tu
                                    archivo</h5>
                                <p class="text-muted small mb-3">Una vez llenada la plantilla con la información de los
                                    miembros, suba el archivo aquí.</p>
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="mb-4">
                                        <input type="file" class="form-control" name="excel_file" accept=".xlsx"
                                            required style="border-radius: 8px; padding: 10px;">
                                    </div>
                                    <button type="submit" class="btn btn-success w-100"
                                        style="border-radius: 8px; padding: 10px; font-weight: 500;">
                                        <i class="fas fa-cloud-upload-alt me-2"></i> Procesar e Importar Registros
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Tabla de Registros Recientes -->
                <div class="col-lg-7 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold text-dark"><i class="fas fa-history text-muted me-2"></i> Registros
                                importados recientemente</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table-softwys table-hover w-100">
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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </main>
</div>

<?php include 'footer.php'; ?>
</body>

</html>