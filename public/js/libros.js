let temporizadorBusqueda;

async function cargarLibros(titulo = '') {
    const parametros = new URLSearchParams({ per_page: 50 });
    if (titulo.trim() !== '') {
        parametros.set('titulo', titulo.trim());
    }

    const respuesta = await api('/libros?' + parametros.toString());
    pintarLibros(respuesta.data);
    return respuesta;
}

function pintarLibros(libros) {
    const cuerpo = document.getElementById('tabla-libros');
    document.getElementById('libros-vacio').hidden = libros.length > 0;

    cuerpo.innerHTML = libros.map((libro) => {
        const autores = libro.autores.map((autor) => autor.nombre_completo).join(', ');
        const stock = libro.stock_disponible > 0
            ? libro.stock_disponible
            : '<span class="sin-stock">Sin stock</span>';

        return `<tr>
            <td>${escaparHtml(libro.titulo)}</td>
            <td>${escaparHtml(autores)}</td>
            <td>${libro.anio_publicacion}</td>
            <td>${stock}</td>
        </tr>`;
    }).join('');
}

// Búsqueda en tiempo real: espera 300 ms después de la última tecla
function iniciarBusqueda() {
    const campo = document.getElementById('buscar');

    campo.addEventListener('input', () => {
        clearTimeout(temporizadorBusqueda);
        temporizadorBusqueda = setTimeout(async () => {
            try {
                await cargarLibros(campo.value);
            } catch (error) {
                mostrarMensaje(textoDeError(error), 'error');
            }
        }, 300);
    });
}

// Llena el desplegable del formulario con los libros que tienen stock
async function cargarOpcionesLibros() {
    const respuesta = await api('/libros?disponibles=1&per_page=100');
    const select = document.getElementById('prestamo-libro');

    select.innerHTML = '<option value="">Selecciona un libro</option>'
        + respuesta.data.map((libro) =>
            `<option value="${libro.id}">${escaparHtml(libro.titulo)} (stock: ${libro.stock_disponible})</option>`
        ).join('');
}