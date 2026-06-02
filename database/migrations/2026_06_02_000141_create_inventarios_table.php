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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('cantidad')->default(1);
            $table->enum('estado', ['Nuevo', 'Bueno', 'Regular', 'Malo'])->default('Bueno');
            $table->string('ubicacion')->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('miembros')->nullOnDelete();
            $table->date('fecha_adquisicion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
