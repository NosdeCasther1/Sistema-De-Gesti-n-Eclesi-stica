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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->foreignId('evento_id')->nullable()->constrained('eventos')->onDelete('set null');
            $table->foreignId('celula_id')->nullable()->constrained('celulas')->onDelete('set null');
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
