# Biblioteca API

API REST para gestionar libros, autores, usuarios y préstamos. Laravel, PostgreSQL y Sanctum.

## Requisitos

- PHP 8.2 o superior (extensiones `pdo_pgsql`, `pgsql`, `intl`)
- Composer
- PostgreSQL 14 o superior

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Crea la base de datos (`createdb biblioteca`) y edita en `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=biblioteca
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
```

Luego:

```bash
php artisan migrate --seed
php artisan serve
```

El seeder crea 10 autores, 20 libros, 15 usuarios y 10 préstamos.
Usuario de prueba: `admin@biblioteca.test` / `password`.

## Endpoints (prefijo `/api`)

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/login` | Devuelve un token Bearer (máx. 5 intentos por minuto) |
| POST | `/logout` | Cierra la sesión |
| GET | `/libros` | Lista paginada. Filtros: `titulo`, `autor`, `anio`, `disponibles`, `per_page` |
| GET | `/libros/{id}` | Libro con sus autores |
| POST | `/libros` | Crea un libro |
| PUT | `/libros/{id}` | Actualiza un libro |
| DELETE | `/libros/{id}` | Soft delete |
| GET | `/prestamos` | Préstamos con libro y usuario. Filtros: `estado`, `user_id` |
| POST | `/prestamos` | Crea un préstamo |
| PUT | `/prestamos/{id}/devolver` | Marca el préstamo como devuelto |
| GET | `/autores` | Lista de autores |
| DELETE | `/autores/{id}` | Elimina un autor (409 si tiene libros) |

Todas, excepto `/login`, exigen `Authorization: Bearer <token>`.

## Reglas de negocio

- No se presta un libro sin stock.
- Un usuario no puede tener más de 3 préstamos sin devolver.
- No se elimina un autor con libros asociados.
- Los préstamos con más de 15 días se marcan como vencidos (comando programado diario).

## Comandos Artisan

```bash
php artisan biblioteca:reporte            # libros más prestados, usuarios con vencidos, libros sin stock
php artisan prestamos:marcar-vencidos     # marca los préstamos atrasados
```

## Postman

En la carpeta `entregables` están la colección y el environment. Impórtalos, elige el environment
`Biblioteca Local` y ejecuta primero `login`, que guarda el token solo.
La petición `Crear Libro` solo da 201 una vez por ISBN; cambia el ISBN para repetirla.

## Entregables

La carpeta `entregables` incluye el script SQL, el diagrama ER, la colección de Postman,
su environment y las respuestas teóricas.


## Página web

Con el servidor encendido (`php artisan serve`), abre `http://127.0.0.1:8000`.

- Login con `admin@biblioteca.test` / `password`.
- Resumen con estadísticas básicas (libros, disponibles, préstamos activos y vencidos).
- Lista de libros con búsqueda en tiempo real por título.
- Formulario para crear un préstamo, con validación del lado cliente.
- Mensajes de éxito y de error a partir de las respuestas de la API.

Archivos: `resources/views/biblioteca.blade.php`, `public/css/biblioteca.css` y `public/js/`
(`api.js`, `libros.js`, `prestamos.js`, `dashboard.js`, `main.js`). Usa JavaScript puro y `fetch`.
El token de Sanctum se guarda en `sessionStorage`, así que se borra al cerrar la pestaña.