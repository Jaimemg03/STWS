<html>
    <head><meta charset="UTF8"><link rel='stylesheet' href='estilo.css'></head>
<body>
<?php
    include "conexion.php"; // Aprovechamos para abrir la conexión

    $nombre = $nif = $nacido = $accion = $texto = $id = '';
    $altura = 0;

    if (isset($_POST["nombre"]))  $nombre = $_POST["nombre"];
    if (isset($_POST["nif"]))  $nif = $_POST["nif"];
    if (isset($_POST["nacido"])) $nacido = $_POST["nacido"];
    if (isset($_POST["altura"])) $altura = $_POST["altura"];

    if (isset($_POST["texto"])) $texto = $_POST["texto"];
    if (isset($_REQUEST["accion"])) $accion = $_REQUEST["accion"]; //Puede venir por get o por post
    if (isset($_REQUEST["id"])) $id = $_REQUEST["id"];

    if ($accion != "")
    {
        $SQL = '';
        if ($accion == "Borrar" && $id != '') {
            //! $SQL = "delete from datos_personales where id = $id";
            $stmt = $Conexion->prepare("delete from datos_personales where id = ?");
            if (!$stmt) die("Error en prepare: " . $Conexion->error);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        if ($accion == "Modificar" && $id != '') 
        {
            //! $SQL = "update datos_personales set nombre='$nombre', nif='$nif', ";
            //! $SQL .= "nacido='$nacido' , altura=$altura where id = $id";
            $stmt = $Conexion->prepare("update datos_personales set nombre=?, nif=?, nacido=?, altura=? where id=?");
            if (!$stmt) die("Error en prepare: " . $Conexion->error);
            $stmt->bind_param("sssdi", $nombre, $nif, $nacido, $altura, $id);
            $stmt->execute();
            $stmt->close();
        }
        if ($accion == "Insertar" && $id == '' && $nombre != '') 
        {
            //! $SQL = "insert into datos_personales (nombre, nif, nacido, altura) ";
            //! $SQL .= "values ('$nombre', '$nif', '$nacido', $altura)";
            $stmt = $Conexion->prepare("insert into datos_personales (nombre, nif, nacido, altura) values (?, ?, ?, ?)");
            if (!$stmt) die("Error en prepare: " . $Conexion->error);
            $stmt->bind_param("sssd", $nombre, $nif, $nacido, $altura);
            $stmt->execute();
            $stmt->close();
        }

        if ($SQL)
        {
            if (!$Conexion->query($SQL)) die("Error: ". $Conexion->error);
        }
    }

    echo "<form id='buscador' method='post'>Buscador: <input type='text' name='texto' value='$texto'>";
    echo "<input type='submit' value='localizar'></form>";
    echo "<table border='1'>";
    echo "<tr><th>Nombre</th><th>NIF</th><th>Fecha de nacimiento</th>";
    echo "<th>Altura</th><th colspan='2'>&nbsp;</th></tr>";
    
    //! $SQL = "select * from datos_personales ";
    //! if ($texto != '') 
    //! {
    //!     $SQL .= " where nombre like '%$texto%' OR nif like '%$texto%' ";
    //!     $SQL .= " OR nacido  like '%$texto%' ";
    //!     $SQL .= " OR altura = ".floatval($texto);
    //! }
    //! $SQL .= " order by id desc";
    //! $Resultado = $Conexion->query($SQL);
    if ($texto != '') 
    {
        $like = "%$texto%";
        
        $SQL = "select * from datos_personales 
                where nombre like ? 
                or nif like ? 
                or nacido like ?
                or CAST(altura AS CHAR) like ?
                order by id desc";
        
        $stmt = $Conexion->prepare($SQL);
        $stmt->bind_param("ssss", $like, $like, $like, $like);
        $stmt->execute();
        $Resultado = $stmt->get_result();
    }
    else
    {
        // Sin filtro: mostrar todos
        $SQL = "select * from datos_personales order by id desc";
        $Resultado = $Conexion->query($SQL);
    }
    while ($Tupla = $Resultado->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>{$Tupla["nombre"]}</td><td>{$Tupla["nif"]}</td>";
        echo "<td>{$Tupla["nacido"]}</td><td>{$Tupla["altura"]}</td>";
        echo "<td><a href='formulario.php?accion=Editar&id={$Tupla["id"]}'>Editar</a></td>";
        echo "<td><a href='index.php?accion=Borrar&id={$Tupla["id"]}'>Borrar</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    $Conexion->close();
?>
    <center><p><a href="formulario.php" class="boton">Nuevo jugador</a></p></center>

</body>
</html>