-- Actualizar el enum del campo rol en la tabla usuario
-- Cambiar de ('admin','usuario','operador') a ('usuario','admin')

ALTER TABLE `usuario` MODIFY COLUMN `rol` enum('usuario','admin') DEFAULT 'usuario';

-- Actualizar usuarios existentes que tengan 'operador' a 'usuario'
UPDATE `usuario` SET `rol` = 'usuario' WHERE `rol` = 'operador';

-- Asegurar que al menos un usuario sea admin (si no hay ninguno)
INSERT IGNORE INTO `usuario` (`nombre`, `apellido_paterno`, `correo`, `password`, `rol`, `estado`)
VALUES ('Admin', 'Sistema', 'admin@transporte.com', '$2y$10$example_hash', 'admin', 'Activo');
