<?php
/**
 * Procesa una venta enviada como JSON (endpoint alternativo).
 */

require_once __DIR__ . '/../includes/bootstrap.php';

checkLogin();
checkRole(['admin', 'cajero']);
header('Content-Type: application/json; charset=utf-8');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || empty($data['items'])) {
        throw new Exception('No hay productos en el carrito');
    }

    $cajaQuery = $pdo->prepare(
        "SELECT id FROM cajas WHERE usuario_id = ? AND fecha = CURRENT_DATE() AND estado = 'abierta'"
    );
    $cajaQuery->execute([$_SESSION['user_id']]);
    $caja = $cajaQuery->fetch();

    if (!$caja) {
        throw new Exception('No hay caja abierta');
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "INSERT INTO ventas (usuario_id, caja_id, total, metodo_pago) VALUES (?, ?, ?, 'efectivo')"
    );
    $stmt->execute([$_SESSION['user_id'], $caja['id'], $data['total']]);
    $ventaId = $pdo->lastInsertId();

    foreach ($data['items'] as $item) {
        $stmt = $pdo->prepare('SELECT stock FROM productos WHERE id = ? FOR UPDATE');
        $stmt->execute([$item['id']]);
        $producto = $stmt->fetch();

        if (!$producto || $producto['stock'] < $item['quantity']) {
            throw new Exception('Stock insuficiente para el producto: ' . ($item['nombre'] ?? $item['id']));
        }

        $precioUnitario = $item['precio_venta'] ?? $item['valor_venta'] ?? 0;
        $subtotal = $item['quantity'] * $precioUnitario;

        $stmt = $pdo->prepare(
            'INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$ventaId, $item['id'], $item['quantity'], $precioUnitario, $subtotal]);

        $stmt = $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');
        $stmt->execute([$item['quantity'], $item['id']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'venta_id' => $ventaId]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
