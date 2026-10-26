function mostrarPanel(usuario) {
    document.getElementById('seccion-login').hidden = true;
    document.getElementById('app').hidden = false;
    document.getElementById('sesion').hidden = false;
    document.getElementById('usuario-activo').textContent = usuario.nombre;
    document.getElementById('prestamo-usuario').value = usuario.id;
}

function mostrarLogin() {
    document.getElementById('seccion-login').hidden = false;
    document.getElementById('app').hidden = true;
    document.getElementById('sesion').hidden = true;
}

async function cargarPanel() {
    await Promise.all([cargarLibros(), cargarOpcionesLibros(), cargarEstadisticas()]);
}

function iniciarLogin() {
    const formulario = document.getElementById('form-login');
    const correo = document.getElementById('login-email');
    const clave = document.getElementById('login-password');

    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        // Validación del lado cliente
        const correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value.trim());
        const claveValida = clave.value !== '';

        marcarInvalido(correo, !correoValido);
        marcarInvalido(clave, !claveValida);

        if (!correoValido || !claveValida) {
            mostrarMensaje('Escribe un correo válido y tu contraseña.', 'error');
            return;
        }

        try {
            const respuesta = await api('/login', {
                method: 'POST',
                body: JSON.stringify({ email: correo.value.trim(), password: clave.value }),
            });

            guardarToken(respuesta.data.token);
            clave.value = '';
            mostrarPanel(respuesta.data.usuario);
            await cargarPanel();
            mostrarMensaje(respuesta.message);
        } catch (error) {
            mostrarMensaje(textoDeError(error), 'error');
        }
    });
}

function iniciarSalida() {
    document.getElementById('btn-salir').addEventListener('click', async () => {
        try {
            await api('/logout', { method: 'POST' });
        } catch (error) {}

        borrarToken();
        mostrarLogin();
        mostrarMensaje('Sesión cerrada.');
    });
}

async function arrancar() {
    iniciarLogin();
    iniciarSalida();
    iniciarBusqueda();
    iniciarFormularioPrestamo();

    if (!obtenerToken()) {
        mostrarLogin();
        return;
    }

    // Si ya había un token en esta pestaña, se comprueba que siga siendo válido
    try {
        const respuesta = await api('/me');
        mostrarPanel(respuesta.data);
        await cargarPanel();
    } catch (error) {
        borrarToken();
        mostrarLogin();
    }
}

document.addEventListener('DOMContentLoaded', arrancar);