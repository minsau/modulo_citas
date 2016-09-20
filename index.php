<?php
session_start();
$path = 'lib/';
?>

<!DOCTYPE html>
<html lang="es" ng-app="citasApp">
  <head>
    <meta charset="utf-8">
    <title>Mi cuenta</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $path; ?>css/bootstrap.css">
    <link href="<?php echo $path; ?>fileinput/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?php echo $path; ?>css/bootstrap-datetimepicker.css">
    <style media="screen">
      #pacientes{
        overflow-y: scroll;
        height: 600px;
        position: relative;
      }
      #citasDiv{
        overflow-y: scroll;
        height: 600px;
        border: 1px solid;
        position: relative;
      }

      #nuevaCita{
        overflow-y: scroll;
        height: 600px;
        border: 1px solid;
        position: relative;
      }
    </style>
  </head>
  <body ng-controller="citasController">
    <div id="agregarPaciente" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Agregar paciente</h4>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="">Nombre: </label>
                <input type="text" ng-model="persona.nombre" value="">
              </div>

              <div class="form-group">
                <label for="">Apellido Paterno: </label>
                <input type="text" ng-model="persona.aPaterno" value="">
              </div>

              <div class="form-group">
                <label for="">Apellido Materno: </label>
                <input type="text" ng-model="persona.aMaterno" value="">
              </div>

              <div class="form-group">
                <label for="">Fecha Nacimiento: </label>
                <input type="date" ng-model="persona.fechaNacimiento" value="">
              </div>

              <div class="form-group">
                <label for="">Telefono: </label>
                <input type="number" ng-model="persona.telefono" value="">
              </div>

              <div class="form-group">
                <label for="">Direccion: </label>
                <input type="text" ng-model="persona.direccion" value="">
              </div>

              <div class="form-group">
                <label for="">Sexo: </label>
                <input type="text" ng-model="persona.sexo" value="">
              </div>

              <div class="form-group">
                <label class="control-label">Foto:</label>
                <input id="foto" type="file" class="file" ng-files="getTheFiles($files)" data-show-preview="true">
              </div>
              <div class="">
                {{resultado.mensaje}}
              </div>
              <input type="button" value="Agregar" ng-click="agregarPaciente(datosSesion.id)">
            </form>
          </div>
        </div>

      </div>
    </div>


    <label><span class="glyphicon glyphicon-user"></span>{{datosSesion.nombre}}</label>

    <div class="row">
      <div class="col-md-4" id="pacientes">
        <div class="form-group">
          <div class="input-group">
            <input type="search" class="form-control" id="palabra" ng-change="buscar()" ng-model="palabra" placeholder="Buscar paciente">
            <div class="input-group-addon"><span class="glyphicon glyphicon-search"></span></div>
          </div>
        </div>
        <button class="btn btn-sm btn-default" data-toggle="modal" data-target="#agregarPaciente"><span class="glyphicon glyphicon-plus" style="color:green;"> </span>Nuevo paciente</button>
        <br>
        <br>
        <div class="panel panel-default" ng-repeat="paciente in pacientes" >
          <div class="panel-heading">{{paciente.nombre+ " " + paciente.aPaterno + " "+ paciente.aMaterno}}</div>
          <div class="panel-body">
            <img class="" src="{{'fotoPaciente.php?id='+paciente.id}}" width="70px" height="90px">
            Doctor: {{paciente.doctor}}
            Sexo: {{paciente.sexo}}
            Fecha de nacimiento: {{paciente.fechaNacimiento}}
            <button type="button" name="button" class="btn btn-default btn-sm" ng-click="mostrarCitas(paciente.id)">Ver citas</button>
          </div>
        </div>
      </div>

      <div class="col-md-4" style="display:none;" id="citasDiv">
        <button class="btn btn-sm btn-default" ng-click="nuevaCita(datosSesion.id,citaActual)"><span class="glyphicon glyphicon-plus" style="color:green;"> </span>Nuevo cita</button>
        <br>
        <br>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default" ng-repeat="val in citasPersona">
            <div class="panel-heading" role="tab" id="{{'heading_'+val.id}}">
              <h4 class="panel-title">
                <a role="button" ng-click="obtenerArchivos(val.id)" data-toggle="collapse" data-parent="#accordion" href="{{'#'+val.id}}" aria-expanded="false" aria-controls="{{val.id}}">
                  {{val.fecha}}
                </a>
              </h4>
            </div>
            <div id="{{val.id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="{{'heading_'+val.id}}">
              <div class="panel-body">
                {{val.descripcion}}
                <br><br><a data-toggle="modal" ng-click="setRuta(archivo.id)" data-target="#verArchivo" ng-repeat="archivo in archivos">Ver {{archivo.nombre}}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4" id="nuevaCitaDiv" style="display:none;">
        <form>
          <legend>Nueva cita para {{paciente}} {{doctor}}</legend>
          <div class="form-group">
            <label for="">Descripción</label>
            <textarea name="descripcion" class = "form-control" ng-model="cita.descripcion" rows="8" cols="40"></textarea>
          </div>

          <div class="form-group">
              <label for="">Fecha: </label>
              <input type="text" class = "form-control" id="fecha" ng-model="cita.fecha">
          </div>

          <div class="form-group">
            <label for="">Fotos, Videos o archivos.</label>
            <input id="archivos" type="file" class="file" multiple ng-files="getTheFilesCita($files)" data-show-preview="true">
          </div>
          <div id="cargando" style="display: none;">
            <img src="img/subiendo.gif" width="70px" width="70px" alt="" />
          </div>
          <div id="terminado" style="display: none;" class="alert alert-success">
            <p>
              {{des.mensaje}}
            </p>
          </div>
          <button type="button" name="button" ng-click="guardarCita(paciente,doctor)"> Guardar cita</button>
        </form>
      </div>
    </div>


    <!-- Modal -->
<div class="modal fade" id="verArchivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Ver archivo</h4>
      </div>
      <div class="modal-body">
        <iframe src="{{url}}" width="600px" height="500px"></iframe>
      </div>

    </div>
  </div>
</div>

    <script type="text/javascript" src="<?php echo $path; ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/angular.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/citasApp.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>fileinput/js/plugins/canvas-to-blob.min.js" ></script>
    <script type="text/javascript" src="<?php echo $path; ?>fileinput/js/fileinput.min.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>fileinput/js/locales/es.js"></script>
    <script type="text/javascript" src='<?php echo $path; ?>js/moment.min.js'></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/collapse.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/transition.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" >
      $("#foto").fileinput({
        showUpload: false,
        language: "es"
      });

      $("#archivos").fileinput({
        showUpload: false,
        language: "es"
      });

      $('#fecha').datetimepicker({
        format: 'Y-MM-DD HH:mm:ss'
      });

      $(".collapse").collapse();
      $('#accordion').collapse({hide: true})
    </script>
  </body>
</html>
