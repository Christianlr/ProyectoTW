-- Active: 1685277857137@@127.0.0.1@3306@quejate
DROP TABLE log;
CREATE TABLE log (
    id int(11) NOT NULL AUTO_INCREMENT,
    fecha datetime DEFAULT NULL,
    descripcion text COLLATE utf8_spanish2_ci,
    PRIMARY KEY (id)
);

DROP TABLE usuarios;
CREATE TABLE usuarios (
    email varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
    nombre varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
    apellidos varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
    password char(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
    telefono varchar(11) COLLATE utf8_spanish2_ci DEFAULT NULL,
    direccion varchar(200) COLLATE utf8_spanish2_ci DEFAULT NULL,
    foto MEDIUMBLOB,
    estado char(32) COLLATE utf8_spanish2_ci DEFAULT NULL,
    rol varchar(15) COLLATE utf8_spanish2_ci DEFAULT NULL,
    PRIMARY KEY (email)
);

DROP TABLE incidencias;
CREATE TABLE incidencias (
    id int(11) NOT NULL AUTO_INCREMENT,
    titulo varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
    descripcion text COLLATE utf8_spanish2_ci,
    fecha datetime DEFAULT NULL,
    lugar varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
    keywords varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
    id_usuario varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
    estado varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_incidencia_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (email) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE fotos;
CREATE TABLE fotos (
    id int(11) NOT NULL AUTO_INCREMENT,
    fotografia MEDIUMBLOB,
    id_incidencia int(11) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_fotos_incidencia FOREIGN KEY (id_incidencia) REFERENCES incidencias (id) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE valoraciones;
CREATE TABLE valoraciones (
    id int(11) NOT NULL AUTO_INCREMENT,
    id_usuario varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
    id_incidencia int(11) NOT NULL,
    valoracion tinyint(1) DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_valoraciones_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (email) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_valoraciones_incidencia FOREIGN KEY (id_incidencia) REFERENCES incidencias (id) ON DELETE CASCADE ON UPDATE CASCADE

);

DROP TABLE comentarios;
CREATE TABLE comentarios(
    id int(11) NOT NULL AUTO_INCREMENT,
    id_usuario varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
    id_incidencia int(11) NOT NULL,
    comentario text COLLATE utf8_spanish2_ci,
    fecha datetime DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_comentarios_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (email) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_comentarios_incidencia FOREIGN KEY (id_incidencia) REFERENCES incidencias (id) ON DELETE CASCADE ON UPDATE CASCADE
);