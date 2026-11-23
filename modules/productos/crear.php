<?php
require_once '../../includes/header.php';
require_once '../../config/conexion.php';

$error = "";
$exito = "";

// 1. Obtener solo las categorías activas para llenar el <select>
$stmt_cat = $pdo->query("SELECT id_categoria, nombre FROM CATEGORIA WHERE estado = 1 ORDER BY nombre ASC");
$categorias = $stmt_cat->fetchAll();

// 2. Procesar el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo       = trim($_POST['codigo']);
    $nombre       = trim($_POST['nombre']);
    $id_categoria = $_POST['id_categoria'];
    $descripcion  = trim($_POST['descripcion']);
    $precio       = $_POST['precio'];
    $stock_actual = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];

    // 3. Validaciones en el servidor (PHP)
    if (empty($codigo) || empty($nombre) || empty($id_categoria)) {
        $error = "El código, nombre y categoría son obligatorios.";
    } elseif (!is_numeric($precio) || $precio < 0 || !is_numeric($stock_actual) || $stock_actual < 0 || !is_numeric($stock_minimo) || $stock_minimo < 0) {
        $error = "El precio, el stock actual y el stock mínimo no pueden ser negativos.";
    } else {
        try {
            $sql = "INSERT INTO PRODUCTO (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria) 
                    VALUES (:codigo, :nombre, :descripcion, :precio, :stock_actual, :stock_minimo, :id_categoria)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':codigo'       => $codigo,
                ':nombre'       => $nombre,
                ':descripcion'  => $descripcion,
                ':precio'       => $precio,
                ':stock_actual' => $stock_actual,
                ':stock_minimo' => $stock_minimo,
                ':id_categoria' => $id_categoria
            ]);

            $exito = "Producto registrado correctamente.";
        } catch (PDOException $e) {
            // El código '23000' indica violación de restricción UNIQUE (código repetido)
            if ($e->getCode() == 23000) {
                $error = "Error: Ya existe un producto registrado con el código '$codigo'.";
            } else {
                $error = "Error al guardar el producto: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nuevo Producto</h2>
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

        <form id="formProducto" method="POST" action="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Ej: TEC-001" required>
                </div>

                <div class="col-md-8 mb-3">
                    <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                <select class="form-select" id="id_categoria" name="id_categoria" required>
                    <option value="">-- Seleccione una categoría --</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>">
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="precio" class="form-label">Precio ($) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="precio" name="precio" min="0" step="1" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="stock_actual" class="form-label">Stock Actual <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock_actual" name="stock_actual" min="0" value="0" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="stock_minimo" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" value="5" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">💾 Guardar Producto</button>
        </form>

    </div>
</div>

<!-- Validación inmediata en el cliente con JavaScript -->
<script>
document.getElementById('formProducto').addEventListener('submit', function(event) {
    const precio = parseFloat(document.getElementById('precio').value);
    const stockActual = parseInt(document.getElementById('stock_actual').value);
    const stockMinimo = parseInt(document.getElementById('stock_minimo').value);

    if (precio < 0 || stockActual < 0 || stockMinimo < 0) {
        event.preventDefault(); // Detiene el envío del formulario
        alert('El precio, el stock actual y el stock mínimo no pueden ser números negativos.');
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>