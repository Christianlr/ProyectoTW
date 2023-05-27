-- Active: 1684834928654@@127.0.0.1@3306@quejate
insert into usuarios values("admin@admin.admin", "Enrique", "Araque Espinosa", SHA2('contraseña', 256), null, null, null, "activo", "administrador");
insert into usuarios values("christian@admin.admin", "Christian", "López Román", SHA2('Swap1234', 256), "607890670", 'c/periodista, 1, 4B', null, "activo", "administrador");
insert into usuarios values("colab@gmail.com", "Pepa", "Cerda Pig", SHA2('pepa', 256), null, null, null, "activo", "colaborador");
insert into usuarios values("jacinto@hotmail.com", "Jacinto", "Pérez Castillo", SHA2('jacinto', 256), null, null, null, "activo", "colaborador");
insert into usuarios values("juan@libros.com", "Juan", "Gómez Jurado", SHA2('juan', 256), null, null, null, "activo", "colaborador");
insert into usuarios values("rafa@ugr.es", "Rafael", "Fuente Barranco", SHA2('rafa', 256), null, null, null, "activo", "colaborador");
select * from usuarios;

describe  incidencias;

select * from incidencias;

insert into incidencias(titulo, id_usuario) values("Primera incidencia", "jacinto@hotmail.com");
insert into incidencias(titulo, id_usuario) values("Segunda incidencia", "rafa@ugr.es");
insert into incidencias(titulo, id_usuario) values("Tercera incidencia", "rafa@ugr.es");
insert into incidencias(titulo, id_usuario) values("Cuarta incidencia", "rafa@ugr.es");
insert into incidencias(titulo, id_usuario) values("Quinta incidencia", "jacinto@hotmail.com");
insert into incidencias(titulo, id_usuario) values("Sexta incidencia", "rafa@ugr.es");
insert into incidencias(titulo, id_usuario) values("Septima incidencia", "juan@libros.com");
insert into incidencias(titulo, id_usuario) values("Octaba incidencia", "juan@libros.com");
insert into incidencias(titulo, id_usuario) values("Novena incidencia", "christian@admin.admin");

insert into incidencias(titulo, id_usuario) values("Decima incidencia", "juan@libros.com");


select id_usuario, count(*) as count 
from incidencias 
group by id_usuario
order by count desc
limit 3;

select nombre, apellidos from usuarios where email='rafa@ugr.es';
delete from usuarios where nombre = "Enrique";

Select * from usuarios where nombre = 'Enrique';

SELECT * FROM usuarios WHERE email='admin@admin.admin';

select name from usuarios;

select rol from usuarios where email='juan@libros.com';

update usuarios
set rol = 'administrador'
where email = 'juan@libros.com';

select * from usuarios;