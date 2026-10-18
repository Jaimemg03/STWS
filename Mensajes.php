<?php
/*
 * Archivo: @CRUD/Mensajes.php
 * Empleado utilizando el prompt del usuario para incluir estilos CSS en la práctica CRUD (@CRUD).
 * Modelo de referencia: @CRUD/estilo.css
 */

    include "conexion.php";
    session_start();

    if (!isset($_SESSION["USUARIO_ID"]) || !isset($_SESSION["USUARIO_NOMBRE"])) {
        header('location: index.html');
        exit;
    }

    $nombreUsuario = $_SESSION["USUARIO_NOMBRE"];
    $usuarioId = $_SESSION["USUARIO_ID"];

    $accion = isset($_GET["Accion"]) ? $_GET["Accion"] : '';
    $id     = isset($_GET["ID"])     ? $_GET["ID"]     : '';

    $fecha = '';
    $de    = '';
    $para  = 0;
    $mensaje = '';
    $boton = "Insertar";

    if ($accion == "Editar" && $id != "")
    {
        $stmt = $Conexion->prepare("select id, Usuario, Para, Mensaje, Fecha from mensajes where id = ?");
        if (!$stmt) die("Error en prepare: " . $Conexion->error);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $Resultado = $stmt->get_result();
        $stmt->close();

        if ($Tupla = $Resultado->fetch_assoc())
        {
            $fecha  = $Tupla["Fecha"];
            $de     = $Tupla["Usuario"];
            $para   = $Tupla["Para"];
            $mensaje = $Tupla["Mensaje"];
            $boton  = "Modificar";
        }
    }
?>
<html>
    <head>
        <title>Mensajes</title>
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/mensaje.css">
    </head>
    <body>

    <?php
    echo "<h2 class='bienvenida'>Bienvenido/a $nombreUsuario</h2>";
    ?>

    <form action="Lista.php" method="post">
        <?php
            $usuarioId = $_SESSION["USUARIO_ID"];

            $stmt = $Conexion->prepare("select id, usuario from usuarios where id <> ? order by usuario asc");
            if (!$stmt) die("Error en prepare: " . $Conexion->error);
            $stmt->bind_param("i", $usuarioId);
            $stmt->execute();
            $ResultadoUsuarios = $stmt->get_result();

            echo "<fieldset class='Formulario'><legend><strong>Mensaje</strong></legend>";
            echo "<div class='Etiqueta'>Para</div> <div class='Valor'><select name='Para' class='Para'>";
            echo ("<option value=''></option>");
            while ($RTemp = $ResultadoUsuarios->fetch_array())
            {
                echo ("<option value='{$RTemp['id']}'");
                if (isset($RTemp["id"]) && $RTemp["id"] == $para) echo (" selected='selected'");
                echo (">" . $RTemp["usuario"] . "</option>");
            }
            echo ("</select></div>\n");
            $stmt->close();
        ?>
        <div class='Separador'>&nbsp;</div>
        <div class="Etiqueta">Mensaje</div><div class="Valor">
            <textarea class='TA_Mensaje' name="Mensaje"><?php echo htmlspecialchars($mensaje); ?></textarea>
        </div>
        <input type="hidden" name="ID" value="<?php echo $id; ?>">
        <div class='Separador'>&nbsp;</div>
        <div>
            <button type="submit" name="Accion" value="<?php echo $boton; ?>"><?php echo $boton; ?></button>
        </div>
        </fieldset>
    </form>
    </body>
</html>