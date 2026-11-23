<?php                   //Indica que el codigo a ejecutar es lógica PHP y no HTML.

$host       = "localhost";          //Se escribe $ para especificar que es una variable, asigno el nombre del servidor de la base de datos.
$db         = "tecnostock_db";      //Se define el nombre de la DB a usar.
$user       = "root";               //Se define el usuario para credencial de XAMPP
$pass       = "";                   //Se define la contraseña para credencial de XAMPP
$charset    = "utf8mb4";            //Charset refiere a la configuracion de caracteres para poder usar caracteres especiales como ñ o tildes, en este caso, utf8mb4 permite esto.

try{                                //Intento de conexion de MySQL con PDO

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [  //Puente para interacciones con DB en sql
        PDO::ATTR_ERRMODE                   =>  PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE        =>  PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES          =>  false,
        ]);

} 

catch (PDOException $e) {      

    die("Error crítico de conexión a la base de datos: " . $e->getMessage());
}

