<?php
require_once '../../includes/header.php';
require_once '../../config/conexion.php';

$error = "";
$exito = "";

// 1. Validar que recibimos un ID por la URL (ej: editar.php?id=1)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Si alguien entra directo a editar.php sin ID, lo devolvemos al listado
    header("Location: index.php");
    exit;
}

$id_categoria = $_GET['id'];

// 2. Buscar los datos actuales de la categoría para rellenar el formulario
$sql = "SELECT id_categoria, nombre, descripcion, estado FROM CATEGORIA WHERE id_categoria = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_categoria]);
$categoria = $stmt->fetch();

// Si ponemos un ID que no existe en la URL (ej: id=999)
if (!$categoria) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Categoría no encontrada.</div></div>";
    require_once '../../includes/footer.php';
    exit;
}

// 3. Procesar los datos si el usuario presionó el botón de "Guardar Cambios" (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $estado = $_POST['estado']; // Viene de un <select>

    if (empty($nombre)) {
        $error = "El nombre de la categoría es obligatorio.";
    } else {
        try {
            // Consulta de actualización (UPDATE)
            $sql_update = "UPDATE CATEGORIA SET nombre = :nombre, descripcion = :descripcion, estado = :estado WHERE id_categoria = :id";
            $stmt_update = $pdo->prepare($sql_update);
            
            $stmt_update->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':estado' => $estado,
                ':id' => $id_categoria
            ]);

            $exito = "Categoría actualizada correctamente.";
            
            // Actualizamos el arreglo local para que los inputs muestren los nuevos valores ingresados
            $categoria['nombre'] = $nombre;
            $categoria['descripcion'] = $descripcion;
            $categoria['estado'] = $estado;

        } catch (PDOException $e) {
            $error = "Error al actualizar en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Categoría</h2>
    <a href="index.php" class="btn btn-secondary">⬅️ Volver al listado</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($exito)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Categoría <span class="text-danger">*</span></label>
                <!-- El atributo value="..." rellena el campo con el dato de la base de datos -->
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($categoria['nombre']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($categoria['descripcion']); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="1" <?php echo ($categoria['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo ($categoria['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                </select>
                <div class="form-text">Si desactiva la categoría, se ocultará en nuevos ingresos, pero no se borrará la información histórica.</div>
            </div>
            
            <button type="submit" class="btn btn-warning">💾 Guardar Cambios</button>
        </form>

    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>