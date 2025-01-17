const idUsuario = localStorage.getItem('idUsuario');
let idProducto1;
const token = localStorage.getItem("token");
// Expresiones regulares para validaciones
const regexNombreCompleto = /^[A-Za-z\s]+$/;
const regexNombreUsuario = /^[A-Za-z0-9_]+$/;
const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
let regexNombre = /^[A-Za-z\s]+$/;
let regexPrecio = /^[0-9.,]+$/;
let regexDescripcion = /.+/;


fetch(`https://easymarketivan.000webhostapp.com/api/users/auth/checkUser.php?idUsuario=${idUsuario}`)
  .then(response => response.json())
  .then(data => {
    // Verificar si el usuario existe
    if (data.existe === false) {
      // Si el usuario no existe, redireccionar al índice
      localStorage.clear();
      window.location.href = '../index.html';
    }
  })
  .catch(error => console.error('Error al verificar el usuario:', error));

fetch(`https://easymarketivan.000webhostapp.com/api/users/crud/getUser.php?idUsuario=${idUsuario}`)
  .then(response => response.json())
  .then(data => {
    document.getElementById('nombre').value = data.nombre_completo;
    document.getElementById('username').value = data.nombre_usuario;
    document.getElementById('email').value = data.email;
  })
  .catch(error => console.error('Error al obtener los datos del usuario:', error));

document.getElementById('logoutButton').addEventListener('click', function () {
  localStorage.clear();
  window.location.href = '../index.html';
});


function actualizarUsuario() {
  const nombreCompletoInput = document.getElementById('nombre');
  const nombreUsuarioInput = document.getElementById('username');
  const emailInput = document.getElementById('email');
  const regexNombreCompleto = /^[A-Za-z\s]+$/;
  const regexNombreUsuario = /^[A-Za-z0-9_]+$/;
  const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!regexNombreCompleto.test(nombreCompletoInput.value) || 
      !regexNombreUsuario.test(nombreUsuarioInput.value) || 
      !regexEmail.test(emailInput.value)) 
      {
        showAlert('Datos incompleto o no válidos', 'error');
        return;
      }
  const datosUsuario = {
    id_usuario: idUsuario,
    nombre_completo: nombreCompletoInput.value,
    nombre_usuario: nombreUsuarioInput.value,
    email: emailInput.value,
  };

  // Realizar solicitud Fetch para actualizar el usuario
  fetch(`https://easymarketivan.000webhostapp.com/api/users/crud/update.php`,
    {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
      },
      body: JSON.stringify(datosUsuario)
    })
    .then(response => {
      console.log(JSON.stringify(datosUsuario));
      if (response.status == 200) {
        showAlert('Datos actualizados', 'success');
      } else {
        showAlert('Sesión no válida!', 'error');
        localStorage.clear();
        setTimeout(function () {
          window.location.href = '../index.html';
        }, 2000);
      }
    })
    .catch(error => {
      showAlert('Error al actualizar los datos', 'error');
    });
}

fetch(`https://easymarketivan.000webhostapp.com/api/items/getSellsByUser.php?id_usuario=${idUsuario}`, {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + token
  }
})
  .then(response => {
    console.log(response);
    if (response.status === 200) {
      return response.json();
    }
  })
  .then(data => {
    let comprados = document.getElementById("productosContainer2");
    if (data.length > 0 ) {
      comprados.innerHTML = "";
      data.forEach(producto => {
        console.log('ID del producto:', producto.id_producto);
        fetch(`https://easymarketivan.000webhostapp.com/api/items/getDetails.php?id=${producto.id_producto}`)
          .then(response => response.json())
          .then(detallesProducto => {
            const productoHTML = `
              <div class="producto">
                <h3><b>${detallesProducto.nombre}</b></h3>
                <p>Precio: ${detallesProducto.precio}€</p>
                <p>Categoría: ${detallesProducto.categoria}</p>
                <img style="width:120px; border: solid 1px black; border-radius:20px" src="data:image/jpeg;base64,${detallesProducto.imagen}" alt="${detallesProducto.nombre}">
              </div>
            `;
            comprados.innerHTML += productoHTML;
          })
          .catch(error => console.error('Error al obtener detalles del producto:', error));
      });
    } else {
      comprados.innerHTML = '<p>No hay productos comprados para este usuario.</p>';
    }
  });

document.getElementById('actualizarBtn').addEventListener('click', function (event) {
  event.preventDefault();
  actualizarUsuario();
});

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

fetch(`https://easymarketivan.000webhostapp.com/api/items/getUserProducts.php`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ id_usuario: idUsuario })
})
  .then(response => {
    if (!response.ok) {
      throw new Error('Error al obtener productos del usuario');
    }
    return response.json();
  })
  .then(data => {
    if (data && data.length > 0) {
      mostrarProductos(data);
    }
  })
  .catch(error => {
    console.error('Error:', error);
  });

function mostrarProductos(productos) {
  const container = document.getElementById('productosContainer');
  container.innerHTML = '';

  if (productos.length === 0) {
    container.innerHTML = '<p>No hay productos disponibles para este usuario.</p>';
  } else {
    productos.forEach(producto => {
      const productoElement = document.createElement('div');
      productoElement.classList.add('producto');
      productoElement.innerHTML = `
        <img src="data:image/jpeg;base64,${producto.imagen}" style="width: 10em; border:solid 1px; border-radius:5px;" /> 
        <h3>${producto.nombre}</h3>
        <p>Precio: ${producto.precio} €</p>
        <p>Descripción: ${producto.descripcion}</p>
        <button class="btn-borrar btn-action" onclick="eliminarProducto(${producto.id_producto})"><i class="fas fa-trash-alt"></i> Eliminar</button>
        <button class="btn-editar btn-action" onclick="editarProducto(${producto.id_producto} )"><i class="fas fa-edit"></i> Editar</button>
      `;
      container.appendChild(productoElement);
    });
  }
}

function eliminarProducto(idProducto) {
  if (confirm("¿Estás seguro de que quieres eliminar este producto?")) {
    const datosProducto = {
      id_producto: idProducto
    };

    fetch('https://easymarketivan.000webhostapp.com/api/items/crud/delete.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
      },
      body: JSON.stringify(datosProducto)
    })
      .then(response => {
        if (response.status == 200) {
          showAlert('Producto eliminado!', 'success');
          setTimeout(function () {
            window.location.href = 'productos.html';
          }, 4000);
        } else {
          showAlert('Sesión no válida!', 'error');
          localStorage.clear();
          setTimeout(function () {
            window.location.href = '../index.html';
          }, 2000);
        }
      })
      .catch(error => {
        showAlert('Error al eliminar el producto', 'error');
        console.error('Error:', error);
      });
  }
}

function editarProducto(idProducto) {
  // Obtener detalles del producto
  idProducto1 = idProducto;
  fetch(`https://easymarketivan.000webhostapp.com/api/items/getDetails.php?id=${idProducto1}`)
    .then(response => response.json())
    .then(producto => {
      document.getElementById('editNombre').value = producto.nombre;
      document.getElementById('editCategoria').value = producto.categoria;
      document.getElementById('editEstado').value = producto.estado;
      document.getElementById('editPrecio').value = producto.precio;
      document.getElementById('editDescripcion').value = producto.descripcion;
      document.getElementById('editModal').style.display = 'block';
    })
    .catch(error => console.error('Error al cargar detalles del producto:', error));
}

function closeModal() {
  document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editForm').addEventListener('submit', function (event) {
  event.preventDefault();

  // Obtén los datos del formulario
  const nombre = document.getElementById('editNombre').value;
  const precio = document.getElementById('editPrecio').value;
  const descripcion = document.getElementById('editDescripcion').value;
  const categoria = document.getElementById('editCategoria').value;
  const estado = document.getElementById('editEstado').value;

  const datosProducto = {
    id_producto: idProducto1,
    nombre: nombre,
    precio: precio,
    descripcion: descripcion,
    categoria: categoria,
    estado: estado
  };

  function validarDatosProducto(nombre, precio, descripcion) {
    let esNombreValido = regexNombre.test(nombre);
    let esPrecioValido = regexPrecio.test(precio);
    let esDescripcionValida = regexDescripcion.test(descripcion);

    return esNombreValido && esPrecioValido && esDescripcionValida;
  }

  let resultado = validarDatosProducto(nombre, precio, descripcion);
  if (!resultado) {
    showAlert('Completa todos los campos de manera correcta.', 'error');
    return;
  }
  // Realiza la solicitud fetch al PHP de actualización
  fetch(`https://easymarketivan.000webhostapp.com/api/items/crud/update.php`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify(datosProducto)
  })
    .then(response => {
      if (response.status == 200) {
        showAlert('Producto actualizado!', 'success');
        setTimeout(function () {
          window.location.reload()
        }, 2000);
      } else {
        showAlert('Sesión no válida!', 'error');
        localStorage.clear()
        setTimeout(function () {
          window.location.href = '../index.html';
        }, 2000);
      }
    })
    .catch(error => {
      alert('Error al actualizar el producto: ' + error);
    });
});

