document.addEventListener('DOMContentLoaded', function () {
    actualizarMenu();
});

function actualizarMenu() {
    const menuItems = document.getElementById('menuItems');

    const idUsuario = localStorage.getItem('idUsuario');
    const token = localStorage.getItem('token');
    const nombreUsuario = localStorage.getItem('nombreUsuario');

    if (idUsuario && token && nombreUsuario) {
        menuItems.innerHTML = `
            <li><a href="../html/createProduct.html">Subir Item</a></li>
            <li><a href="../html/profile.html">Mi Perfil</a></li>
            <li><a href="../html/sobreNosotros.html">Sobre Nosotros</a></li>
            <li><a href="#" id="logout">Cerrar sesión</a></li>
        `;

        document.getElementById('logout').addEventListener('click', function () {
            localStorage.removeItem('idUsuario');
            localStorage.removeItem('token');
            localStorage.removeItem('nombreUsuario');
            localStorage.removeItem('expiracion');
            localStorage.clear();
            window.location.href = '../index.html';
        });
    } else {
        menuItems.innerHTML = `
            <li><a href="../html/registro.html">Registrarse</a></li>
            <li><a href="../html/login.html">Iniciar sesión</a></li>
            <li><a href="#">Sobre Nosotros</a></li>
        `;
    }
}
