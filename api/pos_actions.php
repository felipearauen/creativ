<?php
/**
 * Endpoints AJAX del POS: buscar producto y cerrar venta.
 */

require_once __DIR__ . '/../includes/bootstrap.php';

checkLogin();
checkRole(['cajero', 'admin', 'usuario']);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    echo json_encode(['error' => 'Solicitud inválida']);
    exit;
}

$cajaQuery = $pdo->prepare(
    "SELECT * FROM cajas WHERE usuario_id = ? AND fecha = CURRENT_DATE() AND estado = 'abierta'"
);
$cajaQuery->execute([$_SESSION['user_id']]);
$cajaActual = $cajaQuery->fetch();

switch ($_POST['action']) {
    case 'buscar_producto':
        $codigo = trim($_POST['codigo'] ?? '');

        try {
            $stmt = $pdo->prepare(
                'SELECT * FROM productos WHERE codigo = ? OR codigo_barras = ? OR nombre LIKE ? LIMIT 1'
            );
            $stmt->execute([$codigo, $codigo, "%$codigo%"]);
            $producto = $stmt->fetch();
            echo json_encode($producto ?: null);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'procesar_venta':
        if (!$cajaActual) {
            echo json_encode(['error' => 'No hay caja abierta']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO ventas (usuario_id, caja_id, total, metodo_pago, fecha_venta)
                 VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)'
            );
            $stmt->execute([
                $_SESSION['user_id'],
                $cajaActual['id'],
                $_POST['total'],
                $_POST['metodo_pago']
            ]);
            $ventaId = $pdo->lastInsertId();

            $productos = json_decode($_POST['productos'], true);
            if (!is_array($productos) || count($productos) === 0) {
                throw new RuntimeException('El carrito está vacío');
            }

            foreach ($productos as $producto) {
                $stmt = $pdo->prepare(
                    'INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
                     VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $ventaId,
                    $producto['id'],
                    $producto['cantidad'],
                    $producto['precio'],
                    $producto['cantidad'] * $producto['precio']
                ]);

                $stmt = $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');
                $stmt->execute([$producto['cantidad'], $producto['id']]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'venta_id' => $ventaId]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Acción no soportada']);
}
