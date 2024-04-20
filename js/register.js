document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let nombreCompleto = document.getElementById('fullname').value;
    let nombreUsuario = document.getElementById('username').value;
    let email = document.getElementById('email').value;
    let contrasena = document.getElementById('password').value;

    if (!nombreCompleto || !nombreUsuario || !email || !contrasena) {
        alert('Por favor, complete todos los campos.');
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
        alert('Usuario registrado correctamente. Por favor, inicia sesión.');
        window.location.href = 'login.html';
    })
    .catch(function(error) {
        console.error('Error:', error.message);
        alert('Ocurrió un error al registrar el usuario. Por favor, inténtalo de nuevo.');
    });
});
