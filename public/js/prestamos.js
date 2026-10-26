function iniciarFormularioPrestamo() {
    const formulario = document.getElementById('form-prestamo');
    const libro = document.getElementById('prestamo-libro');
    const usuario = document.getElementById('prestamo-usuario');
    const boton = formulario.querySelector('button');

    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        // Validación del lado cliente (la API valida de nuevo del lado servidor)
        const libroValido = libro.value !== '';
        const usuarioValido = Number.isInteger(Number(usuario.value)) && Number(usuario.value) > 0;

        marcarInvalido(libro, !libroValido);
        marcarInvalido(usuario, !usuarioValido);

        if (!libroValido || !usuarioValido) {
            mostrarMensaje('Selecciona un libro e indica un ID de usuario válido.', 'error');
            return;
        }

        boton.disabled = true;

        try {
            const respuesta = await api('/prestamos', {
                method: 'POST',
                body: JSON.stringify({
                    libro_id: Number(libro.value),
                    user_id: Number(usuario.value),
                }),
            });

            mostrarMensaje(respuesta.message);

            // Refresca lo que cambió: stock de los libros y estadísticas
            await Promise.all([
                cargarLibros(document.getElementById('buscar').value),
                cargarOpcionesLibros(),
                cargarEstadisticas(),
            ]);
        } catch (error) {
            mostrarMensaje(textoDeError(error), 'error');
        } finally {
            boton.disabled = false;
        }
    });
}