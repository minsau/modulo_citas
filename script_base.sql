
DROP DATABASE citas;
CREATE DATABASE citas;
use citas;

CREATE TABLE Doctor(
  id int not null auto_increment primary key,
  nombre varchar(100),
  aPaterno varchar(100),
  aMaterno varchar(100),
  user varchar(50),
  password varchar(50),
  fechaAlta timestamp
);

INSERT INTO Doctor VALUES (null,'Saúl','Gómez','Navarrete','minsau','esasistemas',now());
INSERT INTO Doctor VALUES (null,'Efren','Cruz','Cruz','joker','esasistemas',now());
INSERT INTO Doctor VALUES (null,'Marco','Morales','Lopez','markus','esasistemas',now());

CREATE TABLE Paciente(
  id int not null auto_increment primary key,
  nombre varchar(100),
  aPaterno varchar(100),
  aMaterno varchar(100),
  fechaNacimiento date,
  telefono varchar(15),
  direccion text,
  fechaAlta timestamp,
  sexo varchar(50),
  doctor int not null,
  foto LONGBLOB,
  tipo VARCHAR(255),
  FOREIGN KEY (doctor) REFERENCES Doctor(id)
);

INSERT INTO Paciente VALUES (null,'Paciente1','aPaterno','aMaterno','1994-05-25','7121424290','Por alla',now(),'Masculino',1,null,null);
INSERT INTO Paciente VALUES (null,'Paciente2','aPaterno','aMaterno','1994-05-15','7121424290','Por aca',now(),'Masculino',3,null,null);
INSERT INTO Paciente VALUES (null,'Paciente3','aPaterno','aMaterno','1994-01-18','7121424290','Por asd',now(),'Masculino',1,null,null);
INSERT INTO Paciente VALUES (null,'Paciente4','aPaterno','aMaterno','1994-02-25','7121424290','Por alasdasdla',now(),'Masculino',2,null,null);
INSERT INTO Paciente VALUES (null,'Paciente5','aPaterno','aMaterno','1994-05-23','7121424290','Poasdwefasdfr alasdasdla',now(),'Femenino',1,null,null);
INSERT INTO Paciente VALUES (null,'Paciente6','aPaterno','aMaterno','1994-09-25','7121424290','Por asdaffadsflla',now(),'Femenino',2,null,null);


CREATE TABLE Cita(
  id int not null auto_increment primary key,
  descripcion text,
  fecha timestamp,
  paciente int not null,
  FOREIGN KEY (paciente) REFERENCES Paciente(id)
);


CREATE TABLE Files(
	id INT NOT NULL auto_increment,
	archivo LONGBLOB,
	tipo VARCHAR(255),
	nombre VARCHAR(255),
  descripcion text,
	PRIMARY KEY (id),
  cita int not null,
  FOREIGN KEY (cita) REFERENCES Cita(id)
);
