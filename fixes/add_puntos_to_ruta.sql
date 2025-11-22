-- Añadir columna 'puntos' a la tabla ruta para almacenar los puntos de la ruta en JSON
ALTER TABLE ruta ADD COLUMN puntos LONGTEXT NULL COMMENT 'Puntos de la ruta en formato JSON';
