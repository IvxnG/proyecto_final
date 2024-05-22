async function verificarYRedirigir() {
    const variables = ['token', 'expiracion', 'idUsuario', 'nombreUsuario'];

    // Comprobamos si todas las variables existen en localStorage
    const todasExisten = variables.every(variable => localStorage.getItem(variable) !== null);

    if (todasExisten) {
        // Si todas las variables existen, verificamos que el idUsuario es válido
        const idUsuario = localStorage.getItem('idUsuario');
        const url = `http://localhost/proyecto_final/api/users/auth/checkUser.php?idUsuario=${idUsuario}`;
        
        try {
            const response = await fetch(url);
            const data = await response.json();

            if (!data.existe) {
                localStorage.clear();
                showAlert('Usuario no válido, inicia sesión de nuevo!', 'error');
                setTimeout(function () {
                    window.location.href = '../index.html';
                }, 2000);
            } 
        } catch (error) {
            console.error('Error al verificar el Usuario:', error);
            localStorage.clear();
        }
    }else{
        localStorage.clear();
        window.location.href = '../index.html';
    }
}

verificarYRedirigir();

document.getElementById('createProductForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Obtener los datos del formulario
    const nombre = document.getElementById('nombre').value;
    const descripcion = document.getElementById('descripcion').value;
    const precio = document.getElementById('precio').value;
    const categoria = document.getElementById('categoria').value;
    const estado = document.getElementById('estado').value;
    const idUsuario = localStorage.getItem('idUsuario');
    const imagenInput = document.getElementById('imagen');
    const imagen = imagenInput.files[0];
    const token = localStorage.getItem('token'); // Obtener el token del localStorage

    if (!nombre || !descripcion || !precio || !categoria || !estado || !imagen) {
        showAlert('Completa todos los campos!', 'error');
        setTimeout(function() {}, 4000);
        return; 
    }

    // Crear FormData y agregar los datos del formulario
    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('descripcion', descripcion);
    formData.append('precio', precio);
    formData.append('categoria', categoria);
    formData.append('estado', estado);
    formData.append('id_usuario', idUsuario);
    formData.append('imagen', imagen);

    // Enviar datos al servidor
    enviarDatos(formData, token);
});

function enviarDatos(formData, token) {
    fetch('http://localhost/proyecto_final/api/items/crud/create.php', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token 
        },
        body: formData
    })
    .then(response => {
        if (response.status == 200) {
            showAlert('Producto puesto en venta!', 'success');
            setTimeout(function() {
                window.location.href = 'productos.html';
            }, 4000);
        } else {
                showAlert('Sesión no válida!', 'error');
                localStorage.clear()
                setTimeout(function() {
                    window.location.href = '../index.html';
                }, 2000);
        }
    })
    .catch(error => {
        console.error('Hubo un problema con tu operación de fetch:', error);
        showAlert('Error al conectar con el servidor', 'error');
    });
}

function showAlert(message, type = 'error') {
    const alertMessage = document.getElementById('alertMessage');
    alertMessage.textContent = message;
    alertMessage.style.backgroundColor = type === 'error' ? '#ffdddd' : '#ddffdd';
    alertMessage.style.color = type === 'error' ? '#d8000c' : '#4f8a10';
    alertMessage.style.borderColor = type === 'error' ? '#d8000c' : '#4f8a10';
    alertMessage.style.display = 'block';

    setTimeout(function() {
        alertMessage.style.display = 'none';
    }, 3000);
}
