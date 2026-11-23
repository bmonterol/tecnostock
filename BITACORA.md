#  Sistema web TecnoStock 

## Martes 27/10/2026 - 20:17 Hrs - Inicio

**Objetivo: Comprender y organizar el taller para la creación de un sistema web enlazado a una base de datos relacional para un determinado caso.**
	
    Se lee, resume y organizan los objetivos del taller, reglas de negocio, requisitos y resultados esperados, y se seleccionan las herramientas a usar.


## Martes 27/10/2026 - 23:35 Hrs - Herramientas iniciales

**Objetivo: Dejar herramientas de editor y servidor local para la creación de la base de datos.**

    Se instala XAMPP, se inician los servidores de Apache y MySQL para la creación de la base de datos en donde se registrarán usuarios, productos, etc.


## Miércoles 28/10/2026 - 00:28 Hrs - Creacion de DB y primera tabla

**Objetivo: Crear la DB y comenzar a crear las tablas especificadas con campos y restricciones adecuadas.**

    Se crea la base de datos en el servidor local "Tecnostock_DB" y la primera tabla "Usuario", donde se tienen los campos para ID, nombre, correo, clave y estado con sus correspondientes restricciones como NOT NULL y UNIQUE en el caso de los correos.


## Miércoles 28/10/2026 - 01:42 Hrs - Tablas completadas

**Objetivo: Finalizar las tablas solicitadas y revisarlas teniendo en cuenta el uso de PHP.**

    Se finaliza la creación de tablas y se usa el apoyo de una IA (Gemini Pro) para comprobar la compatibilidad de las tablas con un entorno en PHP.
    Se corrigen dos problemas de compatibilidad: Uso de tilde en una palabra y formato de hora y fecha (cambiada de DATETIME NOT NULL a DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP para el registro automático de la hora y fecha actual).


## Miércoles 28/10/2026 - 02:06 Hrs - Inicialización de tablas

**Objetivo: Verificar que las tablas se generen y almacenen datos correctamente.**

    Se probaron las tablas añadiendo datos y verificando que cada elemento y columna funcionen correctamente, añadiendo datos nulos a propósito para forzar error y ver si efectivamente rechaza datos nulos.
    La revisión de tablas fue un éxito teniendo todas las columnas funcionando e incluso mostrando errores al no ingresar un valor en llaves foráneas (FK).

## Lunes 01/11/2026 - 21:35 Hrs - Conexión de MySQL con PDO

**Objetivo: Con ayuda de Gemini Pro, generar un código que verifique si la base de datos es reconocida por PDO.**

    Se escribió con IA un bloque de código PHP preguntando por sus pasos, comandos y que hace cada sección del bloque con la base de datos.