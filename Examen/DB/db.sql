create table usuarios(
	id int primary key AUTO_INCREMENT,
    nombre varchar(50) not null,
    clave varchar(20) not null,
    sexo varchar(10) not null,
    perfil varchar(15) not null
)

CREATE TABLE Compras
(
   id          INT(20) PRIMARY KEY AUTO_INCREMENT NOT NULL,
   fecha       DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
   articulo    VARCHAR(50) NOT NULL,
   precio      VARCHAR(20) NOT NULL,
   idusuario int(20) not null REFERENCES usuarios(id)
)

CREATE TABLE accesosLog
(
   id            INT(20) PRIMARY KEY AUTO_INCREMENT NOT NULL,
   idusuario     INT(20) NOT NULL REFERENCES usuarios(id),
   metodo        VARCHAR(50) NULL,
   ruta          VARCHAR(80) NULL,
   fechayhora    DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
)