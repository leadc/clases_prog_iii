create table usuarios(
	id int primary key AUTO_INCREMENT,
    nombre varchar(50) not null,
    clave varchar(20) not null,
    sexo varchar(10) not null,
    perfil varchar(10) not null
)