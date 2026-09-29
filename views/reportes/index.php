<div class="container">
    <div class="report-container animate-fade-in">
        <form class="mb-4" method="GET">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?php echo e($fechaInicio); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?php echo e($fechaFin); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <h3><?php echo count($ventas); ?></h3>
                <p>Total Ventas</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <h3>$<?php echo formatMoney(array_sum(array_column($ventas, 'total'))); ?></h3>
                <p>Ingresos Totales</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <h3><?php echo array_sum(array_column($ventas, 'total_items')); ?></h3>
                <p>Productos Vendidos</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="productosChart"></canvas>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <h3>Productos Más Vendidos</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Código</th>
                        <th>Cantidad Vendida</th>
                        <th>Total Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productosMasVendidos as $producto): ?>
                        <tr>
                            <td><?php echo e($producto['nombre']); ?></td>
                            <td><?php echo e($producto['codigo']); ?></td>
                            <td><?php echo e($producto['cantidad_vendida']); ?></td>
                            <td>$<?php echo formatMoney($producto['total_ventas']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <button class="btn btn-success me-2" type="button" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-2"></i>Exportar a Excel
            </button>
            <button class="btn btn-danger" type="button" onclick="exportToPDF()">
                <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
            </button>
        </div>
    </div>
</div>
