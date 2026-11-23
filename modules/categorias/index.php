<?php
// 1. Incluir el header (que ya valida la sesión y carga Bootstrap)
// Usamos '../..' para retroceder 2 carpetas y llegar a la raíz
require_once '../../includes/header.php';

// 2. Conectar a la base de datos
require_once '../../config/conexion.php';

// 3. Preparar y ejecutar la consulta SELECT
$sql = "SELECT id_categoria, nombre, descripcion, estado FROM CATEGORIA";
$stmt = $pdo->query($sql);
$categorias = $stmt->fetchAll(); // Extrae todos los registros en un arreglo
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Categorías</h2>
    <!-- Botón para ir al formulario de creación (lo haremos en el siguiente paso) -->
    <a href="crear.php" class="btn btn-success">➕ Nueva Categoría</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categorias) > 0): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cat['id_categoria']); ?></td>
                                <td><?php echo htmlspecialchars($cat['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($cat['descripcion']); ?></td>
                                <td>
                                    <?php if ($cat['estado'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Botones de acción preparados para más adelante -->
                                    <a href="editar.php?id=<?php echo $cat['id_categoria']; ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay categorías registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// 4. Incluir el footer
require_once '../../includes/footer.php';
?>