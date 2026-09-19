-- Ejecutar solo si la base fue creada con la estructura anterior del día 9.
USE zdtecnoc_db;

UPDATE usuarios SET rol = 'administrador' WHERE rol = 'admin';
UPDATE usuarios SET rol = 'vendedor' WHERE rol = 'empleado';

ALTER TABLE usuarios
    CHANGE COLUMN password clave_hash VARCHAR(255) NOT NULL,
    CHANGE COLUMN fecha_creacion creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    MODIFY COLUMN rol ENUM('administrador', 'vendedor', 'consultor') DEFAULT 'consultor',
    ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1,
    ADD COLUMN bloqueado_hasta DATETIME NULL;

CREATE TABLE IF NOT EXISTS intentos_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correo VARCHAR(100) NOT NULL,
    intentos INT NOT NULL DEFAULT 0,
    ultimo_intento DATETIME NOT NULL,
    UNIQUE KEY uq_intento_correo (correo),
    INDEX idx_ultimo_intento (ultimo_intento)
) ENGINE=InnoDB;
