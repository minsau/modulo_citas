<?php
	session_start();
	$path = 'lib/';
	require_once($path.'baseDatos/conexion.php');
	$idArchivo = $_GET['id'];
	$sql = "SELECT foto,tipo FROM Paciente WHERE id = '$idArchivo'";
	$resultado = mysqli_query($conexion,$sql) or die("Error obteniendo archivo".mysqli_error($conexion));
	$datos = mysqli_fetch_array($resultado);

    $archivo = $datos['foto'];
    $tipo = $datos['tipo'];

    header("Content-type: $tipo");
    echo $archivo;
?>
