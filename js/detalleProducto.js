verificarYRedirigir();

document.addEventListener('DOMContentLoaded', function () {
    mostrarDetalleProducto();
});

let idUser;
function mostrarDetalleProducto() {
    const urlParams = new URLSearchParams(window.location.search);
    const idProducto = urlParams.get('id');

    fetch(`http://localhost/proyecto_final/api/items/getDetails.php?id=${idProducto}`)
        .then(response => response.json())
        .then(data => mostrarProducto(data))
        .catch(error => console.error('Error al obtener detalles del producto:', error));
}

function mostrarProducto(producto) {
    const detalleProductoContainer = document.getElementById('detalle-producto');

    if (producto.mensaje) {
        detalleProductoContainer.innerHTML = `<p>${producto.mensaje}</p>`;
    } else {
        const ubicacion = producto.ubicacion ? producto.ubicacion : "Sin especificar";
        idUser = producto.id_usuario;
        detalleProductoContainer.innerHTML = `
            <img src="data:image/jpeg;base64,${producto.imagen}" alt="${producto.nombre}" /> 
            <h3>${producto.nombre}</h3>
            <p>Estado: ${producto.estado}</p>
            <p>Categoria: ${producto.categoria}</p>
            <p>Precio: <b>${producto.precio} €</b></p>
            <p>Descripción: ${producto.descripcion}</p>
            <p>Ubicación: ${ubicacion}</p>
            <button class="btn-comprar" onClick=comprarItem(${producto.id_producto})><i class="fas fa-shopping-cart"></i>Comprar</button>
            <button class="btn-reportar" onClick=abrirModal(${producto.id_producto}) ><i class="fas fa-flag"></i> Reportar</button>
            `;
    }
}

function showAlert(message, type = 'error') {
    const alertMessage = document.getElementById('alertMessage');
    alertMessage.textContent = message;
    alertMessage.style.backgroundColor = type === 'error' ? '#ffdddd' : '#ddffdd';
    alertMessage.style.color = type === 'error' ? '#d8000c' : '#4f8a10';
    alertMessage.style.borderColor = type === 'error' ? '#d8000c' : '#4f8a10';
    alertMessage.style.display = 'block';

    setTimeout(function () {
        alertMessage.style.display = 'none';
    }, 3000);
}

async function verificarYRedirigir() {
    const variables = ['token', 'expiracion', 'idUsuario', 'nombreUsuario'];

    const todasExisten = variables.every(variable => localStorage.getItem(variable) !== null);

    if (todasExisten) {
        const idUsuario = localStorage.getItem('idUsuario');
        const url = `http://localhost/proyecto_final/api/users/auth/checkUser.php?idUsuario=${idUsuario}`;

        try {
            const response = await fetch(url);
            const data = await response.json();

            if (!data.existe) {
                localStorage.clear();
                showAlert('Usuario no válido, inicia sesión de nuevo!', 'error');
                setTimeout(function () {
                    window.location.href = 'login.html';
                }, 2000);
            }

        } catch (error) {
            console.error('Error al verificar el Usuario:', error);
            localStorage.clear();
        }
    } else {
        localStorage.clear();
        showAlert('Debes estar registrado para poder comprar!', 'error');
        setTimeout(function () {
            window.location.href = 'login.html';
        }, 2000);
    }
}

function comprarItem(id) {
    let idComprobar = localStorage.getItem("idUsuario")
    if (idUser == idComprobar) {
        showAlert('No puedes comprar tu propio producto!', 'error');
        setTimeout(function () {
            window.location.href = 'productos.html';
        }, 2000);
    } else {
        if (confirm("¿Estás seguro de que quieres comprar este producto?")) {
            const datosProducto = {
                id_producto: id
            };

            fetch('http://localhost/proyecto_final/api/items/crud/delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify(datosProducto)
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al comprar el producto');
                    }
                    return response.json();
                })
                .then(data => {
                    showAlert('La compra se realizo de manera exitosa!', 'success');
                    setTimeout(function () {
                        window.location.href = '../index.html';
                    }, 3000);
                })
                .catch(error => {
                    showAlert('Error al comprar el producto', 'error');
                    setTimeout(function () {
                        window.location.href = '../index.html';
                    }, 3000);
                });
        }
    }
}
function abrirModal(idProducto) {
    const modal = document.getElementById('reportModal');
    modal.style.display = 'block';

    const reportForm = document.getElementById('reportForm');
    reportForm.onsubmit = function (event) {
        event.preventDefault();
        reportarItem(idProducto);
    };
}

function closeModal() {
    const modal = document.getElementById('reportModal');
    modal.style.display = 'none';
}

function reportarItem(idProducto) {
    const motivo = document.getElementById('reportMotivo').value;

    if (!motivo.trim()) {
        showAlert('Debes proporcionar un motivo para el reporte.', 'error');
        return;
    }

    const datosReporte = {
        id_producto: idProducto,
        motivo: motivo
    };

    fetch('http://localhost/proyecto_final/api/items/crud/report.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datosReporte)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al reportar el producto');
            }
            return response;
        })
        .then(data => {
            showAlert('El producto ha sido reportado correctamente.', 'success');
            closeModal();
        })
        .catch(error => {
            showAlert('Error al reportar el producto.', 'error');
        });
} 