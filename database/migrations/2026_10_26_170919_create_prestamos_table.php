<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained('libros')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion_estimada');
            $table->date('fecha_devolucion_real')->nullable();
            $table->string('estado', 10)->default('activo');
            $table->timestamps();

            $table->index(['user_id', 'estado']);
        });

        DB::statement("ALTER TABLE prestamos ADD CONSTRAINT prestamos_estado_check CHECK (estado IN ('activo', 'devuelto', 'vencido'))");
        DB::statement('ALTER TABLE prestamos ADD CONSTRAINT prestamos_fechas_check CHECK (fecha_devolucion_estimada >= fecha_prestamo)');
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};