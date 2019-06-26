create table usuarios(
	legajo int primary key AUTO_INCREMENT,
    nombre varchar(50) not null,
    clave varchar(20) not null,
    tipo varchar(10) not null,
    email varchar(100),
    materiasDictadas varchar(500),
    foto varchar(100)
)

CREATE TABLE Materia
(
   id          INT(20) PRIMARY KEY AUTO_INCREMENT NOT NULL,
   nombre    VARCHAR(100) NOT NULL,
   cuatrimestre int(20) NOT NULL,
   cupos int(20) not null
)


CREATE TABLE alumnosInscriptos
(
   id          INT(20) PRIMARY KEY AUTO_INCREMENT NOT NULL,
   idAlumno    INT(20) NOT NULL FOREIGN KEY REFERENCES usuarios(id),
   idMateria int(20) NOT NULL KEY REFERENCES usuarios(id)
)
