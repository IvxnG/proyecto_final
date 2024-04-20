// Cuando se cargue la página
document.addEventListener("DOMContentLoaded", function() {
    fetch('api/items/getAll.php')
    .then(response => response.json())
    .then(data => {
        const productCardsContainer = document.getElementById('product-cards');

        if (data && data.length > 0) {
            const lastThreeProducts = data.slice(0, 3);

            lastThreeProducts.forEach(product => {
                const card = document.createElement('div');
                card.classList.add('card');

                card.innerHTML = `
                    <img src="${product.imagen}" alt="${product.nombre}">
                    <h3>${product.nombre}</h3>
                    <p>${product.descripcion}</p>
                    <span class="price">$${product.precio}</span>
                `;

                productCardsContainer.appendChild(card);
            });
        } else {
            productCardsContainer.innerHTML = '<p>No hay productos disponibles.</p>';
        }
    })
    .catch(error => {
        console.error('Error al obtener los productos:', error);
    });
});
