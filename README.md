# 📦 TecnoStock - Sistema de Gestión de Inventario

Aplicación web desarrollada para la gestión y control de inventario, permitiendo administrar categorías, productos y movimientos de stock (entradas y salidas) con trazabilidad por usuario y alertas de stock bajo.

Se usó ayuda de IA para el desarrollo del programa, pero no generado en su totalidad con IA.

## Tecnologías Utilizadas
* **Backend:** PHP 8 (PDO con consultas preparadas y transacciones).
* **Base de Datos:** MySQL (motor InnoDB).
* **Frontend:** HTML5, CSS3, JavaScript y Bootstrap 5.

## Funcionalidades Principales
* **Autenticación de Usuarios:** Inicio y cierre de sesión con control de acceso por sesiones PHP.
* **Panel de Control (Dashboard):** Indicadores clave (productos activos, categorías, movimientos) y tabla de alertas automáticas para productos bajo el stock mínimo.
* **Módulo de Categorías:** Creación, listado, edición y desactivación lógica (sin borrado físico).
* **Módulo de Productos:** CRUD completo con buscador por código, nombre o categoría, validación de código único, alertas visuales de stock bajo y desactivación lógica.
* **Módulo de Movimientos:** Registro de entradas y salidas con actualización automática de stock mediante transacciones PDO (`beginTransaction`, `commit`, `rollBack`) y bloqueo de salidas que superen el stock disponible.
* **Validación Doble:** Controles tanto en el cliente (HTML5/JavaScript) como en el servidor (PHP).

## Estructura del Proyecto
```text
tecnostock/
├── config/
│   └── conexion.php        # Conexión PDO a MySQL
├── database/
│   └── tecnostock_db.sql   # Respaldo de la base de datos
├── includes/
│   ├── header.php          # Barra de navegación y control de sesión
│   └── footer.php          # Pie de página y scripts
├── modules/
│   ├── categorias/         # CRUD de categorías
│   ├── productos/          # CRUD de productos y buscador
│   └── movimientos/        # Historial y registro de entradas/salidas
├── index.php               # Panel de control (Dashboard)
├── login.php               # Inicio de sesión
└── logout.php              # Cierre de sesión
