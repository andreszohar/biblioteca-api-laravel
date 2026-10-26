-- ============================================
-- Sistema de gestión de biblioteca (PostgreSQL)
-- ============================================

-- Tabla 1: AUTORES
CREATE TABLE autores (
    id               BIGSERIAL PRIMARY KEY,
    nombre           VARCHAR(100) NOT NULL,
    apellido         VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    nacionalidad     VARCHAR(80),
    biografia        TEXT,
    created_at       TIMESTAMP,
    updated_at       TIMESTAMP
);

-- Tabla 2: LIBROS
CREATE TABLE libros (
    id                BIGSERIAL PRIMARY KEY,
    titulo            VARCHAR(255) NOT NULL,
    isbn              VARCHAR(17)  NOT NULL UNIQUE,
    anio_publicacion  SMALLINT     NOT NULL,
    numero_paginas    SMALLINT     NOT NULL CHECK (numero_paginas > 0),
    descripcion       TEXT,
    stock_disponible  INTEGER      NOT NULL DEFAULT 0 CHECK (stock_disponible >= 0),
    created_at        TIMESTAMP,
    updated_at        TIMESTAMP,
    deleted_at        TIMESTAMP
);

-- Tabla 3: USERS (personas que piden préstamos)
CREATE TABLE users (
    id             BIGSERIAL PRIMARY KEY,
    nombre         VARCHAR(255) NOT NULL,
    email          VARCHAR(255) NOT NULL UNIQUE,
    telefono       VARCHAR(30),
    fecha_registro DATE NOT NULL DEFAULT CURRENT_DATE,
    estado         VARCHAR(10) NOT NULL DEFAULT 'activo'
                   CHECK (estado IN ('activo', 'inactivo')),
    password       VARCHAR(255) NOT NULL,
    created_at     TIMESTAMP,
    updated_at     TIMESTAMP
);

-- Tabla 4: AUTOR_LIBRO (pivote: relación muchos a muchos)
CREATE TABLE autor_libro (
    id          BIGSERIAL PRIMARY KEY,
    autor_id    BIGINT   NOT NULL REFERENCES autores(id) ON DELETE RESTRICT,
    libro_id    BIGINT   NOT NULL REFERENCES libros(id)  ON DELETE CASCADE,
    orden_autor SMALLINT NOT NULL DEFAULT 1,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP,
    UNIQUE (autor_id, libro_id)
);

-- Tabla 5: PRESTAMOS
CREATE TABLE prestamos (
    id                        BIGSERIAL PRIMARY KEY,
    libro_id                  BIGINT NOT NULL REFERENCES libros(id) ON DELETE RESTRICT,
    user_id                   BIGINT NOT NULL REFERENCES users(id)  ON DELETE RESTRICT,
    fecha_prestamo            DATE NOT NULL,
    fecha_devolucion_estimada DATE NOT NULL,
    fecha_devolucion_real     DATE,
    estado                    VARCHAR(10) NOT NULL DEFAULT 'activo'
                              CHECK (estado IN ('activo', 'devuelto', 'vencido')),
    created_at                TIMESTAMP,
    updated_at                TIMESTAMP,
    CHECK (fecha_devolucion_estimada >= fecha_prestamo)
);

-- Índice para acelerar la búsqueda de préstamos activos por usuario
CREATE INDEX idx_prestamos_user_estado ON prestamos (user_id, estado);