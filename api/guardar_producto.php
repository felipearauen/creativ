<?php
/**
 * Alta rápida de producto vía JSON (legacy / integraciones).
 */

require_once __DIR__ . '/../includes/bootstrap.php';

checkLogin();
checkRole(['admin', 'inventario']);
header('Content-Type: application/json; charset=utf-8');

try {
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $estado = $_POST['estado'] ?? 'bueno';
    $stock = $_POST['stock'] ?? 0;
    $precioCompra = $_POST['precio_compra'] ?? 0;
    $precioVenta = $_POST['precio_venta'] ?? 0;

    $stmt = $pdo->prepare('SELECT id FROM productos WHERE codigo = ?');
    $stmt->execute([$codigo]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'El código ya existe']);
        exit;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO productos (codigo, nombre, categoria, estado, stock, precio_compra, precio_venta)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$codigo, $nombre, $categoria, $estado, $stock, $precioCompra, $precioVenta]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el producto: ' . $e->getMessage()]);
}
