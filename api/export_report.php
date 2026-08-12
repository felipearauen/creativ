<?php
/**
 * Exporta reportes a Excel (HTML/xls) o PDF (FPDF si está instalado).
 */

require_once __DIR__ . '/../includes/bootstrap.php';

ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');

checkLogin();
checkRole(['admin']);

$fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
$tipo = $_GET['tipo'] ?? 'excel';

function getReportData(PDO $pdo, string $fechaInicio, string $fechaFin): array
{
    $ventasQuery = $pdo->prepare(
        "SELECT
            DATE(v.fecha_venta) AS fecha,
            COUNT(v.id) AS total_ventas,
            SUM(v.total) AS total_ingresos,
            SUM(CASE WHEN v.metodo_pago = 'efectivo' THEN v.total ELSE 0 END) AS total_efectivo,
            SUM(CASE WHEN v.metodo_pago = 'tarjeta' THEN v.total ELSE 0 END) AS total_tarjeta
         FROM ventas v
         WHERE DATE(v.fecha_venta) BETWEEN ? AND ?
         GROUP BY DATE(v.fecha_venta)
         ORDER BY fecha"
    );
    $ventasQuery->execute([$fechaInicio, $fechaFin]);
    $ventas = $ventasQuery->fetchAll();

    $productosQuery = $pdo->prepare(
        'SELECT
            p.codigo,
            p.nombre,
            p.categoria,
            SUM(dv.cantidad) AS cantidad_vendida,
            AVG(dv.precio_unitario) AS precio_promedio,
            SUM(dv.subtotal) AS total_ventas
         FROM productos p
         JOIN detalle_ventas dv ON p.id = dv.producto_id
         JOIN ventas v ON dv.venta_id = v.id
         WHERE DATE(v.fecha_venta) BETWEEN ? AND ?
         GROUP BY p.id
         ORDER BY cantidad_vendida DESC
         LIMIT 10'
    );
    $productosQuery->execute([$fechaInicio, $fechaFin]);
    $productos = $productosQuery->fetchAll();

    return [
        'ventas' => $ventas,
        'productos' => $productos,
    ];
}

$data = getReportData($pdo, $fechaInicio, $fechaFin);

if ($tipo === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte_Ventas_' . $fechaInicio . '_' . $fechaFin . '.xls"');
    header('Cache-Control: max-age=0');
    require __DIR__ . '/../views/reportes/export_excel.php';
    exit;
}

// PDF
$fpdfPath = __DIR__ . '/../vendor/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    // fallback viejo por si lo tenés en la raíz del proyecto
    $fpdfPath = __DIR__ . '/../fpdf186/fpdf.php';
}

if (!file_exists($fpdfPath)) {
    http_response_code(500);
    echo 'Falta la librería FPDF. Colocala en vendor/fpdf/ o fpdf186/.';
    exit;
}

require_once $fpdfPath;

class SalesReportPdf extends FPDF
{
    public string $periodoInicio;
    public string $periodoFin;

    public function Header(): void
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'REPORTE DE VENTAS', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $periodo = 'Periodo: ' . date('d/m/Y', strtotime($this->periodoInicio)) .
            ' - ' . date('d/m/Y', strtotime($this->periodoFin));
        $this->Cell(0, 10, utf8_decode($periodo), 0, 1, 'C');
        $this->Ln(5);
    }

    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Pagina ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, utf8_decode('Fecha de generacion: ') . date('d/m/Y H:i:s'), 0, 0, 'R');
    }
}

$pdf = new SalesReportPdf();
$pdf->periodoInicio = $fechaInicio;
$pdf->periodoFin = $fechaFin;
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'RESUMEN DE VENTAS DIARIAS', 0, 1, 'L');

$pdf->SetFillColor(79, 129, 189);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 7, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Ventas', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Efectivo', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Tarjeta', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Total', 1, 1, 'C', true);

$pdf->SetFillColor(228, 239, 249);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 9);

$totalVentas = 0;
$totalEfectivo = 0;
$totalTarjeta = 0;
$totalIngresos = 0;

foreach ($data['ventas'] as $venta) {
    $pdf->Cell(30, 6, date('d/m/Y', strtotime($venta['fecha'])), 1, 0, 'C');
    $pdf->Cell(25, 6, $venta['total_ventas'], 1, 0, 'C');
    $pdf->Cell(35, 6, '$' . number_format($venta['total_efectivo'], 2), 1, 0, 'R');
    $pdf->Cell(35, 6, '$' . number_format($venta['total_tarjeta'], 2), 1, 0, 'R');
    $pdf->Cell(35, 6, '$' . number_format($venta['total_ingresos'], 2), 1, 1, 'R');

    $totalVentas += $venta['total_ventas'];
    $totalEfectivo += $venta['total_efectivo'];
    $totalTarjeta += $venta['total_tarjeta'];
    $totalIngresos += $venta['total_ingresos'];
}

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 7, 'TOTALES', 1, 0, 'C', true);
$pdf->Cell(25, 7, $totalVentas, 1, 0, 'C', true);
$pdf->Cell(35, 7, '$' . number_format($totalEfectivo, 2), 1, 0, 'R', true);
$pdf->Cell(35, 7, '$' . number_format($totalTarjeta, 2), 1, 0, 'R', true);
$pdf->Cell(35, 7, '$' . number_format($totalIngresos, 2), 1, 1, 'R', true);

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('PRODUCTOS MAS VENDIDOS'), 0, 1, 'L');

$pdf->SetFillColor(79, 129, 189);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 7, utf8_decode('Codigo'), 1, 0, 'C', true);
$pdf->Cell(60, 7, 'Producto', 1, 0, 'C', true);
$pdf->Cell(30, 7, utf8_decode('Categoria'), 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Precio', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Total', 1, 1, 'C', true);

$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 9);

foreach ($data['productos'] as $producto) {
    $pdf->Cell(20, 6, $producto['codigo'], 1, 0, 'C');
    $pdf->Cell(60, 6, utf8_decode($producto['nombre']), 1, 0, 'L');
    $pdf->Cell(30, 6, utf8_decode($producto['categoria']), 1, 0, 'C');
    $pdf->Cell(25, 6, $producto['cantidad_vendida'], 1, 0, 'C');
    $pdf->Cell(25, 6, '$' . number_format($producto['precio_promedio'], 2), 1, 0, 'R');
    $pdf->Cell(30, 6, '$' . number_format($producto['total_ventas'], 2), 1, 1, 'R');
}

$pdf->Output('D', 'Reporte_Ventas_' . $fechaInicio . '_' . $fechaFin . '.pdf');
