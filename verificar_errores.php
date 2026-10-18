<?php
// Activar reporte de errores para detectar warnings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión para probar que no falla
include "conexion.php";
echo "Conexión a BD: OK<br>";

// Probar lectura de variables inexistentes (debería quedar en blanco, sin warning)
$prueba = isset($_GET['clave_que_no_existe']) ? $_GET['clave_que_no_existe'] : '';
echo "Prueba isset(): OK<br>";
?>