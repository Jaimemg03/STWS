<?php
    include "conexion.php";
    $nombre = $nif = $nacido = $altura = $accion = $id = '';
    $boton = "Insertar";

    if (isset($_GET["accion"])) $accion = $_GET["accion"];
    if (isset($_GET["id"])) $id = $_GET["id"];

    if ($accion == "Editar" && $id != "")
    {
        $stmt = $Conexion->prepare("select * from datos_personales where id = ?");
        if (!$stmt) die("Error en prepare: " . $Conexion->error);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $Resultado = $stmt->get_result();
        $stmt->close();
        
        if (!$Resultado) die("Error");
        
        $Tupla = $Resultado->fetch_assoc();
        
        $nombre = $Tupla["nombre"];
        $nif = $Tupla["nif"];
        $nacido = $Tupla["nacido"];
        $altura = $Tupla["altura"];
        $boton = "Modificar";
    }
?>
<html>
    <head><meta charset="UTF8"><title>Formulario</title><link rel='stylesheet' href='estilo.css'></head>
<body>
    <form action="index.php" method="post">
        <p>Nombre <input name="nombre" value="<?=$nombre?>"></p>
        <p>Nif <input name="nif" value="<?=$nif?>"></p>
        <p>Fecha de nacimiento <input name="nacido" type="date" value="<?=$nacido?>"></p>
        <p>Altura <input name="altura" value="<?=$altura?>"></p>
        <p><input type="submit" name="accion" value="<?=$boton?>"></p>
        <input name="id" type="hidden"  value="<?=$id?>" />
    </form>
</body>
</html>