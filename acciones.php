<style>body { color: #CCC; background-color: #111; }</style>
<meta charset="UTF8">
<?php
    include "conexion.php"; // Aprovechamos para abrir la conexión

    $nombre = $nif = $nacido = $accion = $texto = $id = '';
    $altura = 0;

    if (isset($_POST["nombre"]))  $nombre = $_POST["nombre"];
    if (isset($_POST["nif"]))  $nif = $_POST["nif"];
    if (isset($_POST["nacido"])) $nacido = $_POST["nacido"];
    if (isset($_POST["altura"])) $altura = $_POST["altura"];

    if (isset($_POST["texto"])) $texto = $_POST["texto"];
    if (isset($_GET["accion"])) $accion = $_GET["accion"];
    if (isset($_GET["id"])) $id = $_GET["id"];
    
    if ($accion != "")
    {
        if ($accion == "Borrar" && $id != "") {
            //! $SQL = "delete from datos_personales where id = $id";
            $stmt = $Conexion->prepare("delete from datos_personales where id = ?");
            if (!$stmt) die("Error en prepare: " . $Conexion->error);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        if ($accion == "Editar" && $id != "") {
            //! $SQL = "update datos_personales set nombre='$nombre', nif='$nif', nacido='$nacido' and altura=$altura where id = $id";
            $stmt = $Conexion->prepare("update datos_personales set nombre=?, nif=?, nacido=?, altura=? where id=?");
            if (!$stmt) die("Error en prepare: " . $Conexion->error);
            $stmt->bind_param("sssdi", $nombre, $nif, $nacido, $altura, $id);
            $stmt->execute();
            $stmt->close();
        }
        if ($nombre != '') {
            //! $SQL = "insert into datos_personales (nombre, nif, nacido, altura) values ('$nombre', '$nif', '$nacido', $altura)";
            $stmt = $Conexion->prepare("insert into datos_personales (nombre, nif, nacido, altura) values (?, ?, ?, ?)");
            if (!$stmt) die("Error en prepare: " . $Conexion->error);
            $stmt->bind_param("sssd", $nombre, $nif, $nacido, $altura);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (!$Conexion->query($SQL)) die("Error: ". $Conexion->error);

    echo "<form id='buscador'>Buscador: <input type='text' value='$texto'><input type='submit' value='localizar'></form>";
    echo "<table border='1'>";
    $SQL = "select * from datos_personales order by id desc";
    $Resultado = $Conexion->query($SQL);
    while ($Tupla = $Resultado->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>{$Tupla["nombre"]}</td><td>{$Tupla["nif"]}</td>";
        echo "<td>{$Tupla["nacido"]}</td><td>{$Tupla["altura"]}</td>";
        echo "<td><a href='formulario.php?accion=Editar&id={$Tupla["id"]}'>Editar</a></td>";
        echo "<td><a href='insertando.php?accion=Borrar&id={$Tupla["id"]}'>Borrar</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    $Conexion->close();
?>