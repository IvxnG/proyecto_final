document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let nombreUsuario = document.getElementById('username').value;
    let contrasena = document.getElementById('password').value;

    if (!nombreUsuario || !contrasena) {
        alert('Por favor, complete todos los campos.');
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

        alert('Inicio de sesión exitoso!');
        window.location.href = '../index.html'; 
    })
    .catch(function(error) {
        // Manejar errores
        console.error('Error:', error.message);
        if (error.message.includes('401')) {
            alert('Credenciales incorrectas. Por favor, inténtalo de nuevo.');
        } else if (error.message.includes('404')) {
            alert('Usuario no encontrado. Por favor, verifica tu nombre de usuario.');
        } else {
            alert('Credenciales incorrectas. Por favor, inténtalo de nuevo.');
        }
    });
});
