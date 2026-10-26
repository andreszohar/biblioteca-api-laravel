const API_URL = '/api';
const TOKEN_KEY = 'biblioteca_token';

function obtenerToken() {
    try {
        return sessionStorage.getItem(TOKEN_KEY);
    } catch (e) {
        return null;
    }
}

function guardarToken(token) {
    try {
        sessionStorage.setItem(TOKEN_KEY, token);
    } catch (e) {}
}

function borrarToken() {
    try {
        sessionStorage.removeItem(TOKEN_KEY);
    } catch (e) {}
}

// Llamada AJAX a la API: devuelve el JSON o lanza un Error con status y errores
async function api(ruta, opciones = {}) {
    const cabeceras = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    };
    const token = obtenerToken();
    if (token) {
        cabeceras['Authorization'] = 'Bearer ' + token;
    }

    const respuesta = await fetch(API_URL + ruta, { ...opciones, headers: cabeceras });

    let datos = null;
    try {
        datos = await respuesta.json();
    } catch (e) {}

    if (!respuesta.ok) {
        const error = new Error((datos && datos.message) || 'Error inesperado.');
        error.status = respuesta.status;
        error.errores = (datos && datos.errors) || {};
        throw error;
    }

    return datos;
}

// Si el error trae errores de validación, muestra el primero
function textoDeError(error) {
    const lista = Object.values(error.errores || {});
    return lista.length ? lista[0][0] : error.message;
}

let temporizadorMensaje;

function mostrarMensaje(texto, tipo = 'exito') {
    const caja = document.getElementById('mensaje');
    caja.textContent = texto;
    caja.className = 'mensaje ' + tipo;
    caja.hidden = false;
    clearTimeout(temporizadorMensaje);
    temporizadorMensaje = setTimeout(() => {
        caja.hidden = true;
    }, 5000);
}

function marcarInvalido(campo, invalido) {
    campo.classList.toggle('invalido', invalido);
}

// Evita inyección de HTML (XSS) al pintar datos que vienen de la API
function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}