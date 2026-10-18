<html>
<head><title>Listado de mensajes</title>
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/mensaje.css">
	<style>@import url(http://fonts.googleapis.com/css?family=Open+Sans);</style>
</head>
<body>
<script>
	function Eliminar(ID)
	{
		if (confirm("Haga clic en Aceptar para eliminar el mensaje definitivamente"))
		{
			document.location.href="?Accion=Eliminar&ID=" + ID;
		}
	}
	
</script>
<?php
	session_start();
	
	include "conexion.php";
	
	if (!isset($_SESSION["USUARIO_ID"]) || !isset($_SESSION["USUARIO_NOMBRE"])) {
		header('location: index.html');
		exit;
	}
	
	$Conexion = CrearConexion();
	
	$Accion = isset($_REQUEST["Accion"]) ? $_REQUEST["Accion"] : '';
	$Usuario = $_SESSION["USUARIO_ID"];
	$Nombre = $_SESSION["USUARIO_NOMBRE"];
	$ID = isset($_REQUEST["ID"]) ? (int)$_REQUEST["ID"] : 0;
	
	echo "<h2 class='bienvenida'>Bienvenido/a $Nombre</h2>";	
	
	if ($Accion == "") $Accion = "Recibidos";
	if ($Accion == "Insertar" || $Accion == "Modificar")
	{
		$Para = isset($_REQUEST["Para"]) ? (int)$_REQUEST["Para"] : 0;
		$Mensaje = isset($_REQUEST["Mensaje"]) ? $_REQUEST["Mensaje"] : '';

		if ($Para <= 0) {
			die("Error: Debes seleccionar un destinatario válido.");
		}
		
		if ($Accion == "Insertar") 
		{
			$stmt = $Conexion->prepare("insert into mensajes (Usuario, Para, Mensaje) values (?, ?, ?)");
			if (!$stmt) die("Error en prepare: " . $Conexion->error);
			$stmt->bind_param("iis", $Usuario, $Para, $Mensaje);
			$stmt->execute();
			$stmt->close();
		}
		else
		{
			$stmt = $Conexion->prepare("update mensajes set Usuario = ?, Para = ?, Mensaje = ? where id = ?");
			if (!$stmt) die("Error en prepare: " . $Conexion->error);
			$stmt->bind_param("iisi", $Usuario, $Para, $Mensaje, $ID);
			$stmt->execute();
			$stmt->close();
		}
		$Accion = "Recibidos";
	}
	
	if ($Accion == "Eliminar" && $ID != "") 
	{
		$stmt = $Conexion->prepare("delete from mensajes where id = ? and (Para = ? or Usuario = ?)");
		if (!$stmt) die("Error en prepare: " . $Conexion->error);
		$stmt->bind_param("iii", $ID, $Usuario, $Usuario);
		$stmt->execute();
		$stmt->close();
	}

	if ($Conexion)
	{
		//! $SDE = $_REQUEST["SDE"];
		//! $SMensaje = $_REQUEST["SMensaje"];
		$SDE = isset($_REQUEST["SDE"]) ? (int)$_REQUEST["SDE"] : 0;
		$SMensaje = isset($_REQUEST["SMensaje"]) ? $_REQUEST["SMensaje"] : '';
		
		echo "<form method='post'>";
		$SQL = "select * from usuarios where ID <> $Usuario order by Usuario";
		$Resultado = Ejecutar($Conexion, $SQL);
		echo "<fieldset class='Formulario'><legend><strong>Busquedas en $Accion</strong></legend><div class='SEtiqueta_DE'>";
		if ($Accion == "Recibidos") echo "De "; else echo "Para ";
		echo "</div><div class='SValor'><select name='SDE' class='SDE'>";
		echo ("<option value=''></option>");
		while ($RTemp = mysqli_fetch_array($Resultado)) 
		{
			echo ("<option value='". $RTemp["id"]."'");
			if ($RTemp["id"] == $SDE)  echo (" selected='selected'");
			echo (">" . $RTemp["usuario"] . "</option>");
		}
		echo ("</select></div>\n");
		?>
		
		<div class="SEtiqueta_MENSAJE">Mensaje</div><div class="SValor"><input type="text" class='SMensaje' name="SMensaje" value="<?php echo $SMensaje; ?>" /></div>
		<button class="btn btn-primary btn-block btn-large SValor" type="submit" name="Accion" value="<?=$Accion?>">Buscar</button>
		<div class="btn btn-primary btn-block btn-large SValor" onclick="javascript:location.href='?Accion=Recibidos'">Recibidos</div>
		<div class="btn btn-primary btn-block btn-large SValor" onclick="javascript:location.href='?Accion=Enviados'">Enviados</div>
		
		</fieldset></form>

		<?php  
		
		if ($Accion == "Recibidos")
		{
			$SQL = "SELECT mensajes.ID as ID, Mensaje, usuarios.Usuario as Remitente, Fecha FROM mensajes left join usuarios on mensajes.usuario = usuarios.id ";
			$SQL .= " where mensajes.Para = $Usuario ";
			if ($SDE != 0) $SQL .= " and mensajes.usuario = $SDE ";

		}
		else
		{
			$SQL = "SELECT mensajes.ID as ID, Mensaje, usuarios.Usuario as Destinatario, Fecha FROM mensajes left join usuarios on mensajes.Para = usuarios.id ";
			$SQL .= " where mensajes.Usuario = $Usuario";
			if ($SDE != 0) $SQL .= " and mensajes.Para = $SDE ";
		}
		
		if ($SMensaje != "") $SQL .= " and Mensaje like '%" . $Conexion->real_escape_string($SMensaje) . "%' ";
		$SQL .= " order by id desc";

		$Resultado = Ejecutar($Conexion, $SQL);

		echo "<div class='Contenedor'><div class='Lista'>";
		while ($Tupla = mysqli_fetch_array($Resultado ,MYSQLI_ASSOC))
		{
			echo "<div class='Tupla'>\n";

			
			if ($Accion == "Recibidos")
				echo "<div class='Remitente'>Mensaje de " . $Tupla["Remitente"] . "</div>";
			else
				echo "<div class='Remitente'>Mensaje enviado a " . $Tupla["Destinatario"] . "</div>";
			
			echo "<div class='Fecha'>Recibido el " . DameFecha($Tupla["Fecha"]) . "</div>";
			echo "<div class='Separador'>&nbsp;</div>\n";
			echo "<div class='Mensaje'>" . str_replace("\n", "<br />", $Tupla["Mensaje"]) . "</div>\n";
			echo "<div class='Separador'>&nbsp;</div>\n";
	
			echo "<div class=\"Boton1\"><button class=\"btn btn-primary btn-block btn-large ";
			if ($Accion == "Recibidos") echo (" Desactivado ");
			echo "\" onclick=\"javascript:location.href='Mensajes.php?Accion=Editar&ID=".$Tupla["ID"]."'\">Editar</button></div>";
			echo "<div class=\"Boton2\"><button class=\"btn btn-primary btn-block btn-large\" onclick=\"javascript:Eliminar(".$Tupla["ID"].")\">Eliminar</button></div>";
			echo "</div>\n";
			echo "<div class='Separador'>&nbsp;</div>\n";
			echo "<div class='Separador'>&nbsp;</div>\n";
		}
	
		echo "<div class='Separador'>&nbsp;</div>\n";
		echo "<div class='Nuevo'><button class=\"btn btn-primary btn-block btn-large\" onclick=\"javascript:location.href='Mensajes.php'\">Nuevo mensaje</button>\n";
		echo "</div>";
	}
		
	mysqli_close($Conexion);

?>