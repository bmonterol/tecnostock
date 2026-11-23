<?php
require_once '../../includes/header.php';
require_once '../../config/conexion.php';

$error = "";
$exito = "";

// 1. Procesar el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id_producto'];
    $tipo        = $_POST['tipo'];
    $cantidad    = $_POST['cantidad'];
    $observacion = trim($_POST['observacion']);
    $id_usuario  = $_SESSION['id_usuario'];

    // 2. Validaciones en el servidor (PHP)
    if (empty($id_producto) || empty($tipo) || $cantidad === '') {
        $error = "Debe seleccionar un producto, un tipo de movimiento y una cantidad.";
    } elseif (!in_array($tipo, ['ENTRADA', 'SALIDA'])) {
        $error = "El tipo de movimiento seleccionado no es válido.";
    } elseif (!is_numeric($cantidad) || $cantidad <= 0 || floor($cantidad) != $cantidad) {
        $error = "La cantidad debe ser un número entero mayor a cero.";
    } else {
        try {
            // Iniciar transacción: todo se ejecuta junto o nada se guarda
            $pdo->beginTransaction();

            // Consultar el stock actual bloqueando la fila (FOR UPDATE) para evitar inconsistencias
            $stmt_prod = $pdo->prepare("SELECT nombre, stock_actual FROM PRODUCTO WHERE id_producto = :id AND estado = 1 FOR UPDATE");
            $stmt_prod->execute([':id' => $id_producto]);
            $producto = $stmt_prod->fetch();

            if (!$producto) {
                throw new Exception("El producto seleccionado no existe o está inactivo.");
            }

            $stock_actual = (int) $producto['stock_actual'];
            $cantidad_int = (int) $cantidad;

            // Calcular el nuevo stock según el tipo de movimiento
            if ($tipo === 'ENTRADA') {
                $nuevo_stock = $stock_actual + $cantidad_int;
            } else {
                // Regla de negocio: una salida no puede dejar el stock bajo cero
                if ($cantidad_int > $stock_actual) {
                    throw new Exception("Stock insuficiente para '{$producto['nombre']}'. Disponible: {$stock_actual}, solicitado: {$cantidad_int}.");
                }
                $nuevo_stock = $stock_actual - $cantidad_int;
            }

            // 3. Insertar el movimiento en el historial
            $sql_mov = "INSERT INTO MOVIMIENTO (fecha_hora, tipo, cantidad, observacion, id_producto, id_usuario)
                        VALUES (NOW(), :tipo, :cantidad, :observacion, :id_producto, :id_usuario)";
            $stmt_mov = $pdo->prepare($sql_mov);
            $stmt_mov->execute([
                ':tipo'        => $tipo,
                ':cantidad'    => $cantidad_int,
                ':observacion' => $observacion,
                ':id_producto' => $id_producto,
                ':id_usuario'  => $id_usuario
            ]);

            // 4. Actualizar el stock_actual del producto automáticamente
            $sql_upd = "UPDATE PRODUCTO SET stock_actual = :nuevo_stock WHERE id_producto = :id_producto";
            $stmt_upd = $pdo->prepare($sql_upd);
            $stmt_upd->execute([
                ':nuevo_stock' => $nuevo_stock,
                ':id_producto' => $id_producto
            ]);

            // Confirmar ambos cambios en la base de datos
            $pdo->commit();
            $exito = "Movimiento registrado correctamente. Nuevo stock de '{$producto['nombre']}': {$nuevo_stock}.";

        } catch (Exception $e) {
            // Si algo falla, deshacer cualquier cambio pendiente
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = $e->getMessage();
        }
    }
}

// 5. Cargar productos activos (se consulta al final para que muestre el stock ya actualizado)
$stmt_productos = $pdo->query("SELECT id_producto, codigo, nombre, stock_actual FROM PRODUCTO WHERE estado = 1 ORDER BY nombre ASC");
$productos = $stmt_productos->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Registrar Movimiento</h2>
    <a href="index.php" class="btn btn-secondary">⬅️ Volver al historial</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <form id="formMovimiento" method="POST" action="">
            <div class="mb-3">
                <label for="id_producto" class="form-label">Producto <span class="text-danger">*</span></label>
                <select class="form-select" id="id_producto" name="id_producto" required>
                    <option value="">-- Seleccione un producto activo --</option>
                    <?php foreach ($productos as $prod): ?>
                        <option value="<?php echo $prod['id_producto']; ?>" data-stock="<?php echo $prod['stock_actual']; ?>">
                            <?php echo htmlspecialchars($prod['codigo'] . ' - ' . $prod['nombre'] . ' (Stock actual: ' . $prod['stock_actual'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipo" class="form-label">Tipo de Movimiento <span class="text-danger">*</span></label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="">-- Seleccione --</option>
                        <option value="ENTRADA">⬆️ Entrada (Sumar stock)</option>
                        <option value="SALIDA">⬇️ Salida (Restar stock)</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="cantidad" class="form-label">Cantidad <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" step="1" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="observacion" class="form-label">Observación (Opcional)</label>
                <textarea class="form-control" id="observacion" name="observacion" rows="2" placeholder="Ej: Compra a proveedor, venta mostrador, ajuste de inventario..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">💾 Registrar Movimiento</button>
        </form>

    </div>
</div>

<!-- Validación inmediata en el cliente con JavaScript -->
<script>
document.getElementById('formMovimiento').addEventListener('submit', function(event) {
    const selectProducto = document.getElementById('id_producto');
    const tipo = document.getElementById('tipo').value;
    const cantidad = parseInt(document.getElementById('cantidad').value);

    if (isNaN(cantidad) || cantidad <= 0) {
        event.preventDefault();
        alert('La cantidad debe ser un número mayor a cero.');
        return;
    }

    if (tipo === 'SALIDA' && selectProducto.selectedIndex > 0) {
        const stockDisponible = parseInt(selectProducto.options[selectProducto.selectedIndex].dataset.stock);
        if (cantidad > stockDisponible) {
            event.preventDefault();
            alert('No puedes registrar una salida mayor al stock disponible (' + stockDisponible + ').');
        }
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>