<!-- Esta primera etiqueta "meta" es para que los acentos se vean -->
<meta charset="UTF8">
<?php
    include "conexion.php"; // Aprovechamos para abrir la conexión

    // Introducimos la consulta, recuerda ".=" concatena.
    $SQL = "insert into datos_personales ";
    $SQL .= " (nombre, nif, nacido, altura) values ";
    $SQL .= " ('Marco Antoni Ferrer', '56789432D', '2003-02-08', 1.67)";

    //Ejecutamos y si no es False, todo ha ido bien.
    if (!$Conexion->query($SQL))
        echo "Error: ". $Conexion->error;
    else
        echo "Valores insertados correctamente";

    $Conexion->close();
?>