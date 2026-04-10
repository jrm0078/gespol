CREATE TABLE usuario (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(150),
email VARCHAR(150) UNIQUE,
contrasenia VARCHAR(255),
rol ENUM('Superadmin','Admin','Usuario') DEFAULT 'Usuario',
activo TINYINT(1) DEFAULT 1
);

INSERT INTO usuario(nombre,email,contrasenia,rol,activo)
VALUES('admin','admin@admin.com','$2y$10$qxH7K6.97d.7Q5pZG.B4O.wCvV5kJ3r.1O2C2g8V8A9K6D5E4C3B2','Superadmin',1);
