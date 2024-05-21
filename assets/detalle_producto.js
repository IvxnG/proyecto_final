document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id_producto = urlParams.get('id_producto');

    if (id_producto) {
        obtenerDetalleProducto(id_producto);
    } else {
        console.error('ID del producto no proporcionado en la URL.');
    }
});

function obtenerDetalleProducto(id_producto) {
    fetch('http://localhost/proyecto_final/api/items/getDetails.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id_producto: id_producto })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Ocurrió un error al obtener los detalles del producto.');
        }
        return response.json();
    })
    .then(data => mostrarDetalleProducto(data))
    .catch(error => console.error('Error:', error.message));
}

function mostrarDetalleProducto(producto) {
    const detalleProductoContainer = document.getElementById('detalle-producto-container');

    const detalleProductoHTML = `
        <div>
            <img src="${producto.imagen}" alt="${producto.nombre}">
            <h2>${producto.nombre}</h2>
            <p>Precio: ${producto.precio} $</p>
            <p>Categoría: ${producto.categoria}</p>
            <p>Estado: ${producto.estado}</p>
            <button onclick="volver()">Volver</button>
        </div>
    `;

    detalleProductoContainer.innerHTML = detalleProductoHTML;
}

function volver() {
    window.history.back();
}
