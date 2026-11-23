<?php 

    session_start();
    
    if(isset($_SESSION['id_usuario'])){
        header("Location: index.php");
        exit;
    }


    require_once 'config/conexion.php';

    $error = "";
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $correo = trim($_POST['correo']);
        $clave = $_POST['clave'];

            if (!empty($correo) && !empty($clave)){
                $sql = "SELECT id_usuario, nombre, clave FROM USUARIO WHERE correo = :correo AND estado = 1 LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':correo' => $correo]);

                $usuario = $stmt->fetch();

                if($usuario && password_verify($clave, $usuario['clave'])){
                    $_SESSION['id_usuario'] = $usuario ['id_usuario'];
                    $_SESSION['nombre']     = $usuario ['nombre'];

                    header("Location: index.php");
                    exit;

                }
                else{
                    $error = "Correo o contraseña incorrectos, o usuario inactivo.";
                }
            }
            else{
                $error = "Por favor, completa todos los campos.";
            }

    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <title>Iniciar Sesión - TecnoStock</title>
</head>

<body>
    <h2>Acceso a TecnoStock</h2>

    <!-- Si existe algun error, lo mostramos aquí -->
    <?php
        if(!empty($error)): ?>
            <p style="color: red;"> <?php echo htmlspecialchars($error) ?> </p> 
    <?php endif; ?>

    <!-- action="" significa que el formulario se envia a este mismo archivo -->
    <form method="POST" action="">
        <label for="correo">Correo electrónico:</label> <br>
        <input type="email" id="correo" name="correo" required> <br> <br>
        
        <label for="clave">Clave:</label> <br>
        <input type="password" id="clave" name="clave" required> <br> <br>

        <button type="sumbit">Ingresar</button>
    </form>
</body>
</html>

