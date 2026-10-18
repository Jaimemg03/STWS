<?php
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    if ($env === false) {
        die('Error al leer el archivo .env');
    }
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

$servidor = $_ENV['DB_HOST'] ?? 'localhost';
$usuario  = $_ENV['DB_USUARIO'] ?? '';
$clave    = $_ENV['DB_PASSWORD'] ?? '';
$bd       = $_ENV['DB_NOMBRE'] ?? '';
$puerto   = $_ENV['DB_PUERTO'] ?? '3306';

$Conexion = new mysqli($servidor, $usuario, $clave, $bd, (int)$puerto);
if ($Conexion->connect_error) {
    die('Error de conexión: ' . $Conexion->connect_error);
}
$Conexion->set_charset('utf8');

function CrearConexion() {
    global $Conexion;
    return $Conexion;
}

function Ejecutar($conexion, $sql) {
    $resultado = $conexion->query($sql);
    if (!$resultado) {
        die("Error en consulta: " . $conexion->error);
    }
    return $resultado;
}

function DameFecha($fecha) {
    return date("d/m/Y H:i", strtotime($fecha));
}
?>
