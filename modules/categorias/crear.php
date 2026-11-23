<?php
require_once '../../includes/header.php';
require_once '../../config/conexion.php';

$error = "";
$exito = "";

// 1. Verificar si el usuario envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir y limpiar los datos
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    // 2. Validación en el servidor (PHP)
    if (empty($nombre)) {
        $error = "El nombre de la categoría es obligatorio.";
    } else {
        try {
            // 3. Consulta preparada para insertar datos de forma segura
            $sql = "INSERT INTO CATEGORIA (nombre, descripcion) VALUES (:nombre, :descripcion)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion
            ]);

            $exito = "Categoría registrada correctamente.";
        } catch (PDOException $e) {
            $error = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nueva Categoría</h2>
    <a href="index.php" class="btn btn-secondary">⬅️ Volver al listado</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Mostrar mensajes de éxito o error -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($exito)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <!-- Formulario de ingreso -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Categoría <span class="text-danger">*</span></label>
                <!-- Validación en el cliente: el atributo 'required' de HTML5 -->
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">💾 Guardar Categoría</button>
        </form>

    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>