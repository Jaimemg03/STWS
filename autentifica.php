<?php
    include "conexion.php";
    $usuario = $clave = '';

    if (isset($_POST["usuario"])) $usuario = $_POST["usuario"];
    if (isset($_POST["clave"])) $clave = $_POST["clave"];
    
    if ($usuario != '' && $clave != '')
    {
        // Buscar solo por usuario (nunca enviar la contraseña al SQL)
        $stmt = $Conexion->prepare("select id, clave from usuarios where usuario = ?");
        if (!$stmt) 
        { 
            header('location: index.html');
            exit;
        }
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $Resultado = $stmt->get_result();
        $stmt->close();
        
        if ($Tupla = $Resultado->fetch_assoc())
        {
            // Verificar si la contraseña enviada coincide con el hash guardado
            if (password_verify($clave, $Tupla["clave"]))
            {
                session_start();
                $_SESSION["USUARIO_ID"] = $Tupla["id"];
                $_SESSION["USUARIO_NOMBRE"] = $usuario;

                header('location: Lista.php?Accion=Recibidos');
                exit;
            }
        }
        
        // Usuario o contraseña incorrectos
        header('location: index.html');
        exit;
    }
    else
    {
        header('location: index.html');
        exit;
    }
?>