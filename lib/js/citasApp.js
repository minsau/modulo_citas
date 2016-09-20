'use strict'

var angular_module = angular.module('citasApp',[]);

angular_module.controller('citasController',function($scope,$http){
  $scope.persona = {};
  $scope.paciente = "";
  $scope.doctor = "";
  $scope.datosSesion = {};
  $scope.cita = {};
  $scope.palabra = "";
  $scope.des = {};
  $scope.archivos = {};
  $scope.url = "";
  $http.post('citas.php',{op: 1}).success(function(data){
    $scope.datosSesion = data;
    //console.log(data);
    if($scope.datosSesion.hecho == -1){
      alert($scope.datosSesion.mensaje);
      window.location.href = 'login.php';
    }else{
      //console.log("Se ha logueado correctamente");
    }
  });

  $scope.nuevaCita = function(idDoctor,idPaciente){
    console.log(idPaciente);
    console.log(idDoctor);
    $scope.paciente = idPaciente;
    $scope.doctor = idDoctor;
    $("#nuevaCitaDiv").show();
  }

  $scope.setRuta = function(id){
    console.log(id);
    $scope.url = "getArchivo.php?id="+id;
  }

  $scope.mostrarCitas = function(id){
    $("#citasDiv").show();
    $scope.citaActual = id;
    $http.post('citas.php',{op: 6,paciente: id}).success(function(citas){
      $scope.citasPersona = citas;
    });
  }

  $scope.obtenerArchivos = function(id){
    console.log("Entro "+ id);
    $http.post('citas.php',{op: 7,cita: id}).success(function(files){
      $scope.archivos = files;
      console.log(files);
    });
  }

  $scope.buscar = function(){
    //console.log($scope.datosSesion.id);
    $http.post('citas.php',{op: 3,search:$scope.palabra,id: $scope.datosSesion.id}).success(function(datos){
      $scope.pacientes = datos;
      //console.log(datos);
    });
  }

  var formdata = new FormData();
  $scope.getTheFiles = function ($files) {
    angular.forEach($files, function (value, key) {
      console.log(key+" "+value);
      formdata.append(key, value);
      console.log(formdata);
    });
  }

  var formdataCita = new FormData();
  $scope.getTheFilesCita = function ($files) {
    angular.forEach($files, function (value, key) {
      formdataCita.append(key, value);
    });
  }

  $scope.guardarCita = function(idPaciente,idDoctor){
    //console.log($("#fecha").val());
    $("#terminado").hide();
    $("#cargando").show();
    formdataCita.append('op',5);
    formdataCita.append('paciente',idPaciente);
    formdataCita.append('doctor',idDoctor);
    formdataCita.append('descripcion',$scope.cita.descripcion);
    formdataCita.append('fecha',$("#fecha").val());
    console.log(formdataCita);
    var request = {
      method: 'POST',
      url: 'citas.php',
      data: formdataCita,
      headers: {
        'Content-Type': undefined
      }
    };

    $http(request).success(function (d) {
      $scope.des = d;
      //console.log(d);
      $("#cargando").hide();
      $("#terminado").show();
    })
    .error(function () {
    });
  }

  $scope.agregarPaciente = function(id){
    console.log(id);


    formdata.append('op',4);
    formdata.append('nombre', $scope.persona.nombre);
    formdata.append('aPaterno', $scope.persona.aPaterno);
    formdata.append('aMaterno', $scope.persona.aMaterno);
    formdata.append('fechaNacimiento', $scope.persona.fechaNacimiento);
    formdata.append('telefono', $scope.persona.telefono);
    formdata.append('direccion', $scope.persona.direccion);
    formdata.append('sexo', $scope.persona.sexo);
    formdata.append('doctor', id);


    var request = {
      method: 'POST',
      url: 'citas.php',
      data: formdata,
      headers: {
        'Content-Type': undefined
      }
    };

    // SEND THE FILES.
    $http(request).success(function (d) {
      $scope.resultado = d;
    })
    .error(function () {
    });
  }
});

angular_module.directive('ngFiles', ['$parse', function ($parse) {

  function fn_link(scope, element, attrs) {
    var onChange = $parse(attrs.ngFiles);
    element.on('change', function (event) {
      onChange(scope, { $files: event.target.files });
    });
  };

  return {
    link: fn_link
  }
} ]);
