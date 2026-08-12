/**
 * Charts + export de reportes.
 * Espera REPORT_DATA = { ventas, productos } desde PHP.
 */

document.addEventListener('DOMContentLoaded', () => {
    const data = window.REPORT_DATA || { ventas: [], productos: [] };
    const ventasData = data.ventas || [];
    const productosData = data.productos || [];

    const ventasCanvas = document.getElementById('ventasChart');
    const productosCanvas = document.getElementById('productosChart');

    if (ventasCanvas && typeof Chart !== 'undefined') {
        new Chart(ventasCanvas, {
            type: 'line',
            data: {
                labels: ventasData.map((v) => new Date(v.fecha_venta).toLocaleDateString()),
                datasets: [{
                    label: 'Ventas por Día',
                    data: ventasData.map((v) => v.total),
                    borderColor: '#0d6efd',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Ventas Diarias' }
                }
            }
        });
    }

    if (productosCanvas && typeof Chart !== 'undefined') {
        new Chart(productosCanvas, {
            type: 'bar',
            data: {
                labels: productosData.map((p) => p.nombre),
                datasets: [{
                    label: 'Cantidad Vendida',
                    data: productosData.map((p) => p.cantidad_vendida),
                    backgroundColor: '#0dcaf0'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Productos Más Vendidos' }
                }
            }
        });
    }

    window.exportToExcel = function exportToExcel() {
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
        const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
        window.location.href = `api/export_report.php?tipo=excel&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    };

    window.exportToPDF = function exportToPDF() {
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
        const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
        window.location.href = `api/export_report.php?tipo=pdf&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    };
});
