<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca</title>
    <link rel="stylesheet" href="{{ asset('css/biblioteca.css') }}">
</head>
<body>
    <header class="barra">
        <h1>Biblioteca</h1>
        <div id="sesion" hidden>
            <span id="usuario-activo"></span>
            <button id="btn-salir" type="button">Salir</button>
        </div>
    </header>

    <div id="mensaje" class="mensaje" hidden></div>

    <main>
        <section id="seccion-login" class="tarjeta">
            <h2>Iniciar sesión</h2>
            <form id="form-login" novalidate>
                <label>Correo
                    <input type="email" id="login-email" value="admin@biblioteca.test" required>
                </label>
                <label>Contraseña
                    <input type="password" id="login-password" required>
                </label>
                <button type="submit">Entrar</button>
            </form>
        </section>

        <div id="app" hidden>
            <section class="tarjeta">
                <h2>Resumen</h2>
                <div class="estadisticas">
                    <div class="stat"><span id="stat-libros">0</span><small>Libros</small></div>
                    <div class="stat"><span id="stat-disponibles">0</span><small>Disponibles</small></div>
                    <div class="stat"><span id="stat-activos">0</span><small>Préstamos activos</small></div>
                    <div class="stat"><span id="stat-vencidos">0</span><small>Préstamos vencidos</small></div>
                </div>
            </section>

            <section class="tarjeta">
                <h2>Libros</h2>
                <input type="search" id="buscar" placeholder="Buscar por título...">
                <table>
                    <thead>
                        <tr><th>Título</th><th>Autores</th><th>Año</th><th>Stock</th></tr>
                    </thead>
                    <tbody id="tabla-libros"></tbody>
                </table>
                <p id="libros-vacio" hidden>No se encontraron libros.</p>
            </section>

            <section class="tarjeta">
                <h2>Nuevo préstamo</h2>
                <form id="form-prestamo" novalidate>
                    <label>Libro
                        <select id="prestamo-libro" required></select>
                    </label>
                    <label>ID de usuario
                        <input type="number" id="prestamo-usuario" min="1" required>
                    </label>
                    <button type="submit">Prestar</button>
                </form>
            </section>
        </div>
    </main>

    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/libros.js') }}"></script>
    <script src="{{ asset('js/prestamos.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>