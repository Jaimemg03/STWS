<?php
include "conexion.php";

$usuario = isset($_POST["usuario"]) ? $_POST["usuario"] : '';
$clave   = isset($_POST["clave"])   ? $_POST["clave"]   : '';
$mensaje = '';

if ($usuario != '' && $clave != '')
{
    $hash = password_hash($clave, PASSWORD_DEFAULT);

    $stmt = $Conexion->prepare("insert into usuarios (usuario, clave) values (?, ?)");
    if (!$stmt) {
        $mensaje = "Error en la consulta: " . $Conexion->error;
    } else {
        $stmt->bind_param("ss", $usuario, $hash);
        if ($stmt->execute()) {
            $mensaje = "Usuario registrado correctamente.";
        } else {
            $mensaje = "Error al registrar: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de usuario</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <form method="post" action="registro.php" style="max-width:400px;margin:40px auto;padding:20px;background:#fff;border-radius:12px;box-shadow:0 6px 25px rgba(0,0,0,0.1);">
        <h2 style="text-align:center;color:#333;margin-bottom:20px;">Registro de usuario</h2>
        
        <?php if ($mensaje != ''): ?>
            <p style="color: #d00; text-align:center; margin-bottom:15px;"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <p style="font-weight:600;color:#4a5568;">Usuario</p>
        <input type="text" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>" required style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;margin-bottom:15px;">

        <p style="font-weight:600;color:#4a5568;">Contraseña</p>
        <input type="password" name="clave" required style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;margin-bottom:15px;">

        <button type="submit" style="width:100%;padding:12px;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Registrarse</button>
    </form>

    <p style="text-align:center;margin-top:15px;">
        ¿Ya tienes cuenta? <a href="index.html" style="color:#667eea;">Inicia sesión aquí</a>
    </p>
</body>
</html>