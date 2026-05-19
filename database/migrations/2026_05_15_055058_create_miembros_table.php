<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('miembros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('familia_id')->constrained('familias')->onDelete('cascade');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('dpi', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('ministerio')->nullable();
            $table->boolean('estado')->default(true);
            $table->string('foto')->nullable();
            $table->date('fecha_integracion')->nullable();
            $table->string('etapa_consolidacion')->default('Nuevo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miembros');
    }
};
