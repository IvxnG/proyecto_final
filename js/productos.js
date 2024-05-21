document.addEventListener('DOMContentLoaded', function () {
    obtenerProductos();
    obtenerPaginasTotales(); // Llamada para obtener el número total de páginas

    document.getElementById('filtros-form').addEventListener('submit', function (event) {
        event.preventDefault();
        obtenerProductosConPaginacion();
    });

    document.getElementById('pagina-anterior').addEventListener('click', function () {
        if (paginaActual > 1) {
            paginaActual--;
            obtenerProductosConPaginacion(paginaActual);
        }
    });

    document.getElementById('pagina-siguiente').addEventListener('click', function () {
        if (paginaActual < paginasTotales) {
            paginaActual++;
            obtenerProductosConPaginacion(paginaActual);
        }
    });
});

let paginaActual = 1;
let paginasTotales = 1; // Variable para almacenar el número total de páginas

function obtenerPaginasTotales() {
    fetch('http://localhost/proyecto_final/api/items/getItemPages.php')
        .then(response => response.json())
        .then(data => {
            paginasTotales = data.paginas_totales; 
            document.getElementById('pagina-actual').textContent = paginaActual;
        })
        .catch(error => console.error('Error al obtener el número total de páginas:', error));
}

function obtenerProductosConPaginacion(pagina) {
    const categoria = document.getElementById('categoria').value;
    const precio_min = document.getElementById('precio_min').value;
    const precio_max = document.getElementById('precio_max').value;
    const estado = document.getElementById('estado').value;

    const filtros = {
        categoria: categoria,
        precio_min: precio_min,
        precio_max: precio_max,
        estado: estado
    };
    document.getElementById('pagina-actual').textContent = paginaActual;
    obtenerProductos(filtros, pagina);
}

function obtenerProductos(filtros = {}, pagina = 1) {
    let url = `http://localhost/proyecto_final/api/items/getAll.php?lote=${pagina}`;
    const parametros = [];

    if (filtros.categoria) {
        parametros.push(`categoria=${encodeURIComponent(filtros.categoria)}`);
    }
    if (filtros.precio_min) {
        parametros.push(`precio_min=${encodeURIComponent(filtros.precio_min)}`);
    }
    if (filtros.precio_max) {
        parametros.push(`precio_max=${encodeURIComponent(filtros.precio_max)}`);
    }
    if (filtros.estado) {
        parametros.push(`estado=${encodeURIComponent(filtros.estado)}`);
    }

    if (parametros.length > 0) {
        url += '&' + parametros.join('&');
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                mostrarProductos(data)
            } else {
                const productCardsContainer = document.getElementById('productos-container');
                productCardsContainer.innerHTML = '<h2>No tienes productos en venta</h2>';
            }
        })
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
                <img src="data:image/jpeg;base64,${producto.imagen}" /> 
                <h3>${producto.nombre}</h3>
                <div class="focus-content">
                  <p>Estado: ${producto.estado}</p>
                  <p>Categoria: ${producto.categoria}</p>
                  <p>Precio: <b>${producto.precio} €</b></p>
                </div>
            `;

            // Añadir evento de clic a la tarjeta del producto
            card.addEventListener('click', function () {
                window.location.href = `detalleProducto.html?id=${producto.id_producto}`;
            });

            productCardsContainer.appendChild(card);
        });
    }
}
