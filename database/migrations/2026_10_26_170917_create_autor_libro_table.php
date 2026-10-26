<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autor_libro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->constrained('autores')->restrictOnDelete();
            $table->foreignId('libro_id')->constrained('libros')->cascadeOnDelete();
            $table->unsignedSmallInteger('orden_autor')->default(1);
            $table->timestamps();

            $table->unique(['autor_id', 'libro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autor_libro');
    }
};