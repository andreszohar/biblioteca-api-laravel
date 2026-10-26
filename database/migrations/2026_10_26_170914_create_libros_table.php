<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('isbn', 17)->unique();
            $table->unsignedSmallInteger('anio_publicacion');
            $table->unsignedSmallInteger('numero_paginas');
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('stock_disponible')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE libros ADD CONSTRAINT libros_paginas_check CHECK (numero_paginas > 0)');
        DB::statement('ALTER TABLE libros ADD CONSTRAINT libros_stock_check CHECK (stock_disponible >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};