<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // 1. Destruir la tabla vieja (cuidado si hay datos en prod, pero en dev es seguro)
        Schema::dropIfExists('votos');

        // 2. Crear la nueva tabla de auditoría anónima
        Schema::create('registro_votantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleccion_id')->constrained('elecciones')->cascadeOnDelete();
            $table->foreignId('miembro_id')->constrained('miembros')->cascadeOnDelete();
            $table->string('puesto_votado');
            $table->enum('modalidad', ['digital', 'manual'])->default('digital');
            $table->foreignId('registrado_por_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Regla de negocio: Un miembro no puede votar dos veces para el mismo puesto en la misma elección
            $table->unique(['eleccion_id', 'miembro_id', 'puesto_votado'], 'unico_voto_por_puesto');
        });
    }

    public function down(): void {
        Schema::dropIfExists('registro_votantes');
        // (Omitimos la recreación de 'votos' por brevedad en el down)
    }
};
