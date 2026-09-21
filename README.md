# ZDtecnoc — Panel de Gestión

> Aplicación web full stack para administrar el inventario y las ventas de una tienda de accesorios y equipos de cómputo.

## Descripción

ZDtecnoc permite gestionar **usuarios, categorías, productos, clientes y pedidos**, además de visualizar indicadores mediante gráficos (Chart.js) y generar reportes en PDF (mPDF). El proyecto fue desarrollado como plan de mejoramiento del programa de Análisis y Desarrollo de Software (ADSI) — SENA, durante 15 días.

---

## Tecnologías utilizadas

| Capa | Tecnología |
|------|-----------|
| Frontend | HTML5 semántico, CSS3 (Flexbox + Grid), JavaScript ES6+ |
| Backend | PHP 8.1+ con PDO |
| Base de datos | MySQL 8 / MariaDB |
| Seguridad | Sesiones PHP seguras, CSRF, password_hash, bloqueo por intentos |
| Gráficos | Chart.js |
| Reportes | mPDF |

---

## Requisitos previos

- PHP 8.1 o superior (con extensiones `pdo_mysql`, `mbstring`, `session`)
- MySQL 8 o MariaDB 10.6+
- Servidor web local: XAMPP, Laragon o WAMP
- Composer (opcional, solo si se agrega mPDF vía Composer)

---

## Instalación

### 1. Clonar o copiar el proyecto

```bash
git clone <url-del-repositorio>
# O copiarlo directamente a la carpeta htdocs / www de tu servidor local
```

### 2. Crear la base de datos

Importar los archivos SQL en este orden en phpMyAdmin o la terminal MySQL:

```sql
-- 1. Estructura principal
SOURCE sgl/estructura.sql;

-- 2. Vistas SQL para gráficos
SOURCE sgl/vistas.sql;

-- 3. Datos de ejemplo (categorías y productos)
SOURCE sgl/datos.sql;

-- 4. Migraciones adicionales (si corresponde)
SOURCE sgl/migracion_dia10.sql;
SOURCE sgl/migracion_dia13.sql;
```

### 3. Configurar las credenciales de base de datos

```bash
# Copiar el archivo de ejemplo y editarlo con tus credenciales reales
cp sgl/credenciales.example.php sgl/credenciales.php
```

Edita `sgl/credenciales.php` con tus datos:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'zdtecnoc_db');
define('DB_USER', 'root');          // Tu usuario MySQL
define('DB_PASS', '');              // Tu contraseña MySQL
```

> **Nota:** `sgl/credenciales.php` está en `.gitignore` y **nunca** debe subirse al repositorio.

### 4. Crear los usuarios de prueba

Accede desde el navegador **solo en entorno local**:

```
http://localhost/mejoramiento/sgl/registro.php
```

Esto crea los tres usuarios con contraseñas hasheadas en la base de datos.

### 5. Acceder al sistema

```
http://localhost/mejoramiento/login.php
```

---

## Credenciales de prueba

| Rol | Correo | Contraseña |
|-----|--------|-----------|
| Administrador | admin@zd.com | Admin123* |
| Vendedor | vendedor@zd.com | Vendedor123* |
| Consultor | consultor@zd.com | Consultor123* |

> **Importante:** Estas credenciales son solo para desarrollo. Cámbialas antes de un despliegue real.

---

## Estructura de carpetas

```
mejoramiento/
├── app/
│   ├── config/
│   │   └── config.php          # Constantes globales (BASE_URL, tiempos de sesión)
│   ├── modelos/
│   │   ├── CategoriaModelo.php # CRUD de categorías
│   │   ├── ClienteModelo.php   # CRUD de clientes
│   │   ├── ProductoModelo.php  # CRUD de productos
│   │   └── UsuarioModelo.php   # CRUD de usuarios
│   └── vistas/
│       └── parciales/
│           ├── cabecera.php    # <head> y apertura de <body>
│           ├── menu.php        # Menú lateral con roles
│           └── pie.php         # Cierre de página + scripts
├── assets/
│   └── img/                    # Imágenes y evidencias
├── bitacora/                   # Evidencias por día
├── css/
│   ├── estilos.css             # Estilos principales
│   ├── reporte.css             # Estilos para reportes PDF
│   └── tokens.css              # Variables CSS (colores, tipografía)
├── js/
│   ├── datos-prueba.js         # Arreglo estático para ejercicios JS
│   ├── ejercicios.js           # Ejercicios días 7 y 8
│   └── graficos.js             # Chart.js — gráficos del dashboard
├── sgl/                        # Capa de seguridad y base de datos
│   ├── conexion.php            # PDO — conexión a MySQL
│   ├── credenciales.example.php# Plantilla de credenciales (sin datos reales)
│   ├── csrf.php                # Tokens CSRF
│   ├── datos.sql               # Datos de ejemplo
│   ├── estructura.sql          # DDL — creación de tablas
│   ├── guardia.php             # Control de sesión y roles
│   ├── login.php               # Lógica de autenticación
│   ├── migracion_dia10.sql     # Migraciones día 10
│   ├── migracion_dia13.sql     # Migraciones día 13
│   ├── registro.php            # Crea usuarios de prueba (solo localhost)
│   ├── salir.php               # Cierre de sesión
│   ├── sesion.php              # Configuración de sesiones seguras
│   └── vistas.sql              # Vistas SQL para reportes y gráficos
├── api/
│   └── graficos.php            # Endpoint JSON para Chart.js
├── categorias.php              # CRUD de categorías
├── clientes.php                # CRUD de clientes
├── dashboard.php               # Panel principal con gráficos
├── login.php                   # Formulario de inicio de sesión
├── pedidos.php                 # Registro de pedidos
├── productos.php               # CRUD de productos
├── reportes.php                # Generación de reportes PDF
├── usuarios.php                # CRUD de usuarios (solo administrador)
└── README.md
```

---

## Estado del proyecto

- [x] Día 1: Fundamentos de color, estructura del repositorio
- [x] Día 2: Definición de marca, logo y paleta de colores
- [x] Día 3: HTML5 semántico
- [x] Día 4: CSS y modelo de caja
- [x] Día 5: Maquetación con Flexbox y Grid
- [x] Día 6: Diseño responsive (360px, 768px, 1440px)
- [x] Día 7: JavaScript desde cero — arreglos y funciones
- [x] Día 8: DOM, eventos y validación de formularios
- [x] Día 9: PHP y conexión a base de datos con PDO
- [x] Día 10: Inicio de sesión seguro con bloqueo por intentos
- [x] Día 11: Sesiones y control de acceso por roles
- [x] Día 12: Dashboard con menú lateral responsive
- [x] Día 13: CRUD completo (productos, clientes, pedidos, categorías, usuarios)
- [x] Día 14: Vistas SQL y gráficos con Chart.js
- [x] Día 15: Reportes PDF y sustentación final
