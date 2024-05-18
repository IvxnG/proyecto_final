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
                showAlert('Registra tu cuenta!', 'success');
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

document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let nombreCompleto = document.getElementById('fullname').value.trim();
    let nombreUsuario = document.getElementById('username').value.trim();
    let email = document.getElementById('email').value.trim();
    let contrasena = document.getElementById('password').value.trim();

    // Validaciones
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const namePattern = /^[a-zA-Z\s]+$/;
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()\/-=+{};:,<.>]).{8,}$/;


    if (!nombreCompleto || !nombreUsuario || !email || !contrasena) {
        showAlert('Por favor, complete todos los campos.');
        return;
    }

    if (!namePattern.test(nombreCompleto)) {
        showAlert('El nombre completo solo debe contener letras y espacios.');
        return;
    }

    if (!emailPattern.test(email)) {
        showAlert('Por favor, introduzca un correo electrónico válido.');
        return;
    }

    if (!passwordPattern.test(contrasena)) {
        showAlert('La contraseña debe tener al menos 8 caracteres, incluyendo letras y números.');
        return;
    }

    // Crear objeto de datos a enviar
    let data = {
        nombre_completo: nombreCompleto,
        nombre_usuario: nombreUsuario,
        email: email,
        contrasena: contrasena
    };

    fetch('http://localhost/proyecto_final/api/users/auth/register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Ocurrió un error al registrar el usuario.');
        }
        return response.json();
    })
    .then(function(data) {
        showAlert('Usuario registrado correctamente. Por favor, inicia sesión.', 'success');
        setTimeout(function() {
            window.location.href = 'login.html';
        }, 3000);
    })
    .catch(function(error) {
        console.error('Error:', error.message);
        showAlert('Ocurrió un error al registrar el usuario. Por favor, inténtalo de nuevo.');
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
