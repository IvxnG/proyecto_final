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

            if (data.existe) {
                window.location.href = '../index.html';
            } else {
                localStorage.clear();
                showAlert('Usuario no válido, inicia sesión de nuevo!', 'error');
            }
        } catch (error) {
            console.error('Error al verificar el Usuario:', error);
            localStorage.clear();
        }
    }else{
        localStorage.clear();
    }
}

verificarYRedirigir();



document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let nombreUsuario = document.getElementById('username').value.trim();
    let contrasena = document.getElementById('password').value.trim();

    if (!nombreUsuario || !contrasena) {
        showAlert('Por favor, complete todos los campos.');
        return;
    }

    // Crear objeto de datos a enviar
    let data = {
        nombre_usuario: nombreUsuario,
        contrasena: contrasena
    };

    fetch('http://localhost/proyecto_final/api/users/auth/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Ocurrió un error al iniciar sesión.');
        }
        return response.json();
    })
    .then(function(data) {
        localStorage.setItem('token', data.token);
        localStorage.setItem('expiracion', data.expiracion);
        localStorage.setItem('nombreUsuario', nombreUsuario);
        localStorage.setItem('idUsuario', data.id_usuario);

        showAlert('Inicio de sesión exitoso!', 'success');
        setTimeout(function() {
            window.location.href = '../index.html';
        }, 3000);
    })
    .catch(function(error) {
        console.error('Error:', error.message);
        showAlert('Credenciales incorrectas. Por favor, inténtalo de nuevo.');
    });
});

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
