<?php
session_start();
$path = 'lib/';

function login($path,$user,$pass){
  date_default_timezone_set('America/Mexico_City');
  require_once($path."baseDatos/conexion.php");
  $sql = "SELECT * FROM Doctor WHERE user = '$user' and password = '$pass'";
  $res = mysqli_query($conexion, $sql) or die ("error al consultar "+mysqli_connect_error());
  $data = [];
  if(mysqli_num_rows($res)==1){
    if($reg = mysqli_fetch_array($res)){
      $data['hecho'] = 1;
      $data['mensaje'] = 'Se ha logueado correctamente';
      $_SESSION['id'] = $reg['id'];
      $_SESSION['user'] = $user;
    }
  }else{
    $data['hecho'] = -1;
    $data['mensaje'] = 'No se encontro la combinación usuario/contraseña';
  }

  return $data;
}

  function obtenerSesion($path){
    require_once($path."baseDatos/conexion.php");
    $data = [];
    if(!$_SESSION){
      $data['hecho'] = -1;
      $data['mensaje'] = 'Usted no esta logueado,sera redireccionado';
    }else{
      $data['hecho'] = 1;
      $data['mensaje'] = 'Bienvenido';
      $data['id'] = $_SESSION['id'];
      $data['user'] = $_SESSION['user'];
      $sql = "SELECT * FROM Doctor WHERE id = '".$data['id']."'";
      $res = mysqli_query($conexion, $sql) or die ("error al consultar "+mysqli_connect_error());
      if($reg = mysqli_fetch_array($res)){
        $data['nombre'] = $reg['nombre']." ".$reg['aPaterno']." ".$reg['aMaterno'];
      }
    }

    return $data;
  }

  function buscar($path,$cadena,$id){
    require_once($path."baseDatos/conexion.php");
    $sql = "SELECT id,nombre,aPaterno,aMaterno,direccion,telefono,doctor,sexo,fechaNacimiento  FROM Paciente WHERE doctor = '$id' and  (nombre LIKE '%".$cadena."%' or aPaterno  LIKE '%".$cadena."%' or aMaterno  LIKE '%".$cadena."%' or direccion  LIKE '%".$cadena."%' or telefono  LIKE '%".$cadena."%' )";
    $res = mysqli_query($conexion, $sql) or die ("error al consultar "+mysqli_error($conexion));
    $data = [];

    while($reg = mysqli_fetch_assoc($res)){
      $data[] = $reg;
    }
    return $data;
  }

  function addPaciente($path,$form,$files){
    require_once($path.'baseDatos/conexion.php');
    $archivo_temporal = $files['tmp_name'];
    $tipo = $files['type'];
    $name = $files['name'];
    if($tipo == "application/force-download"){
      $tipo = "application/pdf";
    }
    $fp = fopen($archivo_temporal, 'r+b');
    $data = fread($fp, filesize($archivo_temporal));
    fclose($fp);
    $data = mysqli_real_escape_string($conexion,$data);
    $sql = "INSERT INTO Paciente VALUES (null,'".$form['nombre'].
                                              "','".$form['aPaterno'].
                                              "','".$form['aMaterno'].
                                              "','".$form['fechaNacimiento'].
                                              "','".$form['telefono'].
                                              "','".$form['direccion'].
                                              "',now()".
                                              ",'".$form['sexo'].
                                              "','".$form['doctor'].
                                              "','".$data.
                                              "','".$tipo."')";
    $resultado = mysqli_query($conexion, $sql) or die(mysqli_error($conexion));
    $data = [];
    if ($resultado){
      $data['hecho'] = 1;
      $data['mensaje'] = "El paciente ha sido agregado exitosamente.";
    }else{
      $data['hecho'] = -1;
      $data['mensaje'] = "Ocurrio algun error al agregar el paciente.";
    }

    return $data;
  }


  function addCita($path,$form,$files){
    require_once($path.'baseDatos/conexion.php');
    $sqlCita = "INSERT INTO Cita VALUES (null,'".$form['descripcion']."','".$form['fecha']."','".$form['paciente']."')";
    $resultado = mysqli_query($conexion, $sqlCita) or die(mysqli_error($conexion));
    $data = [];
    if ($resultado){
      $sqlId = "SELECT id FROM Cita order by id desc limit 1";
      $resultadoId = mysqli_query($conexion, $sqlId) or die(mysqli_error($conexion));
      if($regid = mysqli_fetch_array($resultadoId)){
        for($i = 0; $i < count($files); $i++){
          $archivo_temporal = $files[$i]['tmp_name'];
          $tipo = $files[$i]['type'];
          $name = $files[$i]['name'];
          if($tipo == "application/force-download"){
            $tipo = "application/pdf";
          }
          $fp = fopen($archivo_temporal, 'r+b');
          $data = fread($fp, filesize($archivo_temporal));
          fclose($fp);
          $data = mysqli_real_escape_string($conexion,$data);
          $sqlFile = "INSERT INTO Files VALUES (null,'".$data."','".$tipo."','".$name."','descripcion','".$regid['id']."')";
          $resultadoFile = mysqli_query($conexion, $sqlFile) or die(mysqli_error($conexion));
          if (!$resultadoFile){
            $data['hecho'] = -1;
            $data['mensaje'] = "Un archivo no se ha podido guardar";
            return $data;
          }

          $archivo_temporal = "";
          $tipo = "";
          $name = "";
          $data = "";
        }
      }else{
        $data['hecho'] = -1;
        $data['mensaje'] = "No se pudo obtener la ultima cita";
        return $data;
      }
    }else{
      $data['hecho'] = -1;
      $data['mensaje'] = "Ocurrio algun error al crear la cita paciente.";
      return $data;
    }

    $data['hecho'] = 1;
    $data['mensaje'] = "Cita guardada correctamente";
    return $data;
  }

  function getCitas($path,$id){
    require_once($path.'baseDatos/conexion.php');
    $sql = "SELECT * FROM Cita WHERE paciente = '$id' order by fecha desc";
    $resultado = mysqli_query($conexion, $sql) or die(mysqli_error($conexion));
    $data = [];
    while($reg = mysqli_fetch_assoc($resultado)){
      $data[] = $reg;
    }
    return $data;
  }

  function getArchivos($path,$id){
    require_once($path.'baseDatos/conexion.php');
    $sql = "SELECT id,nombre,tipo,descripcion,cita FROM Files WHERE cita = '$id'";
    $resultado = mysqli_query($conexion, $sql) or die(mysqli_error($conexion));
    $data = [];
    while($reg = mysqli_fetch_assoc($resultado)){
      $data[] = $reg;
    }
    return $data;
  }

  if($_POST){
    $op = $_POST['op'];
  }else{
    $data = json_decode(file_get_contents('php://input'), true);
    $op = $data['op'];
  }


  if($op == 1){
    $resultado = obtenerSesion($path);
    print  json_encode($resultado);
  }

  if($op == 2){
    $user = $data['user'];
    $clave = $data['clave'];
    $resultado = login($path,$user,$clave);
    print json_encode($resultado);
  }

  if($op == 3){
    $cadena = $data['search'];
    $id = $data['id'];
    $resultado = buscar($path,$cadena,$id);
    print json_encode($resultado);
  }

  if($op == 4){
    $form = $_POST;
    $files = $_FILES[0];
    $resultado = addPaciente($path,$form,$files);
    print json_encode($resultado);
  }

  if($op == 5){
    $form = $_POST;
    $files = $_FILES;
    //print json_encode($files);
    $resultado = addCita($path,$form,$files);
    print json_encode($resultado);
  }

  if($op == 6){
    $id = $data['paciente'];
    $resultado = getCitas($path,$id);
    print json_encode($resultado);
  }

  if($op == 7){
    $id = $data['cita'];
    $resultado = getArchivos($path,$id);
    print json_encode($resultado);
  }
 ?>
