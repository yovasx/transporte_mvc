-- Inserts a default linea to allow creating rutas that reference an existing linea
-- Run this on your local DB (transportedb) if you want a sample linea

INSERT INTO `linea` (nombre_linea, color_linea, tramo_largo, tramo_corto)
VALUES ('Línea 1', '#ff0000', 'Terminal A - Terminal B', 'Corto');

-- If you want to inspect existing lineas:
-- SELECT * FROM linea;
