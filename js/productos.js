document.addEventListener('DOMContentLoaded', function () {
    obtenerProductos();
});

function obtenerProductos() {
    fetch('http://localhost/proyecto_final/api/items/getAll.php')
        .then(response => response.json())
        .then(data => mostrarProductos(data))
        .catch(error => console.error('Error al obtener productos:', error));
}

function mostrarProductos(productos) {
    const productCardsContainer = document.getElementById('productos-container');

    if (productos.length === 0) {
        productCardsContainer.innerHTML = '<p>No hay productos disponibles.</p>';
    } else {
        productCardsContainer.innerHTML = '';
        productos.forEach(producto => {
            const card = document.createElement('div');
            card.classList.add('card');

            card.innerHTML = `
                <img src="https://picsum.photos/id/404/367/267" alt="${producto.nombre}"/>
                <h3>${producto.nombre}</h3>
                <div class="focus-content">
                  <p>Estado: ${producto.estado}</p>
                  <p>Categoria: ${producto.categoria}</p>
                  <p>Precio: <b>${producto.precio} €</b></p>
                  </p>
                </div>
            `;
            productCardsContainer.appendChild(card);
        });
    }
}
// const productoElement = document.getElementById(`producto-${producto.id_producto}`);
// productoElement.addEventListener('click', function () {
//     window.location.href = `detalle_producto.html?id_producto=${producto.id_producto}`;
// });
