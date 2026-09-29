<?php
/**
 * Búsqueda de productos con stock para el POS / autocomplete.
 */

require_once __DIR__ . '/../includes/bootstrap.php';

checkLogin();
header('Content-Type: application/json; charset=utf-8');

try {
    $search = $_GET['search'] ?? '';

    $query = 'SELECT * FROM productos
              WHERE (nombre LIKE :search OR codigo LIKE :search OR codigo_barras LIKE :search)
                AND stock > 0
              ORDER BY nombre ASC
              LIMIT 50';

    $stmt = $pdo->prepare($query);
    $stmt->execute(['search' => "%$search%"]);

    echo json_encode($stmt->fetchAll());
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
