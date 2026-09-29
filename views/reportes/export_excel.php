<html>
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #4F81BD; color: white; }
        .header { font-size: 16px; font-weight: bold; margin-bottom: 20px; }
        .subheader { font-size: 14px; margin-bottom: 10px; }
        .section { margin-top: 20px; }
        .total-row { font-weight: bold; background-color: #E4EFF9; }
    </style>
</head>
<body>
    <div class="header">REPORTE DE VENTAS</div>
    <div class="subheader">
        Período: <?php echo date('d/m/Y', strtotime($fechaInicio)); ?> - <?php echo date('d/m/Y', strtotime($fechaFin)); ?>
    </div>

    <div class="section">
        <table>
            <tr><th colspan="5">RESUMEN DE VENTAS DIARIAS</th></tr>
            <tr>
                <th>Fecha</th>
                <th>Total Ventas</th>
                <th>Ingresos Efectivo</th>
                <th>Ingresos Tarjeta</th>
                <th>Total Ingresos</th>
            </tr>
            <?php
            $totalVentas = 0;
            $totalEfectivo = 0;
            $totalTarjeta = 0;
            $totalIngresos = 0;

            foreach ($data['ventas'] as $venta):
                $totalVentas += $venta['total_ventas'];
                $totalEfectivo += $venta['total_efectivo'];
                $totalTarjeta += $venta['total_tarjeta'];
                $totalIngresos += $venta['total_ingresos'];
            ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($venta['fecha'])); ?></td>
                <td style="text-align: center;"><?php echo $venta['total_ventas']; ?></td>
                <td style="text-align: right;">$<?php echo number_format($venta['total_efectivo'], 2); ?></td>
                <td style="text-align: right;">$<?php echo number_format($venta['total_tarjeta'], 2); ?></td>
                <td style="text-align: right;">$<?php echo number_format($venta['total_ingresos'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td>TOTALES</td>
                <td style="text-align: center;"><?php echo $totalVentas; ?></td>
                <td style="text-align: right;">$<?php echo number_format($totalEfectivo, 2); ?></td>
                <td style="text-align: right;">$<?php echo number_format($totalTarjeta, 2); ?></td>
                <td style="text-align: right;">$<?php echo number_format($totalIngresos, 2); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr><th colspan="6">PRODUCTOS MÁS VENDIDOS</th></tr>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Cantidad Vendida</th>
                <th>Precio Promedio</th>
                <th>Total Ventas</th>
            </tr>
            <?php foreach ($data['productos'] as $producto): ?>
            <tr>
                <td><?php echo e($producto['codigo']); ?></td>
                <td><?php echo e($producto['nombre']); ?></td>
                <td><?php echo e($producto['categoria']); ?></td>
                <td style="text-align: center;"><?php echo e($producto['cantidad_vendida']); ?></td>
                <td style="text-align: right;">$<?php echo number_format($producto['precio_promedio'], 2); ?></td>
                <td style="text-align: right;">$<?php echo number_format($producto['total_ventas'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
