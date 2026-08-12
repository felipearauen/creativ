<?php
/**
 * Dashboard de reportes (admin).
 */

require_once __DIR__ . '/includes/bootstrap.php';

checkLogin();
checkRole(['admin']);

function getVentas(PDO $pdo, string $fechaInicio, string $fechaFin): array
{
    $stmt = $pdo->prepare(
        'SELECT
            v.id,
            v.fecha_venta,
            u.nombre AS cajero,
            v.total,
            v.metodo_pago,
            COUNT(dv.id) AS num_productos,
            SUM(dv.cantidad) AS total_items
         FROM ventas v
         JOIN usuarios u ON v.usuario_id = u.id
         JOIN detalle_ventas dv ON v.id = dv.venta_id
         WHERE DATE(v.fecha_venta) BETWEEN ? AND ?
         GROUP BY v.id
         ORDER BY v.fecha_venta DESC'
    );
    $stmt->execute([$fechaInicio, $fechaFin]);
    return $stmt->fetchAll();
}

function getProductosMasVendidos(PDO $pdo, string $fechaInicio, string $fechaFin): array
{
    $stmt = $pdo->prepare(
        'SELECT
            p.nombre,
            p.codigo,
            SUM(dv.cantidad) AS cantidad_vendida,
            SUM(dv.subtotal) AS total_ventas
         FROM productos p
         JOIN detalle_ventas dv ON p.id = dv.producto_id
         JOIN ventas v ON dv.venta_id = v.id
         WHERE DATE(v.fecha_venta) BETWEEN ? AND ?
         GROUP BY p.id
         ORDER BY cantidad_vendida DESC
         LIMIT 10'
    );
    $stmt->execute([$fechaInicio, $fechaFin]);
    return $stmt->fetchAll();
}

$fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

$ventas = getVentas($pdo, $fechaInicio, $fechaFin);
$productosMasVendidos = getProductosMasVendidos($pdo, $fechaInicio, $fechaFin);

$pageTitle = 'Reportes - Sistema de Gestión';
$activeNav = 'reportes';
$navIcon = 'fa-store';
$navBrand = 'Reportes de Ventas';
$extraCss = ['reportes.css'];
$extraJs = ['reportes.js'];
$extraHeadScripts = ['https://cdn.jsdelivr.net/npm/chart.js'];
$inlineData = 'window.REPORT_DATA = ' . json_encode([
    'ventas' => $ventas,
    'productos' => $productosMasVendidos,
], JSON_UNESCAPED_UNICODE) . ';';

require __DIR__ . '/views/layouts/header.php';
require __DIR__ . '/views/reportes/index.php';
require __DIR__ . '/views/layouts/footer.php';