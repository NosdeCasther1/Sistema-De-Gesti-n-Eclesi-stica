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
        Schema::create('celulas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('sector')->nullable();
            $table->foreignId('lider_id')->nullable()->constrained('miembros')->onDelete('set null');
            $table->string('direccion')->nullable();
            $table->string('dia_reunion')->nullable();
            $table->time('hora_reunion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('celulas');
    }
};
