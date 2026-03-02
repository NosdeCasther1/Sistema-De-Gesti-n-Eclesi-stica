<?php
session_start();
session_unset();
session_destroy();
header("Location: /ProyectoIglesia/Vistas/html/Login.php");
exit();
?>