<?php
require_once '../../includes/header.php';
require_once '../../config/conexion.php';

// Consulta con doble INNER JOIN para traer el nombre del producto y del usuario responsable
$sql = "SELECT m.id_movimiento, m.fecha_hora, m.tipo, m.cantidad, m.observacion,
               p.codigo AS producto_codigo, p.nombre AS producto_nombre,
               u.nombre AS usuario_nombre
        FROM MOVIMIENTO m
        INNER JOIN PRODUCTO p ON m.id_producto = p.id_producto
        INNER JOIN USUARIO u ON m.id_usuario = u.id_usuario
        ORDER BY m.fecha_hora DESC, m.id_movimiento DESC";

$stmt = $pdo->query($sql);
$movimientos = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Historial de Movimientos</h2>
    <a href="crear.php" class="btn btn-success">➕ Registrar Movimiento</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Observación</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($movimientos) > 0): ?>
                        <?php foreach ($movimientos as $mov): ?>
                            <?php $esEntrada = (strtoupper($mov['tipo']) === 'ENTRADA'); ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha_hora'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($mov['producto_codigo']); ?></strong> - 
                                    <?php echo htmlspecialchars($mov['producto_nombre']); ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($esEntrada): ?>
                                        <span class="badge bg-success">⬆️ Entrada</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">⬇️ Salida</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-bold">
                                    <?php echo ($esEntrada ? '+' : '-') . htmlspecialchars($mov['cantidad']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($mov['observacion'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($mov['usuario_nombre']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aún no hay movimientos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>