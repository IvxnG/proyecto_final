document.getElementById('createProductForm').addEventListener('submit', function(event) {
    event.preventDefault(); 
    console.log('Formulario enviado');

    // Obtener los datos del formulario
    const nombre = document.getElementById('nombre').value;
    const descripcion = document.getElementById('descripcion').value;
    const precio = document.getElementById('precio').value;
    const categoria = document.getElementById('categoria').value;
    const estado = document.getElementById('estado').value;
    const idUsuario = localStorage.getItem('idUsuario');
    const imagenInput = document.getElementById('imagen');
    const imagen = imagenInput.files[0];

    if (!nombre || !descripcion || !precio || !categoria || !estado || !imagen) {
        alert('Por favor, complete todos los campos.');
        return; 
    }

    // Crear objeto item
    const item = {
        nombre: nombre,
        descripcion: descripcion,
        precio: precio,
        categoria: categoria,
        estado: estado,
        id_usuario: idUsuario,
        imagen: imagen
    };
    console.log(item.imagen);

    
    enviarDatos(item);
});

function enviarDatos(item) {
    fetch('http://localhost/proyecto_final/api/items/crud/create.php', {
        method: 'POST',
        body: JSON.stringify(item),
        headers: {
            'Content-Type': 'application/json' 
        }
    })
    .then(response => {
        console.log('fecheando q1');
        if (response.ok) {
            return response.json();
        }
        console.log(response);
        throw new Error('Network response was not ok.');
    })
    .then(data => {
        console.log('fecheando q2');
        console.log(data);
        alert('Producto creado exitosamente!');
        window.location.href = 'index.html'; 
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
    });
}
