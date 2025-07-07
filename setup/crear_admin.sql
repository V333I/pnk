-- Insertar usuario administrador
INSERT INTO `usuarios` (`rut`, `nombres`, `ap_paterno`, `ap_materno`, `usuario`, `clave`, `estado`, `tipo_usuario`) 
VALUES ('11111111-1', 'Administrador', 'Sistema', 'PNK', 'admin@pnk.cl', MD5('Admin123!'), 1, 'admin'); 