# Respuestas teóricas

## 1. Diferencia entre hasMany() y belongsToMany()

- **hasMany()**: relación uno a muchos. La llave foránea está en la tabla del lado "muchos".
  Ejemplo: un usuario tiene muchos préstamos (`prestamos.user_id`).

  ```php
  public function prestamos(): HasMany
  {
      return $this->hasMany(Prestamo::class);
  }
  ```

- **belongsToMany()**: relación muchos a muchos. Necesita una tabla intermedia (pivote).
  Ejemplo: un libro tiene varios autores y un autor varios libros (`autor_libro`).

  ```php
  public function autores(): BelongsToMany
  {
      return $this->belongsToMany(Autor::class, 'autor_libro')->withPivot('orden_autor');
  }
  ```

## 2. Diferencia entre all(), get() y find()

- **all()**: trae todos los registros de la tabla, sin filtros. Solo conviene en tablas pequeñas.
  `Libro::all()`
- **get()**: ejecuta una consulta construida (where, orderBy, limit) y devuelve una colección.
  `Libro::disponibles()->orderBy('titulo')->get()`
- **find($id)**: busca un registro por su llave primaria y devuelve un modelo o `null`.
  `Libro::find(1)`. Con `findOrFail()` responde 404 si no existe.

  
## 3. Patrón Repository en Laravel

Es una clase intermedia entre el controller y Eloquent: el controller no consulta el modelo directamente, sino una interfaz (por ejemplo `LibroRepositoryInterface`) cuya implementación contiene las consultas.

**Cuándo usarlo:** cuando la lógica de acceso a datos es compleja o se repite, cuando se necesita cambiar la fuente de datos (otra base de datos o una API externa) o cuando se quiere probar con datos simulados.

**Cuándo no:** en proyectos pequeños, Eloquent ya es un patrón de acceso a datos (Active Record) y un repositorio encima añade código sin aportar. En este proyecto la lógica de negocio está en `PrestamoService` y las consultas simples quedan en los modelos con scopes.

## 4. Problema N+1 en Eloquent

Ocurre cuando se hace 1 consulta para traer una lista y luego 1 consulta adicional por cada elemento para cargar su relación. Con 20 libros son 21 consultas.

```php
// Mal: N+1
$libros = Libro::all();
foreach ($libros as $libro) {
    echo $libro->autores->count(); // una consulta por libro
}

// Bien: eager loading, 2 consultas en total
$libros = Libro::with('autores')->get();
```

**Solución:** cargar las relaciones por adelantado con `with()`. En este proyecto se usa `Libro::with('autores')` y `Prestamo::with(['libro.autores', 'user'])`. En los Resources se usa `whenLoaded()` para no disparar consultas extras. También se puede usar `Model::preventLazyLoading()` en desarrollo para detectarlo.


## 5. Autenticación y autorización en APIs

- **Autenticación**: verificar **quién eres**. En este proyecto, el usuario envía correo y contraseña a `POST /api/login` y recibe un token de Sanctum. Si falla, la API responde **401 Unauthorized**.
- **Autorización**: decidir **qué puedes hacer** una vez identificado (roles, permisos, policies o gates). Si estás autenticado pero no tienes permiso, la API responde **403 Forbidden**.

La autenticación va primero. En este proyecto el middleware `auth:sanctum` protege las rutas, y la regla de que un usuario inactivo no pueda iniciar sesión es parte de la autenticación. Una mejora posible es agregar roles (administrador y socio) con policies para la autorización.

## 6. Diferencia entre PATCH y PUT en APIs REST

- **PUT**: reemplaza el recurso **completo**. Hay que enviar todos los campos; los que falten se consideran vacíos. Es idempotente: repetirlo da el mismo resultado.
- **PATCH**: modifica **solo los campos enviados** y deja el resto igual.

Ejemplo en este proyecto: `PUT /api/libros/22` exige título, ISBN, año, páginas, stock y autores (`UpdateLibroRequest` los marca todos como `required`). Para cambiar solo el stock bastaría un `PATCH` con `{"stock_disponible": 8}`.

Laravel registra `PUT` y `PATCH` hacia el mismo método `update` con `apiResource`.