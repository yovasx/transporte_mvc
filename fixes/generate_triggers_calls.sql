-- Llamadas para generar triggers de auditoría para cada tabla

CALL generate_audit_triggers('usuario', 'id_usuario', 'id_usuario,nombre,apellido_paterno,apellido_materno,correo,direccion,telefono,password,rol,estado');
CALL generate_audit_triggers('parada', 'id_parada', 'id_parada,nombre_parada,latitud,longitud,estado');
CALL generate_audit_triggers('ruta', 'id_ruta', 'id_ruta,nombre_ruta,hora_inicio,hora_final,estado,id_linea,puntos');
CALL generate_audit_triggers('linea', 'id_linea', 'id_linea,nombre_linea,color_linea,tramo_largo,tramo_corto');
CALL generate_audit_triggers('tarifa', 'id_tarifa', 'id_tarifa,id_ruta,fecha_inicio,fecha_fin,monto_estudiante,monto_personaMayor,monto_personaRegular');
CALL generate_audit_triggers('sindicato', 'id_sindicato', 'id_sindicato,nombre_sindicato,idioma');
