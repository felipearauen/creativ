/**
 * CRUD de productos (modal crear/editar + baja).
 */

document.addEventListener('DOMContentLoaded', () => {
    const productoModalEl = document.getElementById('productoModal');
    const productoForm = document.getElementById('productoForm');

    if (!productoModalEl || !productoForm) {
        return;
    }

    const productoModal = new bootstrap.Modal(productoModalEl);

    window.editarProducto = function editarProducto(producto) {
        productoForm.action.value = 'editar';
        productoForm.id.value = producto.id;
        productoForm.codigo.value = producto.codigo;
        productoForm.codigo_barras.value = producto.codigo_barras || '';
        productoForm.nombre.value = producto.nombre;
        productoForm.categoria.value = producto.categoria;
        productoForm.estado.value = producto.estado;
        productoForm.stock.value = producto.stock;
        productoForm.stock_minimo.value = producto.stock_minimo;
        productoForm.precio_compra.value = producto.precio_compra;
        productoForm.precio_venta.value = producto.precio_venta;
        productoModal.show();
    };

    window.eliminarProducto = function eliminarProducto(id) {
        if (!confirm('¿Está seguro de que desea eliminar este producto?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="eliminar">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    };

    productoModalEl.addEventListener('hidden.bs.modal', () => {
        productoForm.reset();
        productoForm.action.value = 'crear';
        productoForm.id.value = '';
    });

    // Evita vender por debajo del costo
    productoForm.addEventListener('submit', (e) => {
        const valorCompra = parseFloat(productoForm.precio_compra.value);
        const valorVenta = parseFloat(productoForm.precio_venta.value);

        if (valorVenta <= valorCompra) {
            e.preventDefault();
            alert('El valor de venta debe ser mayor al valor de compra');
        }
    });
});
