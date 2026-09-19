USE zdtecnoc_db;

-- 1. Insertar Usuarios de prueba
INSERT INTO usuarios (nombre, correo, clave_hash, rol, activo) VALUES 
('Admin Principal', 'admin@zdtecnoc.com', '$2y$10$abcdefghijklmnopqrstuvwx', 'administrador', 1),
('Vendedor Soporte', 'soporte@zdtecnoc.com', '$2y$10$abcdefghijklmnopqrstuvwx', 'vendedor', 1);

-- 2. Insertar Categorías
INSERT INTO categorias (nombre, descripcion) VALUES 
('Herramientas de Corte', 'Discos y herramientas abrasivas para corte industrial'),
('Abrasivos Flexibles', 'Lijas y bandas para acabados de superficies'),
('Equipos de Protección', 'Elementos de seguridad industrial y EPP');

-- 3. Insertar 20 Productos
INSERT INTO productos (nombre, categoria_id, precio, stock) VALUES 
('Disco de Corte Fino 4.5"', 1, 4500.00, 150),
('Disco de Desbaste 7"', 1, 12000.00, 80),
('Lija de Agua Grano 100', 2, 1500.00, 300),
('Lija de Agua Grano 220', 2, 1500.00, 250),
('Casco de Seguridad Industrial', 3, 25000.00, 40),
('Guantes de Carnaza Reforzados', 3, 8500.00, 100),
('Disco Diamantado Segmentado', 1, 35000.00, 30),
('Bandas Abrasivas 3x21"', 2, 7000.00, 60),
('Gafas de Protección UV', 3, 6000.00, 120),
('Protector Auditivo de Copa', 3, 18000.00, 50),
('Disco de Corte Acero Inoxidable', 1, 5200.00, 200),
('Cepillo de Alambre Circular', 1, 14000.00, 45),
('Lija en Seco Grano 80', 2, 2000.00, 180),
('Mascarilla para Polvos N95', 3, 4000.00, 250),
('Botas de Seguridad con puntera', 3, 95000.00, 25),
('Disco Flap Zirconio 7"', 1, 16000.00, 70),
('Esponja Abrasiva Media', 2, 3000.00, 90),
('Filtro para Gases y Vapores', 3, 22000.00, 60),
('Piedra Esmeril de Banco', 1, 42000.00, 15),
('Rollo de Lija Esmeril', 2, 28000.00, 40);

-- 4. Insertar 10 Clientes
INSERT INTO clientes (nombre, telefono, correo, direccion) VALUES 
('Metalmecánica S.A.', '3101234567', 'contacto@metalmecanica.com', 'Zona Industrial Bogotá'),
('Construcciones Andinas', '3209876543', 'obras@candinas.com', 'Calle 80 No. 45-12'),
('Talleres El Libertador', '3154567890', 'taller@libertador.com', 'Soacha Centro'),
('Abrasivos y Metales Ltda', '3112223344', 'ventas@abrasivosmet.com', 'Av. Boyacá'),
('Constructora Bolívar', '3187778899', 'compras@cbolivar.com', 'Bogotá D.C.'),
('Industrias Mecánicas Col', '3145556677', 'info@imcol.com', 'Soacha Compartir'),
('Ferretería El Tornillo', '3163332211', 'tornillo@ferreteria.com', 'Bosa Centro'),
('Estructuras Metálicas JS', '3124445566', 'js_estructuras@gmail.com', 'Calle Sur'),
('Proyectos y Diseños 3D', '3198889900', 'proyectos@diseno.com', 'Kennedy'),
('Servicios Industriales Total', '3139991122', 'total@servicios.com', 'Fontibón');

-- 5. Insertar 15 Pedidos
INSERT INTO pedidos (cliente_id, usuario_id, total) VALUES 
(1, 1, 45000.00), (2, 2, 120000.00), (3, 1, 15000.00), 
(4, 1, 75000.00), (5, 2, 250000.00), (6, 1, 8500.00), 
(7, 2, 35000.00), (8, 1, 42000.00), (9, 2, 95000.00), 
(10, 1, 16000.00), (1, 2, 30000.00), (2, 1, 22000.00), 
(3, 2, 42000.00), (4, 1, 28000.00), (5, 2, 52000.00);

-- 6. Insertar Detalle de Pedidos
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES 
(1, 1, 10, 4500.00), (2, 2, 10, 12000.00), (3, 3, 10, 1500.00),
(4, 7, 2, 35000.00), (5, 15, 2, 95000.00), (6, 6, 1, 8500.00),
(7, 1, 5, 4500.00), (8, 11, 8, 5200.00), (9, 15, 1, 95000.00),
(10, 16, 1, 16000.00), (11, 17, 10, 3000.00), (12, 18, 1, 22000.00),
(13, 19, 1, 42000.00), (14, 20, 1, 28000.00), (15, 11, 10, 5200.00);