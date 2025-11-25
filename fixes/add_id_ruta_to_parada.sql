-- Active: 1763995601119@@127.0.0.1@3306@transportedb
-- Agrega columna id_ruta a la tabla parada para asociar paradas a rutas
ALTER TABLE `parada`
  ADD COLUMN `id_ruta` INT NULL AFTER `id_parada`;

-- Opcional: crear FK si la tabla ruta existe (descomentar si se desea)
-- ALTER TABLE `parada` ADD CONSTRAINT fk_parada_ruta FOREIGN KEY (id_ruta) REFERENCES ruta(id_ruta) ON DELETE SET NULL ON UPDATE CASCADE;
