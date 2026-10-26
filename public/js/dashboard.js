// Pide solo 1 elemento por página: lo único que interesa es meta.total
async function totalDe(ruta) {
    const respuesta = await api(ruta);
    return respuesta.meta.total;
}

async function cargarEstadisticas() {
    const [libros, disponibles, activos, vencidos] = await Promise.all([
        totalDe('/libros?per_page=1'),
        totalDe('/libros?disponibles=1&per_page=1'),
        totalDe('/prestamos?estado=activo&per_page=1'),
        totalDe('/prestamos?estado=vencido&per_page=1'),
    ]);

    document.getElementById('stat-libros').textContent = libros;
    document.getElementById('stat-disponibles').textContent = disponibles;
    document.getElementById('stat-activos').textContent = activos;
    document.getElementById('stat-vencidos').textContent = vencidos;
}