<?php
/**
 * Lookup por código de barras.
 */

require_once __DIR__ . '/../includes/bootstrap.php';

checkLogin();
header('Content-Type: application/json; charset=utf-8');

try {
    $barcode = $_GET['barcode'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM productos WHERE codigo_barras = ? AND stock > 0');
    $stmt->execute([$barcode]);
    $product = $stmt->fetch();

    echo json_encode($product ?: null);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
