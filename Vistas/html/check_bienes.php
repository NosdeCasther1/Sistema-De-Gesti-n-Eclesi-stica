<?php
require '../../Config/conexion.php';
$conn = getDBConnection();
$res = mysqli_query($conn, 'DESCRIBE bienes_muebles');
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
} else {
    echo "No existe bienes_muebles. " . mysqli_error($conn);
}
?>