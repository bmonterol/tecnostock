<?php
require_once 'includes/header.php';
require_once 'config/conexion.php';

// 1. Consultas para las tarjetas de resumen (KPIs)
$total_productos = $pdo->query("SELECT COUNT(*) FROM PRODUCTO WHERE estado = 1")->fetchColumn();
$total_categorias = $pdo->query("SELECT COUNT(*) FROM CATEGORIA WHERE estado = 1")->fetchColumn();
$total_movimientos = $pdo->query("SELECT COUNT(*) FROM MOVIMIENTO")->fetchColumn();

// 2. Consultar productos activos con stock bajo el mínimo
$sql_alertas = "SELECT p.id_producto, p.codigo, p.nombre, p.stock_actual, p.stock_minimo, c.nombre AS categoria
                FROM PRODUCTO p
                INNER JOIN CATEGORIA c ON p.id_categoria = c.id_categoria
                WHERE p.estado = 1 AND p.stock_actual < p.stock_minimo
                ORDER BY p.stock_actual ASC";
$alertas_stock = $pdo->query($sql_alertas)->fetchAll();
$total_alertas = count($alertas_stock);
?>

<div class="mb-4">
    <h2 class="fw-bold">Panel de Control</h2>
    <p class="text-muted">Resumen general del inventario de TecnoStock.</p>
</div>

<!-- Tarjetas de indicadores -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase">Productos Activos</h6>
                <p class="display-6 fw-bold mb-0"><?php echo $total_productos; ?></p>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/productos/index.php" class="text-white text-decoration-none small">Ver catálogo →</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-secondary shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase">Categorías Activas</h6>
                <p class="display-6 fw-bold mb-0"><?php echo $total_categorias; ?></p>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/categorias/index.php" class="text-white text-decoration-none small">Ver categorías →</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase">Movimientos</h6>
                <p class="display-6 fw-bold mb-0"><?php echo $total_movimientos; ?></p>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/movimientos/index.php" class="text-white text-decoration-none small">Ver historial →</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white <?php echo ($total_alertas > 0) ? 'bg-danger' : 'bg-success'; ?> shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase">Alertas Bajo Stock</h6>
                <p class="display-6 fw-bold mb-0"><?php echo $total_alertas; ?></p>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/movimientos/crear.php" class="text-white text-decoration-none small">Registrar entrada →</a>
            </div>
        </div>
    </div>
</div>

<!-- Sección de alertas de stock bajo -->
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-danger">⚠️ Productos con Stock Bajo el Mínimo</h5>
        <a href="modules/movimientos/crear.php" class="btn btn-sm btn-outline-primary">➕ Reponer Stock</a>
    </div>
    <div class="card-body">
        <?php if ($total_alertas > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock Actual</th>
                            <th class="text-center">Stock Mínimo</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alertas_stock as $alerta): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($alerta['codigo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($alerta['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($alerta['categoria']); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-danger fs-6"><?php echo $alerta['stock_actual']; ?></span>
                                </td>
                                <td class="text-center"><?php echo $alerta['stock_minimo']; ?></td>
                                <td class="text-center">
                                    <a href="modules/movimientos/crear.php" class="btn btn-success btn-sm">⬆️ Entrada</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-success mb-0">
                ✅ Todo en orden: no hay productos con stock por debajo del mínimo definido.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
