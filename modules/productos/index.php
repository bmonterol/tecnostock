<?php
require_once '../../includes/header.php';
require_once '../../config/conexion.php';

// 1. Lógica de búsqueda
$busqueda = "";
$parametros = [];

// Consulta base uniendo PRODUCTO y CATEGORIA
$sql = "SELECT p.id_producto, p.codigo, p.nombre, p.precio, p.stock_actual, p.stock_minimo, p.estado, c.nombre as categoria_nombre 
        FROM PRODUCTO p 
        INNER JOIN CATEGORIA c ON p.id_categoria = c.id_categoria";

// Si el usuario escribió algo en el buscador
if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
    $busqueda = trim($_GET['buscar']);
    // Filtramos por nombre del producto, código o nombre de la categoría
    $sql .= " WHERE p.nombre LIKE :busqueda OR p.codigo LIKE :busqueda OR c.nombre LIKE :busqueda";
    $parametros[':busqueda'] = "%" . $busqueda . "%"; // Los % son comodines de SQL para buscar coincidencias parciales
}

$sql .= " ORDER BY p.id_producto DESC";

// 2. Ejecutar la consulta
$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$productos = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Catálogo de Productos</h2>
    <a href="crear.php" class="btn btn-success">➕ Nuevo Producto</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <!-- Formulario de Búsqueda -->
        <form method="GET" action="" class="row g-3">
            <div class="col-md-8">
                <input type="text" class="form-control" name="buscar" placeholder="Buscar por código, nombre o categoría..." value="<?php echo htmlspecialchars($busqueda); ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                <a href="index.php" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock Actual</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($productos) > 0): ?>
                        <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($prod['codigo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($prod['categoria_nombre']); ?></td>
                                <td>$<?php echo number_format($prod['precio'], 0, ',', '.'); ?></td>
                                
                                <!-- Indicador visual de Stock (Requisito de la rúbrica) -->
                                <td class="text-center">
                                    <?php if ($prod['stock_actual'] <= $prod['stock_minimo']): ?>
                                        <span class="badge bg-danger fs-6" title="Stock bajo el mínimo">
                                            ⚠️ <?php echo htmlspecialchars($prod['stock_actual']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success fs-6">
                                            <?php echo htmlspecialchars($prod['stock_actual']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if ($prod['estado'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="editar.php?id=<?php echo $prod['id_producto']; ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron productos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>