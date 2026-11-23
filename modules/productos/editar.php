<?php
require_once '../../includes/header.php';
require_once '../../config/conexion.php';

$error = "";
$exito = "";

// 1. Verificar que llegue el ID del producto por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_producto = $_GET['id'];

// 2. Buscar los datos actuales del producto
$sql = "SELECT * FROM PRODUCTO WHERE id_producto = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_producto]);
$producto = $stmt->fetch();

if (!$producto) {
    echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
    require_once '../../includes/footer.php';
    exit;
}

// 3. Cargar categorías activas (y la categoría actual del producto por si fue desactivada)
$sql_cat = "SELECT id_categoria, nombre FROM CATEGORIA WHERE estado = 1 OR id_categoria = :id_cat ORDER BY nombre ASC";
$stmt_cat = $pdo->prepare($sql_cat);
$stmt_cat->execute([':id_cat' => $producto['id_categoria']]);
$categorias = $stmt_cat->fetchAll();

// 4. Procesar la actualización cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo       = trim($_POST['codigo']);
    $nombre       = trim($_POST['nombre']);
    $id_categoria = $_POST['id_categoria'];
    $descripcion  = trim($_POST['descripcion']);
    $precio       = $_POST['precio'];
    $stock_actual = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];
    $estado       = $_POST['estado'];

    // Validación en el servidor (PHP)
    if (empty($codigo) || empty($nombre) || empty($id_categoria)) {
        $error = "El código, nombre y categoría son obligatorios.";
    } elseif (!is_numeric($precio) || $precio < 0 || !is_numeric($stock_actual) || $stock_actual < 0 || !is_numeric($stock_minimo) || $stock_minimo < 0) {
        $error = "El precio, el stock actual y el stock mínimo no pueden ser negativos.";
    } else {
        try {
            $sql_update = "UPDATE PRODUCTO 
                           SET codigo = :codigo, 
                               nombre = :nombre, 
                               descripcion = :descripcion, 
                               precio = :precio, 
                               stock_actual = :stock_actual, 
                               stock_minimo = :stock_minimo, 
                               id_categoria = :id_categoria, 
                               estado = :estado 
                           WHERE id_producto = :id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                ':codigo'       => $codigo,
                ':nombre'       => $nombre,
                ':descripcion'  => $descripcion,
                ':precio'       => $precio,
                ':stock_actual' => $stock_actual,
                ':stock_minimo' => $stock_minimo,
                ':id_categoria' => $id_categoria,
                ':estado'       => $estado,
                ':id'           => $id_producto
            ]);

            $exito = "Producto actualizado correctamente.";

            // Actualizar el arreglo local para reflejar los cambios en el formulario
            $producto['codigo']       = $codigo;
            $producto['nombre']       = $nombre;
            $producto['descripcion']  = $descripcion;
            $producto['precio']       = $precio;
            $producto['stock_actual'] = $stock_actual;
            $producto['stock_minimo'] = $stock_minimo;
            $producto['id_categoria'] = $id_categoria;
            $producto['estado']       = $estado;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Error: Ya existe otro producto con el código '$codigo'.";
            } else {
                $error = "Error al actualizar el producto: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Producto</h2>
    <a href="index.php" class="btn btn-secondary">⬅️ Volver al catálogo</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <form id="formEditarProducto" method="POST" action="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>" required>
                </div>

                <div class="col-md-8 mb-3">
                    <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>" <?php echo ($cat['id_categoria'] == $producto['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="1" <?php echo ($producto['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo ($producto['estado'] == 0) ? 'selected' : ''; ?>>Inactivo (Desactivado)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="precio" class="form-label">Precio ($) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="precio" name="precio" min="0" step="1" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="stock_actual" class="form-label">Stock Actual <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock_actual" name="stock_actual" min="0" value="<?php echo htmlspecialchars($producto['stock_actual']); ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="stock_minimo" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" value="<?php echo htmlspecialchars($producto['stock_minimo']); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-warning">💾 Guardar Cambios</button>
        </form>

    </div>
</div>

<!-- Validación en el cliente con JavaScript -->
<script>
document.getElementById('formEditarProducto').addEventListener('submit', function(event) {
    const precio = parseFloat(document.getElementById('precio').value);
    const stockActual = parseInt(document.getElementById('stock_actual').value);
    const stockMinimo = parseInt(document.getElementById('stock_minimo').value);

    if (precio < 0 || stockActual < 0 || stockMinimo < 0) {
        event.preventDefault();
        alert('El precio, el stock actual y el stock mínimo no pueden ser números negativos.');
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>