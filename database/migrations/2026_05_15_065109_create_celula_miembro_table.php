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
        Schema::create('celula_miembro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('celula_id')->constrained('celulas')->onDelete('cascade');
            $table->foreignId('miembro_id')->constrained('miembros')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('celula_miembro');
    }
};
